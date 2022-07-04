<?php

namespace App;

use App\Models\VehicleType;
use Faker\Provider\ms_MY\Miscellaneous;
use Illuminate\Database\Eloquent\Factories\Factory;

class VechicleFactory extends Factory
{
    public function definition(): array
    {
        $this->faker->addProvider(new Miscellaneous($this->faker));

        return [
            'registration' => $this->faker->jpjNumberPlate,
            'brand' => $this->faker->text(8),
            'color' => $this->faker->safeColorName,
            'vehicle_type_id' => function () {
                return VehicleType::inRandomOrder()->first(['id'])->id;
            },
        ];
    }
}
