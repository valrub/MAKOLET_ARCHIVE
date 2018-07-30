<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Controllers\NotificationController;

use App\User;
use App\Device;
use Excel;
use Session;

class AdminController extends Controller
{
    
	public function index()
	{
		return view('admin.index');
	}

    public function excel()
    {

        Excel::create('Filename', function($excel) {

            $excel->sheet('Sheetname', function($sheet) {

                $sheet->with(User::all());

            });
            
        })->export('xlsx');

    }

    public static function extract()
    {

        $session_data = Session::get('extract_excel');

        if (method_exists($session_data, 'total')) {
            
            $data = Collection::make($session_data->items());
            Session::flash('extract_excel', $data);

        } else {

            // Do nothing

        }

        // Generate the Excel file
        Excel::create(Session::get('extract_filename') . date(' - Y.m.d - U'), function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $sheet->with(Session::get('extract_excel'));
            });
        })->export('xlsx');

    }

	public function push(Request $request)
	{

        $user_id = $request->get('user');

        if (!$user_id) {
            return 'Please provide ID of a user in the URL - ?user=[ID]';
        }

        $user = User::find($user_id);

        if (!$user) {
            return 'User with ID = ' . $user_id . ' not found.';
        }

        $devices = Device::where('user_id', $user_id)->get();

        if (count($devices) == 0) {
            return 'No devices are registered in the system for user with email ' . $user->email;
        }

		NotificationController::push($user_id, 'THIS IS TEST');

        return count($devices) . ' notification(s) sent to user with email ' . $user->email;

	}

}
