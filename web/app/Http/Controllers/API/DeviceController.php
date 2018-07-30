<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Device;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class DeviceController extends Controller
{
    
    public function store(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required'
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

        $user = \JWTAuth::parseToken()->toUser();

        if (!$user) {
            return \Response::json([
                'error' => [
                    'message' => 'User not found!',
                    'code' => 404
                ]
            ], 404);
        }

        $device_token = trim($request->device_token);
        $device_type = trim($request->device_type);

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

        return \Response::json([
            'success' => 'The device was registered successfully.'
        ], 200);

    }

    public function destroy(Request $request)
    {

        // Input validation

        $validator = \Validator::make($request->all(), [
            'device_token' => 'required'
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
            
            return \Response::json([
                'error' => [
                    'message' => 'Token not found!',
                    'code' => 404
                ]
            ], 404);

        }

        // Check if this token belongs to the current user

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

        // Success

        return \Response::json([
            'success' => 'Device unregistered. You will no more receive notifications on your device.'
        ], 200);

    }

}
