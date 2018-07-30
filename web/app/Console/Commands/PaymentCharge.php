<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\NotificationController;

use App\Order;
use App\OrderProposal;
use App\Payment;

class PaymentCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly payment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
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

            // Check if the user has payment details

            if (!$customer['expdate'] || !$customer['tranzilatk']) {

                \Log::info("PAYMENT UNSUCCESSFUL - Customer: " . $customer['email'] . ", Reason: Payment details not provided.");
                continue;

            }

            // Prepare the transaction data

            // Tranzila details
            $total = $customer['total'];
            $name = $customer['name'];
            $expdate = $customer['expdate'];
            $token = $customer['tranzilatk'];

            // Email details
            $ordersCount = count($customer['orders']);
            $email = $customer['email'];

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

            // Send the charge request

            $resp = $this->_curl($data);

            // Check if there is a proper response

            if (!isset($resp['Response'])) {
                \Log::info("PAYMENT UNSUCCESSFUL - Customer: " . $email . ", Reason: No response from Tranzila.");
                continue;
            }

            // Check if the response code is 000

            $respCode = $resp['Response'];

            if ($respCode != '000') {
                \Log::info("PAYMENT UNSUCCESSFUL - Customer: " . $email . ", Reason: Transaction refused by Tranzila with code " . $respCode . ".");
                continue;
            }

            $ref = $resp['Tempref'];

            // Add the payment to the DB

            $payment = Payment::create([
                'customer_id' => $customer['id'],
                'response' => $resp['Response'],
                'tempref' => $resp['Tempref'],
                'sum' => $resp['sum']
            ]);
            
            // Send email

            $sent = \Mail::send('emails.monthly_charge', ['customer' => $customer, 'payment' => $payment], function ($message) use ($email) {
                $message->subject('מכלת - חשבונית');
                $message->to($email);
            });
            
            // Change the status of the orders

            foreach ($customer['orders'] as $order_id => $order) {
                
                // Change the status of the order

                $order = Order::find($order_id);
                $order->status = 6;
                $order->payment_id = $payment->id;
                $order->save();

                // Change the status of the proposal

                $proposal = $order->proposal;
                $proposal->status = 6;
                $proposal->paid_at = date('Y-m-d H:i:s');
                $proposal->save();

            }

            \Log::info("PAYMENT SUCCESSFUL - Customer: " . $email . ", Total: " . $total . " NIS.");

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

}
