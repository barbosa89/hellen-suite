<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Actions\Tra\QueueTraSubmissions;
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
    public function __construct(private QueueTraSubmissions $queueTraSubmissions) {}

    /**
     * @param  array{room_occupancy_id: int, guest_id?: int|null, identification_type_id?: int|null, first_name?: string|null, second_first_name?: string|null, last_name?: string|null, second_last_name?: string|null, identification_number?: string|null, birth_date?: string|null, gender?: string|null, nationality?: string|null, mobile?: string|null, email?: string|null, residence_country?: string|null, residence_subdivision?: string|null, residence_locality?: string|null, origin_country?: string|null, origin_subdivision?: string|null, origin_locality?: string|null, destination_country?: string|null, destination_subdivision?: string|null, destination_locality?: string|null, travel_purpose?: string|null, transport_means?: string|null}  $data
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
                'checked_in_at' => now(),
                'residence_country' => $data['residence_country'] ?? null,
                'residence_subdivision' => $data['residence_subdivision'] ?? null,
                'residence_locality' => $data['residence_locality'] ?? null,
                'origin_country' => $data['origin_country'] ?? null,
                'origin_subdivision' => $data['origin_subdivision'] ?? null,
                'origin_locality' => $data['origin_locality'] ?? null,
                'destination_country' => $data['destination_country'] ?? null,
                'destination_subdivision' => $data['destination_subdivision'] ?? null,
                'destination_locality' => $data['destination_locality'] ?? null,
                'travel_purpose' => $data['travel_purpose'] ?? null,
                'transport_means' => $data['transport_means'] ?? null,
            ]);

            $occupancy->guests()->attach($guest->id);

            $this->queueTraSubmissions->execute($hotel, $stay->refresh());

            return $stayGuest;
        });
    }

    /**
     * @param  array{guest_id?: int|null, identification_type_id?: int|null, first_name?: string|null, second_first_name?: string|null, last_name?: string|null, second_last_name?: string|null, identification_number?: string|null, birth_date?: string|null, gender?: string|null, nationality?: string|null, mobile?: string|null, email?: string|null}  $data
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
            'second_first_name' => $data['second_first_name'] ?? null,
            'last_name' => $data['last_name'],
            'second_last_name' => $data['second_last_name'] ?? null,
            'identification_number' => $data['identification_number'],
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'nationality' => isset($data['nationality']) ? mb_strtoupper($data['nationality']) : null,
            'residence_country' => isset($data['residence_country']) ? mb_strtoupper($data['residence_country']) : null,
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
        ]);
    }
}
