<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompany extends FormRequest
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
            'tin' => 'required|alpha_num|min:5|max:30|unique_per_user:companies,tin',
            'business_name' => 'required|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|min:6|max:12',
            'mobile' => 'nullable|string|min:6|max:12',
            'is_supplier' => 'required|integer|in:0,1'
        ];
    }
}
