<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\StayGuestRole;
use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Stay;
use App\Models\StayGuest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AddGuestToStay
{
    /**
     * @param  array{room_occupancy_id: int, guest_id?: int|null, identification_type_id?: int|null, first_name?: string|null, last_name?: string|null, identification_number?: string|null, mobile?: string|null, email?: string|null}  $data
     */
    public function execute(Hotel $hotel, Stay $stay, array $data): StayGuest
    {
        return DB::transaction(function () use ($hotel, $stay, $data): StayGuest {
            $stay = $hotel->stays()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'room_occupancy_id' => trans('stays.validation.stay_closed'),
                ]);
            }

            $occupancy = $stay->roomOccupancies()
                ->with('room.roomType:id,capacity')
                ->lockForUpdate()
                ->find($data['room_occupancy_id']);

            if ($occupancy === null) {
                throw ValidationException::withMessages([
                    'room_occupancy_id' => trans('stays.validation.occupancy_invalid'),
                ]);
            }

            if ($occupancy->checked_out_at !== null) {
                throw ValidationException::withMessages([
                    'room_occupancy_id' => trans('stays.validation.occupancy_closed'),
                ]);
            }

            if ($occupancy->guests()->count() >= $occupancy->room->roomType->capacity) {
                throw ValidationException::withMessages([
                    'room_occupancy_id' => trans('stays.validation.room_capacity'),
                ]);
            }

            $guest = $this->resolveGuest($hotel, $data);

            if ($stay->stayGuests()->where('guest_id', $guest->id)->exists()) {
                throw ValidationException::withMessages([
                    'guest_id' => trans('stays.validation.guest_already_in_stay'),
                ]);
            }

            $stayGuest = $stay->stayGuests()->create([
                'guest_id' => $guest->id,
                'role' => StayGuestRole::Companion,
            ]);

            $occupancy->guests()->attach($guest->id);

            return $stayGuest;
        });
    }

    /**
     * @param  array{guest_id?: int|null, identification_type_id?: int|null, first_name?: string|null, last_name?: string|null, identification_number?: string|null, mobile?: string|null, email?: string|null}  $data
     */
    private function resolveGuest(Hotel $hotel, array $data): Guest
    {
        if (isset($data['guest_id'])) {
            return $hotel->guests()->findOrFail($data['guest_id']);
        }

        $matchingGuestExists = $hotel->guests()
            ->where('identification_type_id', $data['identification_type_id'])
            ->where('identification_number', $data['identification_number'])
            ->exists();

        if ($matchingGuestExists) {
            throw ValidationException::withMessages([
                'identification_number' => trans('stays.validation.duplicate_guest'),
            ]);
        }

        return $hotel->guests()->create([
            'identification_type_id' => $data['identification_type_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'identification_number' => $data['identification_number'],
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
        ]);
    }
}
