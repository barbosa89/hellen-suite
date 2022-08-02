<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => $this->faker->randomNumber(3),
            'description' => $this->faker->text(200),
            'price' => $this->faker->numberBetween(40000, 50000),
            'min_price' => $this->faker->numberBetween(30000, 39000),
            'capacity' => 2,
            'floor' => 1,
            'is_suite' => 0,
            'status' => Room::AVAILABLE,
            'tax' => 0,
            'hotel_id' => Hotel::factory()->create(),
        ];
    }
}
