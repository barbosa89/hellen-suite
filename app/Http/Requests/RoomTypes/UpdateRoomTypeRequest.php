<?php

declare(strict_types=1);

namespace App\Http\Requests\RoomTypes;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomTypeRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('room_types', 'name')
                    ->where('hotel_id', $this->route('hotel')->getKey())
                    ->ignore($this->route('room_type')),
            ],
            'capacity' => ['required', 'integer', 'between:1,255'],
        ];
    }
}
