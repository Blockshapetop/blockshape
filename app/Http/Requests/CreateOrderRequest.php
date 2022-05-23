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
        return [
            'history' => 'required|integer|exists:user_history,id',
            'offer'   => 'required|integer|exists:offers,id',
            'gateway' => 'required|in:qiwi,cc-vb'
        ];
    }
}

