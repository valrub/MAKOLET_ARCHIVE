<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Customer;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/orders/create';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            
            // User fields
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6', // required|confirmed|min:6

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
            'security_code' => '',

            // Terms of use
            'terms' => 'required'
            
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        $user = User::create([

            // User fields
            'email' => $data['email'],
            'password' => bcrypt($data['password']),

        ]);

        $customer = Customer::create([
            
            // Customer fields
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'city' => $data['city'],
            'street' => $data['street'],
            'building' => $data['building'],
            'entrance' => $data['entrance'],
            'apartment' => $data['apartment'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude']

        ]);

        if (!$customer) {
            $user->delete();
        }

        \Session::flash('just-registered', true);
        return $user;

    }
}
