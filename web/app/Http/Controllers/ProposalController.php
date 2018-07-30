<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\OrderProposal;

use App\Jobs\SendPushNotifications;

use App\Http\Requests\SendProposalRequest;
use App\Http\Requests\AcceptProposalRequest;

// TODO: Remove NotificationController

class ProposalController extends Controller
{
    
    public function propose(SendProposalRequest $request)
    {

        // Check if the user is logged in
        if (!\Auth::check()) return redirect('/');

        $proposal = OrderProposal::find($request->proposal);

        // Check if the user has permission
        if (\Auth::user()->shop->id != $proposal->shop_id || $proposal->status != 1 || $proposal->order->status >= 3) return redirect('/');

        $date = new \DateTime();

        // Update the proposal
        $proposal->proposed_at = $date->format('Y-m-d H:i:s');
        $proposal->shop_notes = $request->shop_notes;
        $proposal->delivery_time = $date->add(new \DateInterval('PT'.$request->delivery_time.'M'))->format('Y-m-d H:i:s');
        $proposal->status = 2;
        $proposal->save();

        // Update the order
        $order = $proposal->order;
        if ($order->status == 1) {
            $order->status = 2;
            $order->save();
        }

        // Send notification to the customer
        $data = ['order_id' => $order->id];
        $this->dispatch(new SendPushNotifications($order->customer->user_id, 'התקבלה הצעה חדשה', $data));
        // NotificationController::push($order->customer->user_id, 'התקבלה הצעה חדשה', $data); // You recieved a proposal from ' . $proposal->shop->name
        
        return redirect('/orders/' . $order->id);

    }

    public function accept(AcceptProposalRequest $request)
    {

    	$proposal = OrderProposal::find($request->proposal);

    	// Check if the proposal may be accepted or declined

    	if ($proposal->status != 2) {
    		return \Response::json([
    			'message' => 'This proposal cannot be accepted or declined.',
    			'code' => 400
    		], 400);
    	}

        // Check if user has permission to access this resource

        $user = \Auth::user();
        $customer = $proposal->order->customer;

        if ($user->id != $customer->user_id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
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

    	}

    }

}
