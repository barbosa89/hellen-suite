<?php

namespace App\Http\Requests;

use App\Constants\Genders;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGuest extends FormRequest
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
            'identification_type_id' => ['required', 'int', 'exists:identification_types,id'],
            'dni' => [
                'required',
                'alpha_num',
                'min:5',
                'max:15',
                Rule::unique('guests')
                    ->where('user_id', id_parent())
            ],
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'last_name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string', 'max:191'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', Rule::in(Genders::toArray())],
            'birthdate' => ['nullable', 'date'],
            'profession' => ['nullable', 'string', 'max:100'],
            'country_id' => ['required', 'int', 'exists:countries,id']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'identification_type_id' => id_decode($this->input('identification_type_id')),
            'country_id' => id_decode($this->input('country_id')),
        ]);
    }
}
