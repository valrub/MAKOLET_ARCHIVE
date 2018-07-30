<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CloseOrderRequest extends Request
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
            'price' => 'required|max:1000|regex:/^[\d*]{1,3}(\.\d{2})?$/',
            'delivery_price' => 'max:1000|regex:/^[\d*]{1,3}(\.\d{2})?$/'
        ];
    }
}
