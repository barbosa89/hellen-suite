<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'business_name' => $this->faker->company,
            'tin' => $this->faker->randomNumber(6),
            'email' => $this->faker->unique()->email,
            'address' => $this->faker->streetAddress,
            'phone' => $this->faker->e164PhoneNumber,
            'mobile' => $this->faker->e164PhoneNumber,
            'is_supplier' => $this->faker->numberBetween(0, 1),
            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
