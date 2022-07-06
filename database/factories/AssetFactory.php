<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => $this->faker->randomNumber(3),
            'description' => $this->faker->sentence(3),
            'brand' => $this->faker->word(),
            'model' => $this->faker->word(),
            'serial_number' => $this->faker->randomNumber(3),
            'price' => $this->faker->randomNumber(3),
            'room_id' => null,
            'hotel_id' => Hotel::factory()->create(),
            'user_id' => User::factory()->create(),
        ];
    }

    public function location(string $location): Factory
    {
        return $this->state(function (array $attributes) use ($location) {
            return [
                'location' => $location,
            ];
        });
    }
}
