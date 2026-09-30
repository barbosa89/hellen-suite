<?php

declare(strict_types=1);

namespace App\Http\Requests\Cash;

use App\Constants\CashMovementType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCashMovementRequest extends FormRequest
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
            'type' => ['required', Rule::in([CashMovementType::ManualEntry->value, CashMovementType::Withdrawal->value])],
            'amount' => ['required', 'decimal:0,2', 'gt:0'],
            'comment' => ['required', 'string', 'max:255'],
        ];
    }
}
