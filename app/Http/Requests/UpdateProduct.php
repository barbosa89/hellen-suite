<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProduct extends FormRequest
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
        $id = id_decode($this->route('id'));

        return [
            'description' => 'required|string|max:191|unique_with:products,hotel#hotel_id, ' . $id,
            'brand' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:1',
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
            'description.unique_with' => 'La descripción ya existe en el hotel seleccionado.',
        ];
    }
}
