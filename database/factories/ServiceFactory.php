<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->ingredient,
            'price' => $this->faker->randomNumber(4),
            'is_dining_service' => ceil($this->faker->numberBetween(0,1))
        ];
    }
}
