<?php

declare(strict_types=1);

namespace App\Http\Requests\Rooms;

use App\Constants\HousekeepingStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomHousekeepingStatusRequest extends FormRequest
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
            'housekeeping_status' => ['required', Rule::enum(HousekeepingStatus::class)],
        ];
    }
}
