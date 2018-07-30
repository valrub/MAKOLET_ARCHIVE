<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateOrderRequest extends Request
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

        $rules = [];

        foreach($this->request->get('goods') as $key => $val)
        {
            if ($key == 0) {
                $rules['goods.'.$key] = 'required';
            }
        }

        /*
        foreach($this->request->get('quantities') as $key => $val)
        {
            if ($key == 0) {
                $rules['quantities.'.$key] = 'required|numeric|min:1';
            }
        }
        */

        $rules['shops'] = 'required';
        $rules['shops.*'] = 'required|exists:shops,id';

        $rules['city'] = 'required';
        $rules['street'] = 'required';
        $rules['building'] = 'required';
        $rules['entrance'] = '';
        $rules['apartment'] = '';
        $rules['latitude'] = 'required';
        $rules['longitude'] = 'required';

        return $rules;
    }

    public function messages()
    {

        return [
            'goods.*' => 'The goods field is required',
            /* 'quantities.*' => 'The quantity field is required', */
            'shops.*' => 'At least one shop should be selected',
        ];
    }
}
