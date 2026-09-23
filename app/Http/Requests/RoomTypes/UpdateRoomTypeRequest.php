<?php

declare(strict_types=1);

namespace App\Http\Requests\RoomTypes;

use App\Constants\ReservationStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $capacity = $this->integer('capacity');
            $roomType = $this->route('room_type');
            $rooms = $roomType->rooms()->with([
                'roomOccupancies' => fn ($query) => $query->whereNull('checked_out_at')->withCount('guests'),
                'reservedRooms' => fn ($query) => $query
                    ->whereHas('reservation', fn ($reservationQuery) => $reservationQuery->where('status', ReservationStatus::Confirmed))
                    ->withCount('reservationGuests'),
            ])->get();
            $exceedsCommittedOccupancy = $rooms->contains(fn ($room): bool => $room->roomOccupancies->contains(fn ($occupancy): bool => $occupancy->guests_count > $capacity)
                || $room->reservedRooms->contains(fn ($reservedRoom): bool => $reservedRoom->reservation_guests_count > $capacity));

            if ($exceedsCommittedOccupancy) {
                $validator->errors()->add('capacity', trans('room_types.messages.capacity_blocked'));
            }
        }];
    }
}
