<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAsset extends FormRequest
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
            'number' => 'required|string|max:20|unique_with:assets,hotel#hotel_id,' . $id,
            'description' => 'required|string|max:191',
            'brand' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'serial_number' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:50',
            'room' => 'nullable|string|hashed_exists:rooms,id',
            'hotel' => 'required|string|hashed_exists:hotels,id',
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
            'description.unique_with' => 'El número ya existe en el hotel seleccionado.',
        ];
    }
}
