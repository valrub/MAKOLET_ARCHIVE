<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Shop;

class ShopController extends Controller
{
    
	public function index(Request $request)
	{

        // Validation rules

        $validator = \Validator::make($request->all(), [
            'lat' => 'numeric|min:-90|max:90',
            'lng' => 'numeric|min:-180|max:180',
            'distance' => 'numeric|min:0'
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

        if ($request->lat && $request->lng) {
            
            $distance = 5;

            if ($request->distance) {
                $distance = $request->distance;
            }
            
            $shops = Shop::select('*', \DB::raw('3956 * 2 * ASIN(SQRT( POWER(SIN((' . $request->lat . ' -
            abs( 
            shops.latitude)) * pi()/180 / 2),2) + COS(' . $request->lat . ' * pi()/180 ) * COS( 
            abs
            (shops.latitude) *  pi()/180) * POWER(SIN((' . $request->lng . ' - shops.longitude) *  pi()/180 / 2), 2) )) * 1.61
            as distance'))
            ->having('distance', '<', $distance)
            ->orderBy('distance')
            ->paginate(10);

            foreach ($shops as $shop) {
                $shop->rating = round($shop->feedbacks->avg('score'));
            }

            return \Response::json($shops, 200);

        }

        $shops = Shop::paginate(10);

        foreach ($shops as $shop) {
            $shop->rating = round($shop->feedbacks->avg('score'));
        }

        return \Response::json($shops, 200);

	}

	public function show($id)
	{

		$shop = Shop::find($id);
        $user = \JWTAuth::parseToken()->toUser();

		if (!$shop) {
			return \Response::json([
				'error' => [
					'message' => 'Shop not found!',
					'code' => 404
				]
			], 404);
		}

        $shop->rating = round($shop->feedbacks->avg('score'));

        if ($user->id != $shop->user_id) {
            unset($shop->bank_name);
            unset($shop->bank_branch);
            unset($shop->bank_account_number);
        }

		return \Response::json([
			'data' => $shop
		], 200);

	}

	public function update(Request $request, $id)
	{

		$user = \JWTAuth::parseToken()->toUser();
		$shop = Shop::find($id);

		if (!$shop || $user->id != $shop->user_id) {
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

            // Shop fields
            'first_name' => 'required|between:2,32',
            'last_name' => 'required|between:2,32',
            'name' => 'required|between:2,32',
            'company_name' => 'required',
            'company_id' => '',
            'phone' => 'required',
            'mobile' => 'required',
            'city' => 'required',
            'street' => 'required',
            'building' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',

            'bank_account_number' => '',
            'bank_name' => '',
            'bank_branch' => ''

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

        $shop->first_name = $request->first_name;
        $shop->last_name = $request->last_name;
        $shop->name = $request->name;
        $shop->company_name = $request->company_name;
        $shop->company_id = $request->company_id;
        $shop->mobile = $request->mobile;
        $shop->phone = $request->phone;
        $shop->city = $request->city;
        $shop->street = $request->street;
        $shop->building = $request->building;
        $shop->latitude = $request->latitude ? $request->latitude : null;
        $shop->longitude = $request->longitude ? $request->longitude : null;
        $shop->bank_account_number = $request->bank_account_number ? $request->bank_account_number : null;
        $shop->bank_name = $request->bank_name ? $request->bank_name : null;
        $shop->bank_branch = $request->bank_branch ? $request->bank_branch : null;

        if ($shop->save()) {
        	return \Response::json([
        		'success' => 'The shop was updated successfully.',
        		'data' => $shop
        	], 200);
        }

	}

}
