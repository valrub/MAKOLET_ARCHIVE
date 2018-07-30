<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;

// Controllers
use App\Http\Controllers\NotificationController;

// Jobs
use App\Jobs\SendPushNotifications;

// Models
use App\Order;
use App\OrderItem;
use App\OrderProposal;
use App\Customer;
use App\Shop;

// Requests
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\CloseOrderRequest;

// TODO: Remove NotificationController

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!\Auth::check()) return redirect('/');

        if (\Auth::user()->type == 1) {

            // Customer
            $orders = \Auth::user()->customer->ordersSummary;
            return view('orders.orders')->with('orders', $orders);

        } else if (\Auth::user()->type == 2) {
            
            $status = Input::get('status');

            if ($status == '1') {
                $proposals = \Auth::user()->shop->proposals->where('status', (string)1); // REMOVE: (string)
            } else if ($status == '2') {
                $proposals = \Auth::user()->shop->proposals->where('status', (string)2); // REMOVE: (string)
            } else if ($status == '3') {
                $proposals = \Auth::user()->shop->proposals->where('status', (string)3); // REMOVE: (string)
            } else {
                $proposals = \Auth::user()->shop->proposals;
            }

            return view('orders.shop_orders')->with('proposals', $proposals);

        } else {
            return redirect('/');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if (\Auth::user()->type != 1) return redirect('/home');
        $customer = \Auth::user()->customer;

        if (!$customer) return redirect('/logout');

        // Check if the customer has credit card details
        if (!$customer->expmonth || !$customer->expyear || !$customer->tranzilatk) {
            if ($request->session()->get('just-registered')) {
                $request->session()->flash('alert-success', 'חשבונך נוצר בהצלחה. נא להזין כרטיס אשראי תקף.');
            } else {
                $request->session()->flash('alert-danger', 'נא להזין כרטיס אשראי תקף.');
            }
            return redirect('/profile');
        }

        // Check if there is another order in progres
        $lastOrder = $customer->orders->first();

        if ($lastOrder && $lastOrder->status <= 3) {
            return redirect('/orders/' . $lastOrder->id);
        }

        $shops = Shop::all();
        return view('orders.create')->with('shops', $shops)->with('customer', $customer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateOrderRequest $request)
    {

        if (\Auth::user()->type == 2) return redirect('/');

        // Create record for the order
        $order = Order::create([
            'customer_id' => \Auth::user()->customer->id,
            'customer_notes' => $request->notes,
            'city' => $request->city,
            'street' => $request->street,
            'building' => $request->building,
            'entrance' => $request->entrance,
            'apartment' => $request->apartment,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        // Create records for the goods
        foreach ($request->goods as $key => $val) {
            if ($val !== '' /* && $request->quantities[$key] >= 1 */) {
                $item = OrderItem::create([
                    'order_id' => $order->id,
                    'name' => $val
                    /* 'quantity' => $request->get('quantities')[$key] */
                ]);
            }
        }

        // Create records for the proposals
        foreach ($request->shops as $key => $val) {
            
            $proposal = OrderProposal::create([
                'order_id' => $order->id,
                'shop_id' => $val,
                'status' => 1
            ]);

            // Send notification to the shops
            $shop = Shop::find($val);
            $data = ['order_id' => $order->id];
            $this->dispatch(new SendPushNotifications($shop->user_id, 'התקבלה הזמנה חדשה להתייחסותך', $data));
            // NotificationController::push($shop->user_id, 'התקבלה הזמנה חדשה להתייחסותך', $data); // You received new order

        }
        
        return redirect('orders/' . $order->id . '#proposals');
        
    }

    /**
     * Display a summary of the closed orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {

        if (!\Auth::check()) return redirect('/');

        if (\Auth::user()->type == 1) {

            // Customer
            return redirect('orders');

        } else if (\Auth::user()->type == 2) {

            $proposals = \Auth::user()->shop->proposalsSummary;

            return view('orders.shop_summary')->with('proposals', $proposals);

        } else {
            return redirect('/');
        }
 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {

        // Check if authorized
        if (!\Auth::check()) return redirect('/');

        $order = Order::withTrashed()->where('id', $id)->first();

        // Check if there is order with this ID
        if (!$order) return redirect('/orders');

        // Check if there is notification about this order
        $notification = $order->notifications->where('user_id', (string)\Auth::user()->id)->first(); // (string) needed for production

        if ($notification) {
            $notification->delete();
        }

        // Check if the order is deleted
        if ($order->trashed()) {
            $request->session()->flash('alert-danger', 'ההזמנה בוטלה');
            return redirect('/orders');
        }

        if (\Auth::user()->type == 1) {

            // Check if the customer is owner of this order
            if (\Auth::user()->customer->id != $order->customer_id || $order->status > 6) return redirect('/orders');
            
            return view('orders.order')->with('order', $order);
        }

        if (\Auth::user()->type == 2) {

            $proposal = \Auth::user()->shop->proposalsAll->where('order_id', (string)$order->id)->first(); // (string) needed for production

            // Check if the shop has permission
            if (!count($proposal) || $proposal->status == 4 || $proposal->status == 7 || $proposal->status == 8) return redirect('/orders');

            return view('orders.shop_order')->with('proposal', $proposal);
        }
 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Close an order and add a price.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function close(CloseOrderRequest $request, $id)
    {

        // Check if authorized
        if (!\Auth::check()) return redirect('/');

        // Check if the user is shop
        if (\Auth::user()->type != 2) {
            return redirect('orders/' . $id);
        }

        $proposal = \Auth::user()->shop->proposalsAll->where('order_id', $id)->first();

        // Check if the status of the order allows this change
        if (!$proposal) {
            return redirect('orders/' . $id);
        }

        $order = $proposal->order;

        if ($order->status != 3 || $order->proposal_id != $proposal->id) {
            return redirect('orders/' . $id);
        }

        // Save the changes
        $order->status = 5;
        $order->save();

        $proposal->status = 5;
        $proposal->price = $request->price ? $request->price : null;
        $proposal->delivery_price = $request->delivery_price ? $request->delivery_price : null;
        $proposal->processed_at = date('Y-m-d H:i:s');
        $proposal->save();

        $order->proposal;

        // Send notification to the customer
        $data = ['order_id' => $order->id];
        $this->dispatch(new SendPushNotifications($order->customer->user_id, 'הזמנתך מתבצעת, מחיר עודכן', $data));
        // NotificationController::push($order->customer->user_id, 'הזמנתך מתבצעת, מחיר עודכן', $data);

        return redirect('orders/' . $id);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if (!\Auth::check()) return redirect('/'); // Not authenticated
        
        $user = \Auth::user();

        if ($user->type != 1) return redirect('/'); // The user is not a customer

        $order = Order::find($id);

        if (!$order) return redirect('/'); // Order not found
        if ($user->id != $order->customer->user_id) return redirect('/'); // The order does not belongs to the user
        if ($order->status == 7 && $order->cancelled_at) return redirect('/'); // The order is already cancelled
        if ($order->status >= 3) return redirect('/'); // Proposal for this order is accepted, could not cancel

        $date = new \DateTime();
        $order->status = 7;
        $order->cancelled_at = $date->format('Y-m-d H:i:s');

        if ($order->save()) {
            $request->session()->flash('alert-success', 'The order was cancelled successfully');
            return redirect()->route('orders.index'); // The order was cancelled successfully
        } else {
            return redirect()->route('orders.show', [$user]); // Something went wrong, please try again
        }
    }

    public function cron()
    {

        $now = new \DateTime();
        $now = $now->getTimestamp();

        $orders = Order::whereRaw('(status = 1 AND updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)) OR (status = 2 AND updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)) OR (status = 3 AND updated_at < DATE_SUB(NOW(), INTERVAL 3 HOUR)) OR (status = 5 AND updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY))')->get();

        foreach ($orders as $order) {

            $then = strtotime($order->updated_at);
            $diff = (int)(($now - $then) / 60);
            $changed = true;

            // 1. ORDER

            // Proposal is not recieved in 60 minutes
            if ($order->status == 1 && $diff >= 60) {
                $order->status = 7;
                $order->save();

            // Proposal is not accepted in 60 minutes
            } else if ($order->status == 2 && $diff >= 60) {
                $order->status = 7;
                $order->save();
            
            // Order is not closed by shop in 180 minutes after proposal is accepted
            } else if ($order->status == 3 && $diff >= 180) {
                $order->status = 8;
                $order->save();

            // Order is not paid in 31 days
            } else if ($order->status == 5 && $diff >= 44640) {
                $order->status = 8;
                $order->save();
            
            // It seems that query returned more results
            } else {
                $changed = false;
            }

            // 2. PROPOSALS

            if ($changed) {
                foreach ($order->proposals as $proposal) {

                    if ($proposal->status == 1 || $proposal->status == 2) {
                        $proposal->status = 7;
                        $proposal->save();
                    } else if ($proposal->status == 3 || $proposal->status == 5) {
                        $proposal->status = 8;
                        $proposal->save();
                    } else {
                        // We don't have to change it
                    }

                }
            }

        }

        return $orders;

    }

}
