<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenance extends FormRequest
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
            'date' => 'required|date',
            'commentary' => 'required|string|max:255',
            'value' => 'nullable|numeric|min:0.1|max:99999999',
            'invoice' => 'nullable|file|max:200|mimes:jpeg,png'
        ];
    }
}
