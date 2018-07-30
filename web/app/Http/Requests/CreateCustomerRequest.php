<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateCustomerRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

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
            'security_code' => ''
            
        ];
    }
}
