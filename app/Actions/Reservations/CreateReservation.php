<?php

declare(strict_types=1);

namespace App\Actions\Reservations;

use App\Constants\ReservationEventType;
use App\Data\Reservations\ReservationData;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class CreateReservation
{
    public function __construct(
        private SaveReservation $saveReservation,
        private ReservationSnapshot $reservationSnapshot,
    ) {}

    public function execute(Hotel $hotel, ReservationData $data): Reservation
    {
        return Cache::lock("hotels:{$hotel->id}:inventory", 10)->block(5, fn (): Reservation => DB::transaction(function () use ($hotel, $data): Reservation {
            $reservation = $this->saveReservation->execute($hotel, $data);

            $reservation->events()->create([
                'type' => ReservationEventType::Created,
                'after_data' => $this->reservationSnapshot->execute($reservation),
            ]);

            return $reservation;
        }));
    }
}
