<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\ReservationEventType;
use App\Models\Reservation;
use App\Models\ReservationEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ReservationEvent> */
class ReservationEventFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'type' => ReservationEventType::Created,
            'before_data' => null,
            'after_data' => [],
        ];
    }
}
