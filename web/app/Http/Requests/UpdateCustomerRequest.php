<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateCustomerRequest extends Request
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
            'password' => 'between:6,32',

            // Customer fields
            'first_name' => 'required|between:2,32',
            'last_name' => 'required|between:2,32',
            'phone' => 'required',
            'city' => 'required',
            'street' => 'required',
            'building' => 'required',
            'entrance' => '',
            'apartment' => '',
            'latitude' => '',
            'longitude' => ''
            
        ];
    }
    
}
