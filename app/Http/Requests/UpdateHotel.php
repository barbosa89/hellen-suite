<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotel extends FormRequest
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
            'address' => 'required|string|max:100',
            'phone' => 'required|string|max:10',
            'mobile' => 'required|string|max:10',
            'email' => 'required|email|max:100|unique:hotels,email',
            'image' => 'nullable|file|max:200|mimes:jpeg,png'
        ];
    }
}
