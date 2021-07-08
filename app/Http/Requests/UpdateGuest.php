<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuest extends FormRequest
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
            'type' => 'required|string|hashed_exists:identification_types,id',
            'dni' => 'required|alpha_num|min:5|max:15|unique_per_user:guests,dni,' . $id,
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'nullable|email|unique_per_user:guests,email,' . $id,
            'gender' => 'nullable|string|in:f,m,x',
            'birtdate' => 'nullable|date',
            'profession' => 'nullable|string',
            'nationality' => 'required|string|hashed_exists:countries,id'
        ];
    }
}
