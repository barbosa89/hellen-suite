<?php

declare(strict_types=1);

namespace App\Http\Requests\Stays;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

use function count;
use function in_array;

class StoreStayRequest extends FormRequest
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
            'expected_check_out_on' => ['required', 'date', 'after:today'],
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
            'room_occupancies' => ['required', 'array', 'min:1'],
            'room_occupancies.*.room_id' => [
                'required',
                'integer',
                Rule::exists('rooms', 'id')->where('hotel_id', $this->route('hotel')->getKey()),
            ],
            'room_occupancies.*.nightly_rate' => ['required', 'decimal:0,2', 'gt:0', 'max:9999999999.99'],
            'room_occupancies.*.guest_keys' => ['required', 'array', 'min:1'],
            'room_occupancies.*.guest_keys.*' => ['required', 'string', 'max:100'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $guestKeys = Arr::pluck($this->input('guests', []), 'key');
            $responsibleKey = $this->string('responsible_guest_key')->toString();

            if (! in_array($responsibleKey, $guestKeys, true)) {
                $validator->errors()->add('responsible_guest_key', trans('stays.validation.responsible_required'));
            }

            $assignedGuestKeys = [];

            foreach ($this->input('room_occupancies', []) as $occupancyIndex => $occupancy) {
                foreach ($occupancy['guest_keys'] ?? [] as $guestKey) {
                    if (! in_array($guestKey, $guestKeys, true)) {
                        $validator->errors()->add("room_occupancies.{$occupancyIndex}.guest_keys", trans('stays.validation.unknown_guest'));
                    }

                    $assignedGuestKeys[] = $guestKey;
                }
            }

            foreach ($guestKeys as $guestKey) {
                if (count(array_keys($assignedGuestKeys, $guestKey, true)) !== 1) {
                    $validator->errors()->add('room_occupancies', trans('stays.validation.assign_every_guest_once'));

                    break;
                }
            }
        }];
    }
}
