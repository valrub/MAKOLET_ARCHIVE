<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Customer;
use App\Order;

use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $searchByName = $request->get('name') ? $request->get('name') : '';
        $searchByPhone = $request->get('phone') ? $request->get('phone') : '';
        $searchByEmail = $request->get('email') ? $request->get('email') : '';

        $customers = Customer::withTrashed()->join('users', 'users.id', '=', 'customers.user_id')->select('customers.*', 'users.email')->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%" . $searchByName . "%' AND phone LIKE '%" . $searchByPhone . "%' AND email LIKE '%" . $searchByEmail . "%'")->orderBy('customers.id', 'desc')->paginate(20);

        // Prepare the data for the Excel export
        $request->session()->flash('extract_filename', 'Customers');
        $request->session()->flash('extract_excel', $customers);
        
        return view('admin.customers.customers')->with('customers', $customers);
    }

    public function extract(Request $request)
    {
        $customers = Customer::withTrashed()->join('users', 'users.id', '=', 'customers.user_id')->select('customers.*', 'users.email')->orderBy('customers.id', 'desc')->get();

        // Prepare the data for the Excel export
        $request->session()->flash('extract_filename', 'Customers');
        $request->session()->flash('extract_excel', $customers);
        return redirect('admin/extract');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCustomerRequest $request)
    {

        $user = User::create([

            // User fields
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

        if (!$customer) {
            $user->delete();
        }

        return redirect()->route('admin.customers.show', $customer);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::withTrashed()->find($id);
        $orders = Order::where('customer_id', $customer->id)->orderBy('id', 'desc')->paginate(10);
        return view('admin.customers.customer')->with('customer', $customer)->with('orders', $orders);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::withTrashed()->find($id);
        return view('admin.customers.edit')->with('customer', $customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        
        $customer = Customer::withTrashed()->find($id);

        if (!$customer) {
            return 'ERROR 500 - Customer not found';
        }

        if ($request->password) {
            $user = User::find($customer->user_id);
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

        $customer->save();

        return redirect()->route('admin.customers.show', $customer);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::withTrashed()->find($id);
        if ($customer->trashed()) {
            $customer->restore();
        } else {
            $customer->delete();
        }
        return redirect()->route('admin.customers.show', $id);
    }
}
