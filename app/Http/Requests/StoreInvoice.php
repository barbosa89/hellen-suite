<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoice extends FormRequest
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
            'room.*.number' => 'required|numeric|exists:rooms,number',
            'room.*.price' => 'required|numeric|price:rooms,number',
            'room.*.start' => 'required|date|after_or_equal:today',
            'room.*.end' => 'nullable|date|after:room.*.start',
            'registry' => 'required|string|in:checkin,reservation'
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
            'room.*.price.price' => 'Hay un error en el precio',
            'room.*.start.after_or_equal' => 'La fecha inicial debe ser igual o superior al día actual',
            'room.*.end.after' => 'La fecha final debe ser superior a la fecha inicial',
        ];
    }
}
