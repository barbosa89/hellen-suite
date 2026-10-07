<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class UpdateHotelRequest extends FormRequest
{
    use Concerns\HasHotelComplianceRules;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeCompliance();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'tin' => ['required', 'string', 'max:30', Rule::unique('hotels', 'tin')->ignore($this->hotel)],
            'address' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'image' => [
                'nullable',
                File::image()->max('1mb'),
                'mimes:jpg,jpeg,png,webp',
            ],
            ...$this->complianceRules($this->hotel),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateCompliance($validator, $this->route('hotel'));
    }
}
