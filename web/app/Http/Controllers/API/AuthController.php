<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Device;
use App\Customer;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    
    public function logout(Request $request)
    {

        // Input validation

        $validator = \Validator::make($request->all(), [
            'device_token' => ''
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'error' => [
                    'message' => 'Validation failed',
                    'fields' => $validator->messages(),
                    'code' => 412
                ]
            ], 412);
        }

        // Check if this device is registed in our system

        $device = Device::where('device_token', $request->device_token)->first();

        if (!$device) {
            
            // return \Response::json([
            //     'error' => [
            //         'message' => 'Token not found!',
            //         'code' => 404
            //     ]
            // ], 404);

        } else {

            // Check if this token is assigned to current user

            $user = \JWTAuth::parseToken()->toUser();

            if ($user->id != $device->user_id) {
                // you don't have permission, this token is assigned to another user
                return \Response::json([
                    'error' => [
                        'message' => 'You don\'t have permission to access this resource!',
                        'description' => 'This token is assigned to another account.',
                        'code' => 401
                    ]
                ], 401);
            }

            // Delete the token

            if ($device && !$device->delete()) {
                // something get wrong with the deletion
                return \Response::json([
                    'error' => [
                        'message' => 'Something went wrong, please try again.',
                        'description' => 'The subscription for notifications is still active!',
                        'code' => 500
                    ]
                ], 500);
            }

        }

        \JWTAuth::invalidate(\JWTAuth::getToken());

        // Success

        return \Response::json([
            'success' => 'You signed out successfully.'
        ], 200);

    }

	public function login(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
            'device_token' => '',
            'device_type' => ''
        ]);

        if ($validator->fails()) {
            return \Response::json([
                'error' => [
                    'message' => 'Validation failed',
                    'fields' => $validator->messages(),
                    'code' => 412
                ]
            ], 412);
        }

        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => ['message' => 'Invalid credentials', 'code' => 401]], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => ['message' => 'Could not create token', 'code' => 500]], 500);
        }

        $user = User::where('email', $request->email)->first();
        
        if ($user->type == 1) {
            $customer = $user->customer;
            $customer->solvent = true;
            if (!$customer->expmonth || !$customer->expyear || !$customer->tranzilatk) {
                $customer->solvent = false;
            }
        }

        if ($user->type == 2) {
            $user->shop;
        }

        // Device token

        \Log::info("DEVICE TOKEN @ LOGIN - ", ['email' => $user->email, 'device_token' => $request->device_token, 'device_type' => $request->device_type]);

        $device_token = trim($request->device_token);
        $device_type = trim($request->device_type);

        if ($device_token) {

            $device = Device::where('device_token', $device_token)->first();
            
            if ($device) {
                // This device is already registered in our system.
                if ($user->id != $device->user_id) {
                    // The device is registered from another account. We'll change the owner of this token.
                    $device->user_id = $user->id;
                    $device->save();
                } else {
                    // The device is registered from the same account. Do nothing.
                }
            } else {
                // This device in still not registered in out system. Let's do it.
                $device = Device::create([
                    'user_id' => $user->id,
                    'device_token' => $device_token,
                    'device_type' => $device_type
                ]);
            }
        
        }

        // all good so return the token

        return \Response::json([
            'token' => $token,
            'user' => $user
        ], 200);

    }

    public function messages()
    {
        return [
            'validation.unique' => 'A title is required',
            'body.required'  => 'A message is required',
        ];
    }

    public function register(Request $request) {
    	
        // Validation rules

        $validator = \Validator::make($request->all(), [
            
            // User fields
            'email' => 'required|email|unique:users',
            'password' => 'required|between:6,32',

            // Device token
            'device_token' => '',
            'device_type' => '',

            // Customer fields
            'first_name' => 'required|between:2,32',
            'last_name' => 'required|between:2,32',
            'phone' => 'required|unique:customers',
            'city' => 'required',
            'street' => 'required',
            'building' => 'required',
            'entrance' => '',
            'apartment' => '',
            'latitude' => '',
            'longitude' => '',

            // Credit card fields
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

        // Create user

        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $customer = Customer::create([
            
            // Customer fields
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'city' => $request->city,
            'street' => $request->street,
            'building' => $request->building,
            'entrance' => $request->entrance,
            'apartment' => $request->apartment,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            
        ]);

        $customer->email = $request->email;

        // Generate token @ 24.11.2016

        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => ['message' => 'Invalid credentials', 'code' => 401]], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => ['message' => 'Could not create token', 'code' => 500]], 500);
        }

        $customer->token = $token;

        // Register the device in the DB @ 24.11.2016

        $device_token = trim($request->device_token);
        $device_type = trim($request->device_type);

        if ($device_token) {

            $device = Device::where('device_token', $device_token)->first();
            
            if ($device) {
                // This device is already registered in our system.
                if ($user->id != $device->user_id) {
                    // The device is registered from another account. We'll change the owner of this token.
                    $device->user_id = $user->id;
                    $device->save();
                } else {
                    // The device is registered from the same account. Do nothing.
                }
            } else {
                // This device in still not registered in out system. Let's do it.
                $device = Device::create([
                    'user_id' => $user->id,
                    'device_token' => $device_token,
                    'device_type' => $device_type
                ]);
            }
        
        }

        \Log::info("DEVICE TOKEN @ REGISTER - ", ['email' => $user->email, 'device_token' => $request->device_token, 'device_type' => $request->device_type]);
        \Log::info("REGISTERED - ", [$customer]);

        return $customer;
        
    }

}
