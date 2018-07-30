<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Order;
use App\Feedback;

use App\Http\Requests\CreateFeedbackRequest;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(CreateFeedbackRequest $request)
    {
        
        $order = Order::find($request->order);

        // Check if the user is owner of the order
        if (\Auth::id() != $order->customer->user_id) return redirect()->route('orders.show', $order);

        $feedback = Feedback::where('order_id', $request->order)->first();

        // Check if there is a feedback about this order
        if (count($feedback)) return 'Feedback is already sent for this order.'; // TODO

        $feedback = Feedback::create([
            'customer_id' => $order->customer->id,
            'shop_id' => $order->proposal->shop_id,
            'order_id' => $order->id,
            'comment' => $request->comment,
            'score' => $request->rating
        ]);

        return redirect()->route('orders.show', $order);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }
}
