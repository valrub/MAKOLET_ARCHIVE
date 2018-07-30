<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Device;
use App\Notification;

use PushNotification;

class NotificationController extends Controller
{
    
	public static function push($user_id, $msg, $data = [])
	{

		// Get the devices of the user
		$devices = Device::where('user_id', $user_id)->get();
		
		$devices_gcm = [];
		$devices_apns = [];

		// For each device of the user
		foreach ($devices as $device) {
			
			// Prepare the device information
			$device_type = strtolower($device->device_type);
			$device_token = $device->device_token;

			// Determine which application to call
			if ($device_type == 'gcm') {
				array_push($devices_gcm, PushNotification::Device($device_token));
			} else if ($device_type == 'apns') {
				array_push($devices_apns, PushNotification::Device($device_token));
			}

		}

		\Log::info("NOTIFICATIONS CONTROLLER - ANDROID START - " . count($devices_gcm) . " DEVICES");

		if (count($devices_gcm) > 0) {
			$devices_collection = PushNotification::DeviceCollection($devices_gcm);
			$message = PushNotification::message($msg . ' ' . $data['order_id'], ['title' => 'Makolet', 'data' => $data]);
			$collection = PushNotification::app('appNameAndroid');
			$collection->adapter->setAdapterParameters(['sslverifypeer' => false]);
			$collection->to($devices_collection)->send($message);
		}

		\Log::info("NOTIFICATIONS CONTROLLER - ANDROID END");
		\Log::info("NOTIFICATIONS CONTROLLER - IOS START - " . count($devices_apns) . " DEVICES");

		if (count($devices_apns) > 0) {
			$devices_collection = PushNotification::DeviceCollection($devices_apns);
			$message = PushNotification::message($msg . ' ' . $data['order_id'], ['title' => 'Makolet', 'data' => $data]);
			$collection = PushNotification::app('appNameIOS');
			$collection->to($devices_collection)->send($message);
		}

		\Log::info("NOTIFICATIONS CONTROLLER - IOS END");

		// Add the notification in the DB
		$notification = Notification::create([
			'user_id' => $user_id,
			'order_id' => $data['order_id'],
			'message' => $msg
		]);
		
	}

    public function get()
    {
    	if (!\Auth::check()) return redirect('/');
    	$notification = \Auth::user()->notifications->first();
    	return $notification;
    }

}
