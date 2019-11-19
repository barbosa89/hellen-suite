<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddProducts extends FormRequest
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
            'room' => 'nullable|string',
            'product' => 'required|string|hashed_exists:products,id',
            'quantity' => 'required|integer|min:1|stock'
        ];
    }
}
