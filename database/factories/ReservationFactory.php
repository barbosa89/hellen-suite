<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\ReservationStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Reservation> */
class ReservationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'responsible_guest_id' => fn (array $attributes): Guest => Guest::factory()->create([
                'hotel_id' => $attributes['hotel_id'],
            ]),
            'status' => ReservationStatus::Draft,
            'planned_check_in_on' => now()->addDay()->toDateString(),
            'planned_check_out_on' => now()->addDays(2)->toDateString(),
            'confirmed_at' => null,
            'cancelled_at' => null,
            'no_show_at' => null,
            'checked_in_at' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (): array => [
            'status' => ReservationStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
    }
}
