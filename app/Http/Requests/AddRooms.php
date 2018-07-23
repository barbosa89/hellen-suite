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
            'room' => 'required|string',
            'start' => 'required|date|after_or_equal:today',
            'end' => 'required|date|after:start'
        ];
    }
}
