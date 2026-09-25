<?php

declare(strict_types=1);

namespace App\Http\Requests\Stays;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckOutRoomOccupancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'checked_out_at' => ['nullable', 'date', 'before_or_equal:now'],
        ];
    }
}
