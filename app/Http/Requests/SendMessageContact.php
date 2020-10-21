<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageContact extends FormRequest
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
            'name' => 'required|string|max:100|min:3',
            'lastname' => 'required|string|max:100|min:3',
            'email' => 'required|email:rfc,dns,spoof,filter,strict',
            'phone' => 'required|numeric|digits_between:7,13',
            'message' => 'required|string|max:500|min:20',
        ];
    }
}
