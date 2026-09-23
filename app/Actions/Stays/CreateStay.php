<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Actions\Rooms\RoomAvailability;
use App\Constants\StayGuestRole;
use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Stay;
use App\Models\StayGuest;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function count;

final class CreateStay
{
    public function __construct(private RoomAvailability $roomAvailability) {}

    public function execute(Hotel $hotel, CreateStayData $data): Stay
    {
        return Cache::lock("hotels:{$hotel->id}:inventory", 10)->block(5, fn (): Stay => DB::transaction(function () use ($hotel, $data): Stay {
            $guestsByKey = $this->resolveGuests($hotel, $data);
            $responsibleGuest = $guestsByKey[$data->responsibleGuestKey] ?? null;

            if ($responsibleGuest === null) {
                throw ValidationException::withMessages([
                    'responsible_guest_key' => trans('stays.validation.responsible_required'),
                ]);
            }

            $checkedInAt = now();
            $stay = $hotel->stays()->create([
                'responsible_guest_id' => $responsibleGuest->id,
                'status' => StayStatus::Active,
                'checked_in_at' => $checkedInAt,
                'expected_check_out_on' => $data->expectedCheckOutOn,
            ]);

            /** @var array<string, StayGuest> $stayGuestsByKey */
            $stayGuestsByKey = [];

            foreach ($guestsByKey as $key => $guest) {
                $stayGuestsByKey[$key] = $stay->stayGuests()->create([
                    'guest_id' => $guest->id,
                    'role' => $key === $data->responsibleGuestKey
                        ? StayGuestRole::Responsible
                        : StayGuestRole::Companion,
                ]);
            }

            $this->createRoomOccupancies($hotel, $stay, $data, $stayGuestsByKey, $checkedInAt);

            return $stay;
        }));
    }

    /** @param array<string, StayGuest> $stayGuestsByKey */
    private function createRoomOccupancies(Hotel $hotel, Stay $stay, CreateStayData $data, array $stayGuestsByKey, CarbonInterface $checkedInAt): void
    {
        foreach ($data->roomOccupancies as $occupancyIndex => $occupancyData) {
            $room = $this->availableRoom($hotel, $data->expectedCheckOutOn, $occupancyData->roomId, $occupancyIndex);

            if (count($occupancyData->guestKeys) > $room->roomType->capacity) {
                throw ValidationException::withMessages([
                    "room_occupancies.{$occupancyIndex}.guest_keys" => trans('stays.validation.room_capacity'),
                ]);
            }

            $occupancy = $stay->roomOccupancies()->create([
                'room_id' => $room->id,
                'nightly_rate' => $occupancyData->nightlyRate,
                'checked_in_at' => $checkedInAt,
                'expected_check_out_on' => $data->expectedCheckOutOn,
            ]);

            $guestIds = [];

            foreach ($occupancyData->guestKeys as $guestKey) {
                $stayGuest = $stayGuestsByKey[$guestKey] ?? null;

                if ($stayGuest === null) {
                    throw ValidationException::withMessages([
                        "room_occupancies.{$occupancyIndex}.guest_keys" => trans('stays.validation.unknown_guest'),
                    ]);
                }

                $guestIds[] = $stayGuest->guest_id;
            }

            $occupancy->guests()->attach($guestIds);
        }
    }

    /** @return array<string, Guest> */
    private function resolveGuests(Hotel $hotel, CreateStayData $data): array
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
                        'guests' => trans('stays.validation.duplicate_guest'),
                    ]);
                }

                $guest = $hotel->guests()->create([
                    'identification_type_id' => $guestData->identificationTypeId,
                    'first_name' => $guestData->firstName,
                    'last_name' => $guestData->lastName,
                    'identification_number' => $guestData->identificationNumber,
                    'mobile' => $guestData->mobile,
                    'email' => $guestData->email,
                ]);
            }

            $guestsByKey[$guestData->key] = $guest;
        }

        return $guestsByKey;
    }

    private function availableRoom(Hotel $hotel, CarbonImmutable $expectedCheckOutOn, int $roomId, int $occupancyIndex): Room
    {
        $room = $this->roomAvailability
            ->query($hotel, today()->toImmutable(), $expectedCheckOutOn)
            ->with('roomType:id,capacity')
            ->lockForUpdate()
            ->find($roomId);

        if (! $room instanceof Room) {
            throw ValidationException::withMessages([
                "room_occupancies.{$occupancyIndex}.room_id" => trans('stays.validation.room_unavailable'),
            ]);
        }

        return $room;
    }
}
