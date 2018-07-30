<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Controllers\NotificationController;

use App\Jobs\SendPushNotifications;

use App\Customer;
use App\Shop;
use App\Order;
use App\OrderItem;
use App\OrderProposal;

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
        
        $user = \JWTAuth::parseToken()->toUser();

        if ($user->type == 1) {
            return $this->byCustomerId($user->customer->id);
        }

        if ($user->type == 2) {
            return $this->byShopId($user->shop->id);
        }

    }

    public function byCustomerId($customer_id)
    {

        $user = \JWTAuth::parseToken()->toUser();

        $customer = Customer::find($customer_id);

        if (!$customer) {
            return \Response::json([
                'error' => [
                    'message' => 'Customer not found!',
                    'code' => 404
                ]
            ], 404);
        }

        if ($customer->user_id != $user->id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        $orders = Order::where('customer_id', $customer->id)->whereRaw('status BETWEEN 5 AND 6')->orderBy('id', 'desc')->paginate(10);

        foreach ($orders as $order) {
            $order->goods = Order::find($order->id)->goods;
            $order->proposal; // = Order::find($order->id)->proposals;
            $order->proposal->shop->setVisible(['name']);
        }

        return \Response::json(
            $orders
        , 200);

    }

    public function byShopId($shop_id)
    {
        $user = \JWTAuth::parseToken()->toUser();

        $shop = Shop::find($shop_id);

        if (!$shop) {
            return \Response::json([
                'error' => [
                    'message' => 'Shop not found!',
                    'code' => 404
                ]
            ], 404);
        }

        if ($shop->user_id != $user->id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        // $orders = Order::byShopId($shop_id)->paginate(10);

        $status = \Request::get('status');
        $statuses = ['1', '2', '3', '5', '6'];

        if (isset($status) && strlen($status) > 0 && !in_array($status, $statuses)) {
            return \Response::json([
                'error' => [
                    'message' => 'Unsupported status!',
                    'code' => 400
                ]
            ], 400);
        }

        if (is_numeric($status)) {
            $orders = Order::byShopId($shop_id)->whereRaw('orders.deleted_at IS NULL AND order_proposals.status = ' . $status)->paginate(50);
        } else {
            $orders = Order::byShopId($shop_id)->whereRaw('orders.deleted_at IS NULL AND order_proposals.status BETWEEN 1 AND 3 AND orders.status < 7')->paginate(50);
        }

        /*

        // START OF GROUPED ORDERS ================================================================================

        $orders_response = [];

        foreach ($orders as $order) {
            $date_format = date_format(new \DateTime($order->created_at), 'm.Y');
            if (!isset($grouped_orders[$date_format])) {
                $grouped_orders[$date_format] = [];
            }
            array_push($grouped_orders[$date_format], $order);
        }

        $orders_response['total'] = $orders->total();
        $orders_response['per_page'] = $orders->perPage();
        $orders_response['current_page'] = $orders->currentPage();
        $orders_response['last_page'] = $orders->lastPage();
        $orders_response['next_page_url'] = $orders->nextPageUrl();
        $orders_response['prev_page_url'] = $orders->previousPageUrl();
        $orders_response['data'] = $grouped_orders;

        return \Response::json($orders_response, 200);

        // END OF GROUPED ORDERS ==================================================================================

        */

        return \Response::json($orders, 200);

    }

    public function summary(Request $request) {

        $user = \JWTAuth::parseToken()->toUser();

        if ($user->type == 1) {
            return $this->customerSummary($request);
        }

        if ($user->type == 2) {
            return $this->shopSummary($request);
        }

    }

    public function customerSummary($request) {

        $user = \JWTAuth::parseToken()->toUser();
        $customer = Customer::find($user->customer->id);

        // Check if customer

        if (!$customer) {
            return \Response::json([
                'error' => [
                    'message' => 'Customer not found!',
                    'code' => 404
                ]
            ], 404);
        }

        if ($customer->user_id != $user->id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        // Deal with the GET parameters

        $page = $request->page;
        $order = $request->order;
        $status = $request->status;
        $statuses = ['5', '6'];

        if (!is_numeric($page)) {
            $page = 1;
        }

        if (!in_array($order, ['id', 'shop_id', 'price', 'created_at'])) {
            $order = 'id';
        }

        if (in_array($status, $statuses)) {
            $statuses = [$status];
        }

        // Get the list of order

        $orders = Order::summaryByCustomerId($customer->id, $page, $order)->get();

        // Group the orders by month

        $orders_response = [];
        $grouped_orders = [];

        foreach ($orders as $order) {
            if (in_array($order->status, $statuses)) {
                $date_format = date_format(new \DateTime($order->created_at), 'm.Y');
                if (!isset($grouped_orders[$date_format])) {
                    $grouped_orders[$date_format] = [];
                    $grouped_orders[$date_format]['orders'] = [];
                    $grouped_orders[$date_format]['total_price'] = 0;
                }
                array_push($grouped_orders[$date_format]['orders'], $order);
                $grouped_orders[$date_format]['total_price'] += ($order->price + $order->delivery_price);
            }
        }

        /*
        $orders_response['total'] = $orders->total();
        $orders_response['per_page'] = $orders->perPage();
        $orders_response['current_page'] = $orders->currentPage();
        $orders_response['last_page'] = $orders->lastPage();
        $orders_response['next_page_url'] = $orders->nextPageUrl();
        $orders_response['prev_page_url'] = $orders->previousPageUrl();
        $orders_response['data'] = $grouped_orders;
        */

        krsort($grouped_orders);

        return \Response::json([
            'data' => $grouped_orders
        ], 200);

    }

    public function shopSummary($request) {

        $user = \JWTAuth::parseToken()->toUser();
        $shop = Shop::find($user->shop->id);

        // Check if shop

        if (!$shop) {
            return \Response::json([
                'error' => [
                    'message' => 'Shop not found!',
                    'code' => 404
                ]
            ], 404);
        }

        if ($shop->user_id != $user->id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        // Deal with the GET parameters

        $page = $request->page;
        $order = $request->order;
        $status = $request->status;
        $statuses = ['5', '6'];

        if (!is_numeric($page)) {
            $page = 1;
        }

        if (!in_array($order, ['id', 'customer_id', 'price', 'created_at'])) {
            $order = 'id';
        }

        if (in_array($status, $statuses)) {
            $statuses = [$status];
        }

        // Get the list of order

        $orders = Order::summaryByShopId($shop->id, $page, $order)->get();

        // Group the orders by month

        $orders_response = [];
        $grouped_orders = [];

        foreach ($orders as $order) {
            if (in_array($order->status, $statuses)) {
                $date_format = date_format(new \DateTime($order->created_at), 'm.Y');
                if (!isset($grouped_orders[$date_format])) {
                    $grouped_orders[$date_format] = [];
                    $grouped_orders[$date_format]['orders'] = [];
                    $grouped_orders[$date_format]['total_price'] = 0;
                }
                array_push($grouped_orders[$date_format]['orders'], $order);
                $grouped_orders[$date_format]['total_price'] += ($order->price + $order->delivery_price);
            }
        }

        /*
        $orders_response['total'] = $orders->total();
        $orders_response['per_page'] = $orders->perPage();
        $orders_response['current_page'] = $orders->currentPage();
        $orders_response['last_page'] = $orders->lastPage();
        $orders_response['next_page_url'] = $orders->nextPageUrl();
        $orders_response['prev_page_url'] = $orders->previousPageUrl();
        $orders_response['data'] = $grouped_orders;
        */

        return \Response::json([
            'data' => $grouped_orders
        ], 200);

    }

    public function last()
    {

        $user = \JWTAuth::parseToken()->toUser();

        if ($user->type == 1) {

            $order = $user->customer->orders->first();

            if ($order) {
                $order->goods;
                $order->proposals;
            } else {
                $order = [];
            }

        }

        if ($user->type == 2) {

            $order = Order::byShopId($user->shop->id)->first();

            if (!$order) {
                $order = [];
            }

        }

        return \Response::json([
            'data' => $order
        ], 200);

    }

    /**
     * Close the order and add price.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function close(Request $request)
    {
        
        // Validate the inputs
        $validator = \Validator::make($request->all(), [
            'order' => 'required|exists:orders,id',
            'price' => 'required|regex:/^\d*(\.\d{2})?$/',
            'delivery_price' => 'regex:/^\d*(\.\d{2})?$/'
        ]);

        // Check for errors
        if ($validator->fails()) {
            return \Response::json([
                'error' => [
                    'message' => 'Validation failed',
                    'code' => 400,
                    'fields' => $validator->errors()
                ]
            ], 400);
        }

        $user = \JWTAuth::parseToken()->toUser();

        // Check if the user is shop
        if ($user->type != 2) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'description' => 'Only shops can perform this action.',
                    'code' => 401
                ]
            ], 401);
        }

        $proposal = $user->shop->proposals->where('order_id', (string)$request->order)->first();

        // Check if the shop has permission to access this order
        if (!$proposal) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'description' => 'No proposal was found by the user for this order.',
                    'code' => 401
                ]
            ], 401);
        }

        $order = $proposal->order;

        // Check if the status of the order allows this change
        if ($order->status != 3) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to perform this action!',
                    'description' => 'The status of the order should be 3. The order status is ' . $order->status . '.',
                    'code' => 401
                ]
            ], 401);
        }
        
        // Check if the shop owns the accepted order
        if ($order->proposal_id != $proposal->id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'description' => 'You are not owner of the accepted proposal.',
                    'code' => 401
                ]
            ], 401);
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

        return \Response::json([
            'success' => 'The order was updated successfully.',
            'data' => $order
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $requestJson = $request->json();

        // Check if the user is customer
        $user = \JWTAuth::parseToken()->toUser();
        if ($user->type != 1) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to perform this action!',
                    'code' => 401
                ]
            ]);
        }

        // Validate the inputs
        $validator = \Validator::make($requestJson->all(), [
            'shops' => 'required',
            'shops.0' => 'required|exists:shops,id',
            'shops.*' => 'exists:shops,id',
            'notes' => 'max:255',
            'goods.0' => 'required',
            // 'quantities.0' => 'required',
            'city' => 'required',
            'street' => 'required',
            'building' => 'required',
            'entrance' => '',
            'apartment' => '',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);
        
        // Check for errors
        if ($validator->fails()) {
            return \Response::json([
                'error' => [
                    'message' => 'Validation failed',
                    'code' => 400,
                    'fields' => $validator->errors()
                ]
            ], 400);
        }

        // Create record for the order
        $order = Order::create([
            'customer_id' => \Auth::user()->customer->id,
            'customer_notes' => $requestJson->get('notes'),
            'city' => $requestJson->get('city'),
            'street' => $requestJson->get('street'),
            'building' => $requestJson->get('building'),
            'entrance' => $requestJson->get('entrance'),
            'apartment' => $requestJson->get('apartment'),
            'latitude' => $requestJson->get('latitude'),
            'longitude' => $requestJson->get('longitude')
        ]);

        // Create records for the goods
        foreach ($requestJson->get('goods') as $key => $val) {
            if ($val !== '' /* && $requestJson->get('quantities')[$key] >= 1 */ ) {
                $item = OrderItem::create([
                    'order_id' => $order->id,
                    'name' => $val
                    /* 'quantity' => $requestJson->get('quantities')[$key] */
                ]);
            }
        }

        // Create records for the proposals
        foreach ($requestJson->get('shops') as $key => $val) {
            
            $proposal = OrderProposal::create([
                'order_id' => $order->id,
                'shop_id' => $val,
                'status' => 1
            ]);

            // Send notification to the shops
            $shop = Shop::find($val);
            $data = ['order_id' => $order->id];
            $this->dispatch(new SendPushNotifications($shop->user_id, 'התקבלה הזמנה חדשה להתייחסותך', $data));
            // NotificationController::push($shop->user_id, 'התקבלה הזמנה חדשה להתייחסותך', $data); // You recieved new order
            
        }

        $order->goods;
        $order->proposals;

        return \Response::json([
            'success' => 'The order was created successfully.',
            'data' => $order
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = \JWTAuth::parseToken()->toUser();
        $order = Order::find($id);

        if (!$order) {
            return \Response::json([
                'error' => [
                    'message' => 'Order not found!',
                    'code' => 404
                ]
            ], 404);
        }

        // Shop

        if ($user->type == 2) {

            $proposal = $user->shop->proposalsAll()->where('order_id', $id)->first(); // REMOVE: (string)

            // Check if the shop has permission to access this order
            if (!$proposal) {
                return \Response::json([
                    'error' => [
                        'message' => 'You don\'t have permission to access this resource!',
                        'description' => 'No proposal was found by the user for this order.',
                        'code' => 401
                    ]
                ], 401);
            }

            // Check if there is notification about this order
            $notification = $order->notifications->where('user_id', (string)$user->id)->first(); // (string) for production

            if ($notification) {
                $notification->delete();
            }

            // Check if the proposal is declined
            if ($proposal->status == 4) {
                return \Response::json([
                    'error' => [
                        'message' => 'You don\'t have permission to access this resource!',
                        'description' => 'Your proposal for this order was declined by the customer.',
                        'code' => 401
                    ]
                ], 401);
            }

            $order = $proposal->order;
            $order->goods;
            $order->customer->setVisible(['first_name', 'last_name', 'phone']);
            if (count($order->feedback) > 0) {
                $feedback = true;
            } else {
                $feedback = false;
            }

            unset($proposal->order);
            unset($order->feedback);

            $order->feedback = $feedback;
            $order->proposal = $proposal;

            return \Response::json([
                'data' => $order
            ], 200);

        }

        // Customer

        if ($order->customer_id != $user->customer->id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        // Check if there is notification about this order
        $notification = $order->notifications->where('user_id', $user->id)->first();

        if ($notification) {
            $notification->delete();
        }

        $order->goods;
        $order->proposals;
        
        if (count($order->feedback) > 0) {
            $feedback = true;
        } else {
            $feedback = false;
        }
        unset($order->feedback);
        $order->feedback = $feedback;

        foreach ($order->proposals as $proposal) {
            $proposal->shop->setVisible(['name']);
        }

        return \Response::json([
            'data' => $order
        ], 200);
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
    public function destroy($id)
    {
        $user = \JWTAuth::parseToken()->toUser();
        $order = Order::find($id);
        
        if ($user->type != 1) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        if (!$order) {
            return \Response::json([
                'error' => [
                    'message' => 'Order not found!',
                    'code' => 404
                ]
            ], 404);
        }

        if ($user->id != $order->customer->user_id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        if ($order->status == 7 && $order->cancelled_at) {
            return \Response::json([
                'error' => [
                    'message' => 'The order is already cancelled!',
                    'code' => 400
                ]
            ], 400);
        }

        if ($order->status >= 3) {
            return \Response::json([
                'error' => [
                    'message' => 'Proposal for this order is accepted already!',
                    'code' => 400
                ]
            ], 400);
        }

        $date = new \DateTime();
        $order->status = 7;
        $order->cancelled_at = $date->format('Y-m-d H:i:s');

        if ($order->save()) {
            return \Response::json([
                'success' => 'The order was cancelled successfully.'
            ], 200);
        } else {
            return \Response::json([
                'error' => [
                    'message' => 'Something went wrong, please try again.',
                    'code' => 500
                ]
            ], 500);
        }

    }

    private function orderByCustomer($a, $b)
    {
        return strcmp($a->customer_id, $b->customer_id);
    }
}
