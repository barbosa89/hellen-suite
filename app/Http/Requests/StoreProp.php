<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProp extends FormRequest
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
            'description' => 'required|string|max:191|unique_with:props,hotel#hotel_id',
            'quantity' => 'required|numeric|max:9999|min:1',
            'price' => 'required|numeric|min:1',
            'hotel' => 'required|string|hashed_exists:hotels,id',
            'comments' => 'nullable|string|max:400',
            'company' => 'nullable|string|hashed_exists:companies,id'
        ];
    }
}
