<?php

declare(strict_types=1);

namespace App\Http\Requests\Payments;

use App\Constants\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'decimal:0,2', 'gt:0'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'comment' => ['nullable', 'string', 'max:255'],
            'support' => ['nullable', File::image()->max(5 * 1024)],
        ];
    }
}
