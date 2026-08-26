<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomOccupancy>
 */
class RoomOccupancyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stay_id' => Stay::factory(),
            'room_id' => Room::factory(),
            'nightly_rate' => fake()->randomFloat(2, 50_000, 500_000),
            'checked_in_at' => now(),
            'expected_check_out_on' => now()->addDay()->toDateString(),
            'checked_out_at' => null,
            'end_reason' => null,
        ];
    }
}
