<?php

declare(strict_types=1);

namespace App\Http\Requests\Stays;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRoomOccupancyRequest extends FormRequest
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
            'room_id' => [
                'required',
                'integer',
                Rule::exists('rooms', 'id')->where('hotel_id', $this->route('hotel')->getKey()),
            ],
            'nightly_rate' => ['required', 'decimal:0,2', 'gt:0', 'max:9999999999.99'],
        ];
    }
}
