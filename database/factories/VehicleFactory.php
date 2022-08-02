<?php

namespace Database\Factories;

use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration' => Str::random(6),
            'brand' => $this->faker->text(8),
            'color' => $this->faker->safeColorName,
            'vehicle_type_id' => VehicleType::inRandomOrder()->first(['id']),
        ];
    }
}
