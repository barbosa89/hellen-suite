<?php

declare(strict_types=1);

namespace App\Http\Requests\Folios;

use App\Constants\FolioAdjustmentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFolioAdjustmentRequest extends FormRequest
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
            'type' => ['required', Rule::enum(FolioAdjustmentType::class), Rule::notIn([FolioAdjustmentType::Correction->value])],
            'amount' => ['required', 'decimal:0,2', 'gt:0'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
