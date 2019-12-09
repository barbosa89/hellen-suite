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
            'dni' => 'required|alpha_num|unique_per_user:guests,dni',
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email|unique_per_user:guests,email',
            'address' => 'nullable|string|max:191',
            'phone' => 'nullable|string|max:10',
            'gender' => 'nullable|string|in:f,m,x',
            'birtdate' => 'nullable|date',
            'room' => 'required|string',
            'responsible_adult' => 'nullable|string'
        ];
    }
}
