<?php

declare(strict_types=1);

namespace App\Actions\Reservations;

use App\Constants\ReservationEventType;
use App\Constants\ReservationStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function in_array;

final class CancelReservation
{
    public function __construct(private ReservationSnapshot $reservationSnapshot) {}

    public function execute(Hotel $hotel, Reservation $reservation): Reservation
    {
        return Cache::lock("hotels:{$hotel->id}:inventory", 10)->block(5, fn (): Reservation => DB::transaction(function () use ($hotel, $reservation): Reservation {
            $reservation = $hotel->reservations()->lockForUpdate()->findOrFail($reservation->id);

            if (! in_array($reservation->status, [ReservationStatus::Draft, ReservationStatus::Confirmed], true)) {
                throw ValidationException::withMessages([
                    'reservation' => trans('reservations.validation.cannot_cancel'),
                ]);
            }

            $before = $this->reservationSnapshot->execute($reservation);

            $reservation->update([
                'status' => ReservationStatus::Cancelled,
                'cancelled_at' => now(),
            ]);

            $reservation->events()->create([
                'type' => ReservationEventType::Cancelled,
                'before_data' => $before,
                'after_data' => $this->reservationSnapshot->execute($reservation->refresh()),
            ]);

            return $reservation;
        }));
    }
}
