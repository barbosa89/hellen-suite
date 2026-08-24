<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\HousekeepingStatus;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'room_type_id' => fn (array $attributes): RoomType => RoomType::factory()->create([
                'hotel_id' => $attributes['hotel_id'],
            ]),
            'number' => fake()->unique()->bothify('###?'),
            'floor' => fake()->numberBetween(1, 20),
            'reference_price' => fake()->randomFloat(2, 50_000, 500_000),
            'housekeeping_status' => HousekeepingStatus::Clean,
            'is_active' => true,
        ];
    }
}
