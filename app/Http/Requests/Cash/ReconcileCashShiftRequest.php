<?php

declare(strict_types=1);

namespace App\Http\Requests\Cash;

use App\Constants\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReconcileCashShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reconciliations' => ['required', 'array', 'min:1'],
            'reconciliations.*.method' => ['required', Rule::enum(PaymentMethod::class)],
            'reconciliations.*.currency' => ['required', 'string', 'size:3'],
            'reconciliations.*.declared_amount' => ['required', 'decimal:0,2'],
            'closing_note' => ['nullable', 'string', 'max:255'],
            'opening_note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
