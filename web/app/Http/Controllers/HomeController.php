<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Shop;
use App\Customer;
use App\Payment;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
        $shops = Shop::all();
        return view('home')->with('shops', $shops);
    }

    public function map()
    {
        if (!\Auth::check()) return redirect('/');
        $customer = \Auth::user()->customer;
        $shops = Shop::all();
        return view('shops.map')->with('shops', $shops)->with('customer', $customer);
    }

    public function join()
    {
        return view('join');
    }

    public function joinPost(Request $request)
    {
        
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return redirect('join')->withErrors($validator)->withInput();
        }

        \Mail::send('emails.bussines_owner', ['request' => $request], function ($message) {
            $message->subject('מכלת - בעל עסק? הצטרף');
            $message->to('amsn2001@gmail.com');
        });

        $request->session()->flash('alert-success', 'בקשתך נשלחה בהצלחה!');

        return redirect('join');

    }

    public function contactUs() {
        return view('contact');
    }

    public function contactUsPost(Request $request) {

        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('contact-us')->withErrors($validator)->withInput();
        }

        \Mail::send('emails.contact_us', ['request' => $request], function ($message) {
            $message->subject('מכלת - צור קשר');
            $message->to('amsn2001@gmail.com');
        });

        $request->session()->flash('alert-success', 'בקשתך נשלחה בהצלחה!');

        return redirect('contact-us');

    }

    public function termsOfUse() {
        return view('terms');
    }

    public function welcome()
    {
        return view('welcome');
    }

    public function payment() {

        // Get all not paid orders (status = 5)
        $orders = \DB::table('order_proposals')
            ->join('orders', 'orders.id', '=', 'order_proposals.order_id')
            ->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->join('users', 'users.id', '=', 'customers.user_id')
            ->join('shops', 'shops.id', '=', 'order_proposals.shop_id')
            ->select('orders.id', 'orders.customer_id', 'users.email', 'orders.proposal_id', 'price', 'delivery_price', 'orders.created_at', 'order_proposals.processed_at', 'order_proposals.delivery_time', 'customers.first_name', 'customers.last_name', 'customers.expmonth', 'customers.expyear', 'customers.tranzilatk', 'shops.name')
            ->where('orders.status', '=', 5)
            ->where('order_proposals.status', '=', 5)
            ->get();

        if (!$orders) {
            return 'All orders are paid.';
        }

        $customers = [];

        // Group the orders by customer
        foreach ($orders as $order) {

            if (!isset($customers[$order->customer_id])) {
                $customers[$order->customer_id]['total'] = floatval($order->price + $order->delivery_price);
                $customers[$order->customer_id]['name'] = $order->first_name . ' ' . $order->last_name;
                $customers[$order->customer_id]['email'] = $order->email;
                $customers[$order->customer_id]['id'] = $order->customer_id;
                $customers[$order->customer_id]['tranzilatk'] = $order->tranzilatk;
                $customers[$order->customer_id]['expdate'] = $order->expmonth . $order->expyear;
            } else {
                $customers[$order->customer_id]['total'] += ($order->price + $order->delivery_price);
            }

            $customers[$order->customer_id]['orders'][$order->id] = $order;

        }

        // Sort the list by customer ID
        ksort($customers);

        // Process the orders of each customer
        foreach ($customers as $customer_id => $customer) {

            if (!$customer['expdate'] || !$customer['tranzilatk']) {

                echo 'ERROR: No payment details for ' . $customer['email'] . '. <br><br>';
                continue;

            } else {

                echo 'PAYMENT: ' . $customer['email'] . ' - <b>' . $customer['total'] . ' NIS</b><br><br>';
                continue;

            }

            // Tranzila details
            $total = $customer['total'];
            $name = $customer['name'];
            $expdate = $customer['expdate'];
            $token = $customer['tranzilatk'];

            // Email details
            $ordersCount = count($customer['orders']);
            $email = $customer['email'];

            /////////////////////////////////////////////////////////

            $data = array(
                'supplier' => 'amsn2001', // 'amsntest',
                'sum' => $total,
                'currency' => 1, //NIS
                'expdate' => $expdate,
                'TranzilaPW' =>  '4lEuTQ', // 'mRyqlc',
                'TranzilaTK' => $token,
                'tranmode' => 'A',
                'email' => $email,
                'contact' => $name,
                'pdesc' => 'Product Description',
            );

            $resp = $this->_curl($data);

            var_dump($resp);

            if (!isset($resp['Response'])) {
                break;
            }

            /////////////////////
            /////////////////////
            /////////////////////
            /////////////////////

            $respCode = $resp['Response'];
            if ($respCode != '000') {
                //ERROR, tranzila won't send information
                die('ERROR');
            } else {
                echo 'CODE IS 000';
            }

            $ref = $resp['Tempref']; //Tranzila reference, probably worth to save it for any issue to come

            $payment = Payment::create([
                'customer_id' => $customer['id'],
                'response' => $resp['Response'],
                'tempref' => $resp['Tempref'],
                'sum' => $resp['sum']
            ]);

            echo '<br> Payment ID: ' . $payment->id . ' <br>';

            ////////////////////////////////////////////////////////

            var_dump($resp);

        }

    }

    private function _curl($data) {
    
        $headers = array(
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        );
    
        $poststring = array();
    
        foreach ($data as $k => $v) $poststring[] = $k . '=' . $v;
        $poststring = implode('&', $poststring);

        $cr = curl_init();
        curl_setopt($cr, CURLOPT_URL,  'https://secure5.tranzila.com/cgi-bin/tranzila71u.cgi'); // curl_setopt($cr, CURLOPT_URL,  'https://secure5.tranzila.com/cgi-bin/tranzila31tk.cgi');
        curl_setopt($cr, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($cr, CURLOPT_TIMEOUT,        10);
        curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cr, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cr, CURLOPT_POST, true);
        curl_setopt($cr, CURLOPT_POSTFIELDS, $poststring);
        curl_setopt($cr, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($cr);
        $error = curl_error($cr);
        curl_close($cr);

        if(!empty($error)) die($error);
        $resp = array();
        parse_str($result, $resp);
        return $resp;
    
    }

    public function paymentRegister(Request $request) {

        if ($request->get('ok') != 'true') {
            
            $respCode = $request['Response'];

            switch($respCode) {

                case '004':
                    $err = 'Credit card company error'; //סירוב חברת האשראי
                    
                case '061':
                    $err = 'Invalid card';
                    break;
                
                case '039':
                    $err = 'Invalid card'; //ספרת ביקורת לא תקינה
                    break;

                case '500':
                    $err = $resp['error'];
                    break;
                
                default:
                    $err = 'Payment error';

            }

            echo 'ERROR: ' . $err;

        } else {

            $validator = \Validator::make($request->all(), [
                'expmonth' => 'required',
                'expyear' => 'required',
                'TranzilaTK' => 'required',
                'customer' => 'required|exists:customers,id',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $customer = Customer::find($request['customer']);
            $customer->expmonth = $request['expmonth'];
            $customer->expyear = $request['expyear'];
            $customer->tranzilatk = $request['TranzilaTK'];

            if ($customer->save()) {
                return 'Your credit card information was registered!';
            } else {
                return 'Error occured. Please try again.';
            }

        }

        // echo '<hr>';

        // foreach ($request->all() as $key => $value) {

        //     echo $key . ' => ' . $value . '<br>';

        // }

        //additional data from $_POST:
        //credit card data:
        //expmonth [example: 07]
        //expyear [example: 17]
        //last 4 digits of a number
        //user ID [תעודת זהות]
        //any other parameter you've passed [in this example it's "data" with a value "xxx"
        //Tranzila's reference number [example: 01340001]

        //please note: expmonth and expyear must be saved in order to charge the token; can be 2 different field or a single one in format "$expmonth$expyear" e.g. "0117"

        //also, for successful transfer:
        //ConfirmationCode [for error, it also exists with a value of 0000000]
        //TranzilaTK - the token

    }

    public function language($lang = 'iw') {

        if ($lang === 'en' || $lang === 'iw') {
            \Session::put('lang', $lang);
        }

        return \Redirect::back();
    }

    public function performance() {

        if (!\Auth::check()) return redirect('/login');
        return view('performance');

    }
}
