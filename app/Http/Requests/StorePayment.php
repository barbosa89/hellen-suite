<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayment extends FormRequest
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
            'date' => 'required|date|before:tomorrow',
            'commentary' => 'required|string|max:191',
            'value' => 'required|numeric|min:0.01|max:999999999',
            'invoice' => 'nullable|file|max:200|mimes:jpeg,png,pdf'
        ];
    }
}
