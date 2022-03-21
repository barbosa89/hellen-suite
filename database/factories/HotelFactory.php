<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'business_name' => $this->faker->text(20),
            'tin' => $this->faker->randomNumber(3) . '-' . $this->faker->randomNumber(3) . '-' . $this->faker->randomNumber(3),
            'address' => $this->faker->address,
            'phone' => $this->faker->e164PhoneNumber,
            'mobile' => $this->faker->e164PhoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
