<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Shop;
use App\OrderProposal;
use App\Feedback;

use App\Http\Requests\CreateShopRequest;
use App\Http\Requests\UpdateShopRequest;

class ShopController extends Controller
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

        $shops = Shop::withTrashed()->join('users', 'users.id', '=', 'shops.user_id')->select('shops.*', 'users.email')->whereRaw("name LIKE '%" . $searchByName . "%' AND phone LIKE '%" . $searchByPhone . "%' AND email LIKE '%" . $searchByEmail . "%'")->orderBy('shops.id', 'desc')->paginate(20);
        
        // Prepare the data for Excel extraction
        $request->session()->flash('extract_filename', 'Shops');
        $request->session()->flash('extract_excel', $shops);

        return view('admin.shops.shops')->with('shops', $shops);
    }

    public function extract(Request $request)
    {
        $shops = Shop::withTrashed()->join('users', 'users.id', '=', 'shops.user_id')->select('shops.*', 'users.email')->orderBy('shops.id', 'desc')->get();

        // Prepare the data for the Excel export
        $request->session()->flash('extract_filename', 'Shops');
        $request->session()->flash('extract_excel', $shops);
        return redirect('admin/extract');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.shops.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateShopRequest $request)
    {
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'type' => 2
        ]);
        
        if (!$user) {
            return 'ERROR 500 - #1';
        }

        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'company_name' => $request->company_name,
            'company_id' => $request->company_id,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'city' => $request->city,
            'street' => $request->street,
            'building' => $request->building,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'bank_name' => $request->bank_name,
            'bank_branch' => $request->bank_branch,
            'bank_account_number' => $request->bank_account_number
        ]);

        if (!$shop) {
            $user->delete();
            return 'ERROR 500 - #2';
        }

        return redirect()->route('admin.shops.show', $shop);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::withTrashed()->find($id);
        $proposals = OrderProposal::where('shop_id', $shop->id)->orderBy('id', 'desc')->paginate(10);
        return view('admin.shops.shop')->with('shop', $shop)->with('proposals', $proposals);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop = Shop::withTrashed()->find($id);
        return view('admin.shops.edit')->with('shop', $shop);
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
        
        $shop = Shop::withTrashed()->find($id);

        if (!$shop) {
            return 'ERROR 500 - Shop not found';
        }

        if ($request->password) {
            $user = User::find($shop->user_id);
            $user->password = bcrypt($request->password);
            $user->save();
        }

        $shop->name = $request->name;
        $shop->type = $request->type;
        $shop->first_name = $request->first_name;
        $shop->last_name = $request->last_name;
        $shop->company_name = $request->company_name;
        $shop->company_id = $request->company_id;
        $shop->phone = $request->phone;
        $shop->mobile = $request->mobile;
        $shop->city = $request->city;
        $shop->street = $request->street;
        $shop->building = $request->building;
        $shop->latitude = $request->latitude;
        $shop->longitude = $request->longitude;
        $shop->bank_name = $request->bank_name;
        $shop->bank_branch = $request->bank_branch;
        $shop->bank_account_number = $request->bank_account_number;

        if (!$shop->save()) {
            return 'ERROR 500 - Shop not saved';
        }

        return redirect()->route('admin.shops.show', $shop);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::withTrashed()->find($id);
        if ($shop->trashed()) {
            $shop->restore();
        } else {
            $shop->delete();
        }
        return redirect()->route('admin.shops.show', $id);
    }

    public function feedback($id)
    {
        $shop = Shop::withTrashed()->find($id);
        $feedbacks = $shop->feedbacks()->paginate(10);
        return view('admin.shops.feedback')->with('shop', $shop)->with('feedbacks', $feedbacks);
    }

    public function destroyFeedback($id)
    {  
        $feedback = Feedback::find($id);
        $shop = $feedback->shop;
        $feedback->delete();
        return redirect()->route('admin.shops.feedback', $shop);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
