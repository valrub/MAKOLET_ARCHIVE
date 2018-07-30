<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SendProposalRequest extends Request
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
            'proposal' => 'required|exists:order_proposals,id',
            'delivery_time' => 'required|numeric|min:1|max:999',
            'shop_notes' => ''
        ];
    }
}
