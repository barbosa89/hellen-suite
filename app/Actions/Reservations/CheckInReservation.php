<?php

declare(strict_types=1);

namespace App\Actions\Reservations;

use App\Actions\Rooms\RoomAvailability;
use App\Actions\Stays\CreateStayFolio;
use App\Constants\ReservationEventType;
use App\Constants\ReservationStatus;
use App\Constants\StayStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Stay;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckInReservation
{
    public function __construct(
        private RoomAvailability $roomAvailability,
        private ReservationSnapshot $reservationSnapshot,
        private CreateStayFolio $createStayFolio,
        private GeneralSettings $settings,
    ) {}

    public function execute(Hotel $hotel, Reservation $reservation): Stay
    {
        return Cache::lock("hotels:{$hotel->id}:inventory", 10)->block(5, fn (): Stay => DB::transaction(function () use ($hotel, $reservation): Stay {
            $reservation = $hotel->reservations()
                ->with([
                    'reservationGuests',
                    'reservedRooms.reservationGuests',
                ])
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            if (! $this->canCheckIn($reservation)) {
                throw ValidationException::withMessages([
                    'reservation' => trans('reservations.validation.cannot_check_in'),
                ]);
            }

            $availableRoomCount = $this->roomAvailability
                ->query($hotel, today()->toImmutable(), $reservation->planned_check_out_on->toImmutable(), $reservation)
                ->whereKey($reservation->reservedRooms->pluck('room_id'))
                ->lockForUpdate()
                ->count();

            if ($availableRoomCount !== $reservation->reservedRooms->count()) {
                throw ValidationException::withMessages([
                    'reserved_rooms' => trans('reservations.validation.room_unavailable_at_check_in'),
                ]);
            }

            $before = $this->reservationSnapshot->execute($reservation);
            $checkedInAt = now();
            $stay = $hotel->stays()->create([
                'reservation_id' => $reservation->id,
                'responsible_guest_id' => $reservation->responsible_guest_id,
                'status' => StayStatus::Active,
                'currency' => $this->settings->currency,
                'checked_in_at' => $checkedInAt,
                'expected_check_out_on' => $reservation->planned_check_out_on,
            ]);

            foreach ($reservation->reservationGuests as $reservationGuest) {
                $stay->stayGuests()->create([
                    'guest_id' => $reservationGuest->guest_id,
                    'role' => $reservationGuest->role,
                ]);
            }

            foreach ($reservation->reservedRooms as $reservedRoom) {
                $occupancy = $stay->roomOccupancies()->create([
                    'room_id' => $reservedRoom->room_id,
                    'nightly_rate' => $reservedRoom->nightly_rate,
                    'checked_in_at' => $checkedInAt,
                    'expected_check_out_on' => $reservation->planned_check_out_on,
                ]);

                $occupancy->guests()->attach(
                    $reservedRoom->reservationGuests
                        ->map(fn ($reservationGuest): int => $reservationGuest->guest_id)
                        ->all(),
                );

                $this->createStayFolio->execute($occupancy);
            }

            $reservation->update([
                'status' => ReservationStatus::CheckedIn,
                'checked_in_at' => $checkedInAt,
            ]);
            $reservation->events()->create([
                'type' => ReservationEventType::CheckedIn,
                'before_data' => $before,
                'after_data' => $this->reservationSnapshot->execute($reservation->refresh()),
            ]);

            return $stay;
        }));
    }

    private function canCheckIn(Reservation $reservation): bool
    {
        return $reservation->status === ReservationStatus::Confirmed
            && ! $reservation->planned_check_in_on->isAfter(today())
            && $reservation->planned_check_out_on->isAfter(today())
            && ! $reservation->stay()->exists();
    }
}
