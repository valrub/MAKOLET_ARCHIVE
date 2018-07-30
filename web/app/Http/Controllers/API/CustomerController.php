<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Customer;

class CustomerController extends Controller
{
    
    /**
     * Display the current customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function me()
	{

		$user = \JWTAuth::parseToken()->toUser();
		$customer = $user->customer;

        $customer->setHidden(['deleted_at', 'credit_card_number', 'valid_until_month', 'valid_until_year', 'security_code']);
		
		if (!$customer) {
			return \Response::json([
				'error' => [
					'message' => 'No customer is created for your account!',
					'code' => 404
				]
			], 404);
		}

        $customer->solvent = true;

        if (!$customer->expmonth || !$customer->expyear || !$customer->tranzilatk) {
            $customer->solvent = false;
        }

		return \Response::json([
			'data' => $customer
		], 200);

	}

	/**
     * Display the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show($customer_id)
	{

		$user = \JWTAuth::parseToken()->toUser();
		$customer = $user->customer;

		if (!$customer || $customer_id != $customer->id) {
			return \Response::json([
				'error' => [
					'message' => 'You don\'t have permission to access this resource!',
					'code' => 401
				]
			], 401);
		}

		return \Response::json([
			'data' => $customer
		], 200);

	}

    public function updateCard(Request $request, $customer_id)
    {

        $user = \JWTAuth::parseToken()->toUser();
        $customer = $user->customer;

        if (!$customer || $customer_id != $customer->id) {
            return \Response::json([
                'error' => [
                    'message' => 'You don\'t have permission to access this resource!',
                    'code' => 401
                ]
            ], 401);
        }

        // Validation rules

        $validator = \Validator::make($request->all(), [
            
            'credit_card_number' => '',
            'valid_until_month' => '',
            'valid_until_year' => '',
            'security_code' => ''

        ]);

        // Validation didn't pass

        if ($validator->fails()) {
            return \Response::json([
                'error' => [
                    'message' => 'Validation failed',
                    'fields' => $validator->messages(),
                    'code' => 412
                ]
            ], 412);
        }

        $customer->credit_card_number = $request->credit_card_number ? $request->credit_card_number : null;
        $customer->valid_until_month = $request->valid_until_month ? $request->valid_until_month : null;
        $customer->valid_until_year = $request->valid_until_year ? $request->valid_until_year : null;
        $customer->security_code = $request->security_code ? $request->security_code : null;

        if ($customer->save()) {
            return \Response::json([
                'success' => 'Your credit card was updated successfully.'
            ], 200);
        }
    }

	/**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, $customer_id)
	{

		$user = \JWTAuth::parseToken()->toUser();
		$customer = $user->customer;

		if (!$customer || $customer_id != $customer->id) {
			return \Response::json([
				'error' => [
					'message' => 'You don\'t have permission to access this resource!',
					'code' => 401
				]
			], 401);
		}

		// Validation rules

        $validator = \Validator::make($request->all(), [
            
            // User fields
            'password' => 'between:6,32',

            // Customer fields
            'first_name' => 'required|between:2,32',
            'last_name' => 'required|between:2,32',
            'phone' => 'required',
            'city' => 'required',
            'street' => 'required',
            'building' => 'required',
            'entrance' => '',
            'apartment' => '',
            'latitude' => '',
            'longitude' => ''

        ]);

        // Validation didn't pass

        if ($validator->fails()) {
            return \Response::json([
                'error' => [
                    'message' => 'Validation failed',
                    'fields' => $validator->messages(),
                    'code' => 412
                ]
            ], 412);
        }

        // Apply the changes

        if ($request->password) {
        	$user->password = bcrypt($request->password);
        	$user->save();
        }

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->phone = $request->phone;
        $customer->city = $request->city;
        $customer->street = $request->street;
        $customer->building = $request->building;

        $customer->entrance = $request->entrance ? $request->entrance : null;
        $customer->apartment = $request->apartment ? $request->apartment : null;
        $customer->latitude = $request->latitude ? $request->latitude : null;
        $customer->longitude = $request->longitude ? $request->longitude : null;

        if ($customer->save()) {
        	return \Response::json([
        		'success' => 'Your profile was updated successfully.',
        		'data' => $customer
        	], 200);
        }

	}

}
