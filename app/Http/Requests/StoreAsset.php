<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAsset extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => 'required|string|max:20|unique_with:assets,hotel#hotel_id',
            'description' => 'required|string|max:191',
            'brand' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'serial_number' => 'nullable|string|max:150',
            'price' => 'required|numeric|min:1|max:999999999',
            'location' => 'nullable|string|max:50',
            'room' => 'nullable|string|hashed_exists:rooms,id',
            'hotel' => 'required|string|hashed_exists:hotels,id',
        ];
    }

    public function messages(): array
    {
        return [
            'number.unique_with' => 'El número ya existe en el hotel seleccionado.',
        ];
    }
}
