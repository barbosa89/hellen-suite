<?php

declare(strict_types=1);

namespace App\Http\Requests\Stays;

use App\Models\Hotel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AddGuestToStayRequest extends FormRequest
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
            'room_occupancy_id' => ['required', 'integer', Rule::exists('room_occupancies', 'id')],
            'guest_id' => [
                'nullable',
                'integer',
                Rule::exists('guests', 'id')->where('hotel_id', $this->route('hotel')->getKey()),
            ],
            'identification_type_id' => ['required_without:guest_id', 'nullable', 'integer', Rule::exists('identification_types', 'id')],
            'first_name' => ['required_without:guest_id', 'nullable', 'string', 'max:100'],
            'last_name' => ['required_without:guest_id', 'nullable', 'string', 'max:100'],
            'identification_number' => ['required_without:guest_id', 'nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->filled('guest_id') || $validator->errors()->hasAny([
                'identification_type_id',
                'identification_number',
            ])) {
                return;
            }

            /** @var Hotel $hotel */
            $hotel = $this->route('hotel');

            $guestExists = $hotel->guests()
                ->where('identification_type_id', $this->integer('identification_type_id'))
                ->where('identification_number', $this->string('identification_number')->toString())
                ->exists();

            if ($guestExists) {
                $validator->errors()->add('identification_number', trans('stays.validation.duplicate_guest'));
            }
        }];
    }
}
