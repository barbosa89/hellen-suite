<?php

declare(strict_types=1);

namespace App\Http\Requests\Guests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identification_type_id' => ['required', 'integer', Rule::exists('identification_types', 'id')],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'identification_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('guests', 'identification_number')
                    ->where('hotel_id', $this->route('hotel')->getKey())
                    ->where('identification_type_id', $this->integer('identification_type_id')),
            ],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'second_first_name' => ['nullable', 'string', 'max:100'],
            'second_last_name' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', Rule::in(['M', 'F'])],
            'nationality' => ['nullable', 'string', 'size:3'],
            'residence_country' => ['nullable', 'string', 'size:3'],
        ];
    }
}
