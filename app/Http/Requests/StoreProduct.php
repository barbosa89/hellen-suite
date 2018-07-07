<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProduct extends FormRequest
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
            'description' => 'required|string|max:191|unique:products,description',
            'brand' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
