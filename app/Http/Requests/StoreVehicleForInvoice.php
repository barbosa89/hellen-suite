<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleForInvoice extends FormRequest
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
            'guest' => 'required|string|hashed_exists:guests,id',
            'type' => 'required|string|hashed_exists:vehicle_types,id',
            'registration' => 'required|string|regex:/[A-Z0-9]+/|unique:vehicles,registration',
            'brand' => 'required|string',
            'color' => 'required|string'
        ];
    }
}
