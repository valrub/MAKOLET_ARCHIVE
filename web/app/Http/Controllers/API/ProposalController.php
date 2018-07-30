<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Controllers\NotificationController;

use App\OrderProposal;

use App\Jobs\SendPushNotifications;

use App\Http\Requests\SendProposalRequest;
use App\Http\Requests\AcceptProposalRequest;

// TODO: Remove NotificationController

class ProposalController extends Controller
{
    
    public function propose(Request $request)
    {

        // Validate the inputs
        $validator = \Validator::make($request->all(), [
            'proposal' => 'required|exists:order_proposals,id',
            'delivery_time' => 'required|numeric|min:1|max:999',
            'shop_notes' => ''
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

        $proposal = OrderProposal::find($request->proposal);
        $user = \JWTAuth::parseToken()->toUser();

        // Check if the user has permission
        if ($user->type != 2 || $user->shop->id != $proposal->shop_id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        $order = $proposal->order;

        // Check if the order is 7-CANCELLED by the user (cancelled_at)
        if ($order->status == 7 && $order->cancelled_at) {
            return \Response::json([
                'error' => [
                    'message' => 'The order is cancelled by the user!',
                    'code' => 400
                ]
            ], 400);
        }

        // Check if the order is 1-NEW or 2-PROPOSED
        else if ($order->status >= 3) {
            return \Response::json([
                'error' => [
                    'message' => 'A proposal is already accepted for this order!',
                    'code' => 400
                ]
            ], 400);
        }

        // Check if the proposal is 1-NEW
        else if ($order->status != 1) {
            return \Response::json([
                'error' => [
                    'message' => 'This proposal is already sent!',
                    'code' => 400
                ]
            ], 400);
        }

        $date = new \DateTime();

        // Update the proposal
        $proposal->proposed_at = $date->format('Y-m-d H:i:s');
        $proposal->shop_notes = $request->shop_notes;
        $proposal->delivery_time = $date->add(new \DateInterval('PT'.$request->delivery_time.'M'))->format('Y-m-d H:i:s');
        $proposal->status = 2;
        $proposal->save();

        // Update the order
        if ($order->status == 1) {
            $order->status = 2;
            $order->save();
        }

        // Send notification to the customer
        $data = ['order_id' => $order->id];
        $this->dispatch(new SendPushNotifications($order->customer->user_id, 'התקבלה הצעה חדשה', $data));
        // NotificationController::push($order->customer->user_id, 'התקבלה הצעה חדשה', $data);

        return \Response::json([
            'success' => 'The proposal was sent.',
            'data' => $order
        ], 200);

    }

	public function accept(AcceptProposalRequest $request)
    {

    	$proposal = OrderProposal::find($request->proposal);

    	if (!$proposal) {
    		return \Response::json([
    			'error' => [
    				'message' => 'Proposal not found.',
    				'code' => 404
    			]
    		], 404);
    	}

    	// Check if user has permission to access this resource

    	$user = \JWTAuth::parseToken()->toUser();
    	$customer = $proposal->order->customer;

    	if ($user->id != $customer->user_id) {
    		return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
    	}

    	// Check if the proposal may be accepted or declined

    	if ($proposal->status != 2) {
    		return \Response::json([
    			'message' => 'This proposal cannot be accepted or declined.',
    			'code' => 400
    		], 400);
    	}
        
    	// Decline proposal

    	if ($request->accept == 0) {
    		
    		// Change the status of the proposal

    		$proposal->status = 4;
	    	$proposal->declined_at = date('Y-m-d H:i:s');
	    	$proposal->save();

            // Send notification to the customer
            $data = ['order_id' => $proposal->order_id];
            $this->dispatch(new SendPushNotifications($proposal->shop->user_id, 'הצעתך נדחתה', $data));
            // NotificationController::push($proposal->shop->user_id, 'הצעתך נדחתה', $data);

	    	return \Response::json([
	    		'success' => 'The proposal was declined.'
	    	], 200);

	    	// TODO :: Check if all orders are declined, if yes - decline the order

    	}

    	// Accept proposal

    	if ($request->accept == 1) {
    		
			$order = $proposal->order;

			// Check if the order has the required status

	    	if ($order->status != 2) {
	    		return \Response::json([
	    			'message' => 'Another proposal is already accepted for this order.',
	    			'code' => 400
	    		], 400);
	    	}

	    	// Change the status of the proposal

	    	$proposal->status = 3;
	    	$proposal->accepted_at = date('Y-m-d H:i:s');
	    	$proposal->save();

            // Change the status of all other proposals

            foreach ($order->proposals as $prop) {

                if ($prop->id != $proposal->id) {
                    $prop->status = 4;
                    $prop->save();
                }

            }

	    	// Change the status of the order

	    	$order->status = 3;
            $order->proposal_id = $proposal->id;
	    	$order->save();

            // Send notification to the customer
            $data = ['order_id' => $order->id];
            $this->dispatch(new SendPushNotifications($proposal->shop->user_id, 'הצעתך התקבלה', $data));
            // NotificationController::push($proposal->shop->user_id, 'הצעתך התקבלה', $data);

	    	return \Response::json([
	    		'success' => 'The proposal was accepted.'
	    	], 200);

    	}

    }

}
