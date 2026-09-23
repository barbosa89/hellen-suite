<?php

declare(strict_types=1);

namespace App\Http\Requests\Reservations;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReservationAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'planned_check_in_on' => ['required', 'date', 'after_or_equal:today'],
            'planned_check_out_on' => ['required', 'date', 'after:planned_check_in_on'],
        ];
    }
}
