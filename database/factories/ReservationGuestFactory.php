<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\StayGuestRole;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\ReservationGuest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ReservationGuest> */
class ReservationGuestFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'guest_id' => fn (array $attributes): Guest => Guest::factory()->create([
                'hotel_id' => Reservation::query()->findOrFail($attributes['reservation_id'])->hotel_id,
            ]),
            'role' => StayGuestRole::Companion,
        ];
    }
}
