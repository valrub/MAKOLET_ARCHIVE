<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Order;
use App\Feedback;

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
    public function store(Request $request)
    {

        // Validate the inputs
        $validator = \Validator::make($request->all(), [
            'order' => 'required|exists:orders,id',
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'required|max:255'
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
        $order = Order::find($request->order);

        // Check if the user is owner of the order
        if ($user->id != $order->customer->user_id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        };

        // Check if the status of the order allows to send a feedback
        if ($order->status < 3) {
            return \Response::json([
                'error' => [
                    'message' => 'You cannot add a feedback for this order yet!',
                    'code' => 400
                ]
            ], 400);
        };

        $feedback = Feedback::where('order_id', $request->order)->first();

        // Check if there is a feedback about this order
        if (count($feedback)) {
            return \Response::json([
                'error' => [
                    'message' => 'You already added a feedback for this order.',
                    'code' => 400
                ]
            ], 400);
        }

        $feedback = Feedback::create([
            'customer_id' => $order->customer->id,
            'shop_id' => $order->proposal->shop_id,
            'order_id' => $order->id,
            'comment' => $request->comment,
            'score' => $request->rating
        ]);

        return \Response::json([
            'success' => 'The feedback was added successfully.',
            'data' => $feedback
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
