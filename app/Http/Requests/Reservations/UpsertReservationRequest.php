<?php

declare(strict_types=1);

namespace App\Http\Requests\Reservations;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

use function count;
use function in_array;

class UpsertReservationRequest extends FormRequest
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
            'responsible_guest_key' => ['required', 'string', 'max:100'],
            'guests' => ['required', 'array', 'min:1'],
            'guests.*.key' => ['required', 'string', 'max:100', 'distinct'],
            'guests.*.guest_id' => ['nullable', 'integer', Rule::exists('guests', 'id')->where('hotel_id', $this->route('hotel')->getKey())],
            'guests.*.identification_type_id' => ['required_without:guests.*.guest_id', 'nullable', 'integer', Rule::exists('identification_types', 'id')],
            'guests.*.first_name' => ['required_without:guests.*.guest_id', 'nullable', 'string', 'max:100'],
            'guests.*.last_name' => ['required_without:guests.*.guest_id', 'nullable', 'string', 'max:100'],
            'guests.*.identification_number' => ['required_without:guests.*.guest_id', 'nullable', 'string', 'max:50'],
            'guests.*.mobile' => ['nullable', 'string', 'max:30'],
            'guests.*.email' => ['nullable', 'email', 'max:255'],
            'reserved_rooms' => ['required', 'array', 'min:1'],
            'reserved_rooms.*.room_id' => ['required', 'integer', 'distinct', Rule::exists('rooms', 'id')->where('hotel_id', $this->route('hotel')->getKey())],
            'reserved_rooms.*.nightly_rate' => ['required', 'decimal:0,2', 'gt:0', 'max:9999999999.99'],
            'reserved_rooms.*.guest_keys' => ['required', 'array', 'min:1'],
            'reserved_rooms.*.guest_keys.*' => ['required', 'string', 'max:100'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $guestKeys = Arr::pluck($this->input('guests', []), 'key');
            $responsibleKey = $this->string('responsible_guest_key')->toString();

            if (! in_array($responsibleKey, $guestKeys, true)) {
                $validator->errors()->add('responsible_guest_key', trans('reservations.validation.responsible_required'));
            }

            $assignedGuestKeys = [];

            foreach ($this->input('reserved_rooms', []) as $roomIndex => $reservedRoom) {
                foreach ($reservedRoom['guest_keys'] ?? [] as $guestKey) {
                    if (! in_array($guestKey, $guestKeys, true)) {
                        $validator->errors()->add("reserved_rooms.{$roomIndex}.guest_keys", trans('reservations.validation.unknown_guest'));
                    }

                    $assignedGuestKeys[] = $guestKey;
                }
            }

            $this->validateEachGuestAssignedOnce($validator, $guestKeys, $assignedGuestKeys);
        }];
    }

    /**
     * @param  array<array-key, mixed>  $guestKeys
     * @param  array<int, mixed>  $assignedGuestKeys
     */
    private function validateEachGuestAssignedOnce(Validator $validator, array $guestKeys, array $assignedGuestKeys): void
    {
        foreach ($guestKeys as $guestKey) {
            if (count(array_keys($assignedGuestKeys, $guestKey, true)) !== 1) {
                $validator->errors()->add('reserved_rooms', trans('reservations.validation.assign_every_guest_once'));

                break;
            }
        }
    }
}
