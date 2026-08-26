<?php

declare(strict_types=1);

namespace App\Http\Requests\Stays;

use Illuminate\Foundation\Http\FormRequest;

class CheckOutStayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checked_out_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }
}
