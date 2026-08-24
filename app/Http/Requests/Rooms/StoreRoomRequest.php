<?php

declare(strict_types=1);

namespace App\Http\Requests\Rooms;

use App\Constants\HousekeepingStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
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
            'room_type_id' => [
                'required',
                'integer',
                Rule::exists('room_types', 'id')->where('hotel_id', $this->route('hotel')->getKey()),
            ],
            'number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('rooms', 'number')->where('hotel_id', $this->route('hotel')->getKey()),
            ],
            'floor' => ['nullable', 'string', 'max:20'],
            'reference_price' => ['required', 'decimal:0,2', 'gt:0', 'max:9999999999.99'],
            'housekeeping_status' => ['required', Rule::enum(HousekeepingStatus::class)],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
