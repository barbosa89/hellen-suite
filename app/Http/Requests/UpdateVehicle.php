<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicle extends FormRequest
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
            'type' => 'required|string|hashed_exists:vehicle_types,id',
            'registration' => 'required|alpha_num|unique_per_user:vehicles,registration,' . $id,
            'brand' => 'required|string',
            'color' => 'required|string'
        ];
    }
}
