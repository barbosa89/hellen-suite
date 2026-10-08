<?php

declare(strict_types=1);

namespace App\Actions\Reservations;

use App\Actions\Rooms\RoomAvailability;
use App\Constants\ReservationStatus;
use App\Constants\StayGuestRole;
use App\Data\Reservations\ReservationData;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\ReservationGuest;
use App\Models\ReservedRoom;
use Illuminate\Validation\ValidationException;

use function count;

final class SaveReservation
{
    public function __construct(private RoomAvailability $roomAvailability) {}

    public function execute(Hotel $hotel, ReservationData $data, null|Reservation $reservation = null): Reservation
    {
        $roomsById = $this->roomAvailability
            ->query($hotel, $data->plannedCheckInOn, $data->plannedCheckOutOn, $reservation)
            ->with('roomType:id,capacity')
            ->whereKey(collect($data->reservedRooms)->pluck('roomId'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($roomsById->count() !== count($data->reservedRooms)) {
            throw ValidationException::withMessages([
                'reserved_rooms' => trans('reservations.validation.room_unavailable'),
            ]);
        }

        $guestsByKey = $this->resolveGuests($hotel, $data);
        $responsibleGuest = $guestsByKey[$data->responsibleGuestKey] ?? null;

        if ($responsibleGuest === null) {
            throw ValidationException::withMessages([
                'responsible_guest_key' => trans('reservations.validation.responsible_required'),
            ]);
        }

        if ($reservation === null) {
            $reservation = $hotel->reservations()->create([
                'responsible_guest_id' => $responsibleGuest->id,
                'status' => ReservationStatus::Draft,
                'planned_check_in_on' => $data->plannedCheckInOn,
                'planned_check_out_on' => $data->plannedCheckOutOn,
            ]);
        } else {
            $reservation->reservedRooms()->delete();
            $reservation->reservationGuests()->delete();
            $reservation->update([
                'responsible_guest_id' => $responsibleGuest->id,
                'planned_check_in_on' => $data->plannedCheckInOn,
                'planned_check_out_on' => $data->plannedCheckOutOn,
            ]);
        }

        /** @var array<string, ReservationGuest> $reservationGuestsByKey */
        $reservationGuestsByKey = [];

        foreach ($guestsByKey as $key => $guest) {
            $reservationGuestsByKey[$key] = $reservation->reservationGuests()->create([
                'guest_id' => $guest->id,
                'role' => $key === $data->responsibleGuestKey
                    ? StayGuestRole::Responsible
                    : StayGuestRole::Companion,
                ...$this->travelSnapshot($data, $key),
            ]);
        }

        foreach ($data->reservedRooms as $roomIndex => $roomData) {
            $room = $roomsById->get($roomData->roomId);

            if ($room === null || count($roomData->guestKeys) > $room->roomType->capacity) {
                throw ValidationException::withMessages([
                    "reserved_rooms.{$roomIndex}.guest_keys" => trans('reservations.validation.room_capacity'),
                ]);
            }

            $reservedRoom = $reservation->reservedRooms()->create([
                'room_id' => $room->id,
                'principal_guest_id' => isset($reservationGuestsByKey[$roomData->principalGuestKey ?? ''])
                    ? $reservationGuestsByKey[$roomData->principalGuestKey]->guest_id
                    : null,
                'nightly_rate' => $roomData->nightlyRate,
                'planned_check_in_on' => $data->plannedCheckInOn,
                'planned_check_out_on' => $data->plannedCheckOutOn,
            ]);

            $this->attachRoomGuests($reservedRoom, $roomData->guestKeys, $reservationGuestsByKey, $roomIndex);
        }

        return $reservation;
    }

    /**
     * @param  array<int, string>  $guestKeys
     * @param  array<string, ReservationGuest>  $reservationGuestsByKey
     */
    private function attachRoomGuests(ReservedRoom $reservedRoom, array $guestKeys, array $reservationGuestsByKey, int $roomIndex): void
    {
        foreach ($guestKeys as $guestKey) {
            $reservationGuest = $reservationGuestsByKey[$guestKey] ?? null;

            if ($reservationGuest === null) {
                throw ValidationException::withMessages([
                    "reserved_rooms.{$roomIndex}.guest_keys" => trans('reservations.validation.unknown_guest'),
                ]);
            }

            $reservedRoom->reservationGuests()->attach($reservationGuest);
        }
    }

    /** @return array<string, string|null> */
    private function travelSnapshot(ReservationData $data, string $key): array
    {
        foreach ($data->guests as $guestData) {
            if ($guestData->key === $key) {
                return $guestData->travel->toAttributes();
            }
        }

        return [];
    }

    /** @return array<string, Guest> */
    private function resolveGuests(Hotel $hotel, ReservationData $data): array
    {
        $guestsByKey = [];

        foreach ($data->guests as $guestData) {
            if ($guestData->guestId !== null) {
                $guest = $hotel->guests()->findOrFail($guestData->guestId);
            } else {
                $matchingGuestExists = $hotel->guests()
                    ->where('identification_type_id', $guestData->identificationTypeId)
                    ->where('identification_number', $guestData->identificationNumber)
                    ->exists();

                if ($matchingGuestExists) {
                    throw ValidationException::withMessages([
                        'guests' => trans('reservations.validation.duplicate_guest'),
                    ]);
                }

                $guest = $hotel->guests()->create([
                    'identification_type_id' => $guestData->identificationTypeId,
                    'first_name' => $guestData->firstName,
                    'second_first_name' => $guestData->secondFirstName,
                    'last_name' => $guestData->lastName,
                    'second_last_name' => $guestData->secondLastName,
                    'identification_number' => $guestData->identificationNumber,
                    'birth_date' => $guestData->birthDate,
                    'gender' => $guestData->gender,
                    'nationality' => $guestData->nationality,
                    'residence_country' => $guestData->travel->residenceCountry,
                    'mobile' => $guestData->mobile,
                    'email' => $guestData->email,
                ]);
            }

            $guestsByKey[$guestData->key] = $guest;
        }

        return $guestsByKey;
    }
}
