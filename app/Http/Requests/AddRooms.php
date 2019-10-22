<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddRooms extends FormRequest
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
            'hotel' => 'required|string|hashed_exists:hotels,id',
            'number' => 'required|string|exists:rooms,number',
            'price' => 'required|numeric|price:rooms,number',
            'start' => 'required|date|after_or_equal:today',
            'end' => 'nullable|date|after:start'
        ];
    }
}
