<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoom extends FormRequest
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
            'number' => 'required|string|unique_with:rooms,hotel#hotel_id',
            'description' => 'required|string|max:500',
            'price' => 'required|integer|min:1|max:999999',
            'type' => 'required|in:0,1',
            'min_price' => 'required|integer|lte:price',
            'capacity' => 'required|integer|min:1|max:12',
            'floor' => 'required|integer|min:1|max:500',
            'tax_status' => 'required|in:0,1,2',
            'tax' => 'nullable|numeric|min:0.01|max:0.5',
            'hotel' => 'required|string|hashed_exists:hotels,id'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'number.unique_with' => 'El número ya existe en el hotel seleccionado.',
        ];
    }
}
