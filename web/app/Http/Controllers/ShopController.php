<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Shop;

use App\Http\Requests\UpdateShopRequest;

class ShopController extends Controller
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
        $shops = Shop::all();
        return view('shops.map')->with('shops', $shops)->with('customer', $customer);
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
        $shop = Shop::find($id);
        $shop->rating = round($shop->feedbacks->avg('score'));
        $shop->feedbacks;
        return view('shops.shop')->with('shop', $shop);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check if the user is logged in and it's a shop owner
        if (!\Auth::check() || !\Auth::user()->shop) return redirect('/');
        
        $shop = Shop::find($id);

        // Check if there is such shop and if the current user is owner of the shop
        if(!$shop || \Auth::id() != $shop->user_id) return redirect('/');
        
        return view('shops.edit')->with('shop', $shop);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShopRequest $request, $id)
    {
        if (!\Auth::check() || !\Auth::user()->shop || \Auth::user()->shop->id != $id) return redirect('/');
        
        $user = \Auth::user();
        $shop = Shop::find($id);

        // Change password if required
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
        $shop->bank_account_number = $request->bank_account_number;
        $shop->bank_name = $request->bank_name;
        $shop->bank_branch = $request->bank_branch;

        $shop->save();

        return redirect()->route('shops.edit', $shop);
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
