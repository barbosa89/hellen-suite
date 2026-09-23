<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\ReservedRoom;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ReservedRoom> */
class ReservedRoomFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'room_id' => function (array $attributes): int {
                $reservation = Reservation::query()->findOrFail($attributes['reservation_id']);
                $roomType = RoomType::factory()->for($reservation->hotel)->create();

                return Room::factory()->for($reservation->hotel)->for($roomType)->create()->id;
            },
            'nightly_rate' => fake()->randomFloat(2, 50_000, 500_000),
            'planned_check_in_on' => fn (array $attributes): string => Reservation::query()->findOrFail($attributes['reservation_id'])->planned_check_in_on->toDateString(),
            'planned_check_out_on' => fn (array $attributes): string => Reservation::query()->findOrFail($attributes['reservation_id'])->planned_check_out_on->toDateString(),
        ];
    }
}
