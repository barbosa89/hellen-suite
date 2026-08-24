<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Alcohol\ISO4217;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGeneralSettingsRequest extends FormRequest
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
            'currency' => [
                'required',
                'string',
                'size:3',
                Rule::in(array_column((new ISO4217())->getAll(), 'alpha3')),
            ],
        ];
    }
}
