<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateCommentRequest extends Request
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
            'customer_id' => 'exists:customers,id',
            'shop_id' => 'exists:shops,id',
            'order_id' => 'exists:orders,id',
            'level' => 'required|numeric|between:1,3',
            'comment' => 'required'
        ];
    }
}
