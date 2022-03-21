<?php

namespace Database\Factories;

use Bezhanov\Faker\Provider\Commerce;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $this->faker->addProvider(new Commerce($this->faker));

        return [
            'description' => $this->faker->productName,
            'price' => $this->faker->randomNumber(4),
            'quantity' => 20
        ];
    }
}
