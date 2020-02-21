<?php

namespace App\Http\Requests;

use App\Helpers\Id;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompany extends FormRequest
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
        $id = Id::get($this->route('id'));

        return [
            'tin' => 'required|alpha_num|unique_per_user:companies,tin,' . $id,
            'business_name' => 'required|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|min:6|max:12',
            'mobile' => 'nullable|string|min:6|max:12',
            'is_supplier' => 'required|integer|in:0,1'
        ];
    }
}
