<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateShopRequest extends Request
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
            'password' => 'required|between:6,32',

            // Shop fields
            'name' => 'required|unique:shops,name',
            'type' => 'between:1,2',
            'first_name' => 'required|between:2,32',
            'last_name' => 'required|between:2,32',
            'company_name' => 'required',
            'company_id' => 'required',
            'phone' => 'required',
            'mobile' => 'required',
            'city' => 'required',
            'street' => 'required',
            'building' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'bank_account_number' => 'required',
            'bank_name' => 'required',
            'bank_branch' => 'required'

        ];
    }
}
