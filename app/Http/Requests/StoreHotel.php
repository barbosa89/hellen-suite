<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotel extends FormRequest
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
            'business_name' => 'required|string|max:191|unique_per_user:hotels,business_name',
            'tin' => 'required|string|max:30|headquarters',
            'address' => 'required|string|max:100',
            'phone' => 'required|string|max:10',
            'mobile' => 'required|string|max:10',
            'email' => 'required|email|max:100|unique:hotels,email',
            'image' => 'nullable|file|max:200|mimes:jpeg,png',
            'type' => 'required|string|in:main,headquarters',
            'main_hotel' => 'required_if:type,headquarters|hashed_exists:hotels,id'
        ];
    }
}
