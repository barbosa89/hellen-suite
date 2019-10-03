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
            'address' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:100|unique:hotels,email',
            'image' => 'nullable|file|max:200|mimes:jpeg,png'
        ];
    }
}
