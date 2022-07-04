<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'price' => $this->faker->numberBetween(180000, 500000),
            'months' => $this->faker->numberBetween(2, 12),
            'type' => $this->faker->randomElement(Plan::ALL),
            'status' => true,
        ];
    }
}
