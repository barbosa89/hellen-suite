<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Country;
use App\Constants\Genders;
use App\Models\IdentificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    public function definition(): array
    {
        $genders = [Genders::FEMALE, Genders::MALE];
        $gender = $this->faker->randomElements($genders)[0];

        return [
            'dni' => $this->faker->randomNumber(7),
            'name' => $this->faker->firstName(Genders::DESCRIPTION[$gender]),
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->streetAddress,
            'phone' => $this->faker->e164PhoneNumber,
            'gender' => $gender,
            'identification_type_id' => IdentificationType::inRandomOrder()->first(['id']),
            'country_id' => Country::factory()->create(),
            'user_id' => User::factory()->create(),
        ];
    }
}
