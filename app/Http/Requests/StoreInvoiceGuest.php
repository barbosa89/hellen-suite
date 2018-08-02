<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceGuest extends FormRequest
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
            'type' => 'required|string',
            'dni' => 'required|string|unique:guests,dni',
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email|unique:guests,email',
            'gender' => 'nullable|string|in:f,m,x',
            'birtdate' => 'nullable|date',
            'room' => 'required|string',
            'responsible_adult' => 'nullable|string'
        ];
    }
}
