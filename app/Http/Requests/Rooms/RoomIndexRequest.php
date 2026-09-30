<?php

declare(strict_types=1);

namespace App\Http\Requests\Rooms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RoomIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'check_in_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out_on' => ['required', 'date_format:Y-m-d', 'after:check_in_on'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'check_in_on' => $this->input('check_in_on', today()->toDateString()),
            'check_out_on' => $this->input('check_out_on', today()->addDay()->toDateString()),
        ]);
    }
}
