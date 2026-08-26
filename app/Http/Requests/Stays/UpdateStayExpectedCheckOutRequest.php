<?php

declare(strict_types=1);

namespace App\Http\Requests\Stays;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStayExpectedCheckOutRequest extends FormRequest
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
            'expected_check_out_on' => ['required', 'date', 'after:today'],
        ];
    }
}
