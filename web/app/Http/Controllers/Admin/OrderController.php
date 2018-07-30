<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Order;
use App\OrderProposal;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $statuses = ['1', '2', '3', '4', '5', '6', '7', '8'];
        
        if ($request->get('status')) {
            $statuses = $request->get('status');
        }

        $orders = Order::whereIn('status', $statuses)->orderBy('id', 'desc')->paginate(20);
        $orders = Order::leftJoin('order_proposals', 'order_proposals.id', '=', 'orders.proposal_id')->select('orders.*', 'order_proposals.shop_notes', 'order_proposals.delivery_time', 'order_proposals.price')->orderBy('orders.id', 'desc')->paginate(20);

        // Prepare the data for the Excel export
        $request->session()->flash('extract_filename', 'Orders');
        $request->session()->flash('extract_excel', $orders);

        return view('admin.orders.orders')->with('orders', $orders);
    }

    public function extract(Request $request)
    {
        $orders = Order::leftJoin('order_proposals', 'order_proposals.id', '=', 'orders.proposal_id')->select('orders.*', 'order_proposals.shop_notes', 'order_proposals.delivery_time', 'order_proposals.price')->orderBy('orders.id', 'desc')->get();

        // Prepare the data for the Excel export
        $request->session()->flash('extract_filename', 'Orders');
        $request->session()->flash('extract_excel', $orders);
        return redirect('admin/extract');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        return view('admin.orders.order')->with('order', $order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order = Order::find($id);
        return view('admin.orders.edit')->with('order', $order);
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

        $allowed_statuses = [1, 2, 3, 4, 5, 6, 7, 8];        
        $order = Order::find($id);

        // Check if the selected status is allowed
        // Check if the selected status is different from the current
        if ($request->status && in_array($request->status, $allowed_statuses) && $order->status != $request->status) {
            $order->status = $request->status;
            $order->save();
        }

        // Check if 'proposals' is array
        if ($request->proposals && is_array($request->proposals)) {
            foreach ($request->proposals as $key => $value) {
                // Check if the selected status
                if (is_numeric($key) && in_array($value, $allowed_statuses)) {
                    $proposal = OrderProposal::find($key);
                    if ($proposal->status != $value) {
                        $proposal->status = $value;
                        $proposal->save();
                    }
                }
            }
        }

        return redirect()->route('admin.orders.show', $order);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
