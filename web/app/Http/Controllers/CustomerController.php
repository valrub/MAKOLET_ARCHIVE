<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!\Auth::check()) return redirect('/');
        $customer = \Auth::user()->customer;
        return view('customers.edit')->with('customer', $customer);
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
        //
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
    public function update(UpdateCustomerRequest $request)
    {
        if (!\Auth::check()) return redirect('/');
        
        $user = \Auth::user();
        $customer = $user->customer;

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

        $customer->credit_card_number = $request->credit_card_number ? $request->credit_card_number : null;
        $customer->valid_until_month = $request->valid_until_month ? $request->valid_until_month : null;
        $customer->valid_until_year = $request->valid_until_year ? $request->valid_until_year : null;
        $customer->security_code = $request->security_code ? $request->security_code : null;

        if ($customer->save()) {
            $request->session()->flash('alert-success', 'Your profile was updated successfully.');
        }

        return redirect('/profile');

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
