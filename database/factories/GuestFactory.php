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
        $gender = $this->faker->randomElements($genders);


        return [
            'dni' => $this->faker->randomNumber(7),
            'name' => $this->faker->firstName(Genders::DESCRIPTION[$gender]),
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->streetAddress,
            'phone' => $this->faker->e164PhoneNumber,
            'gender' => $gender,
            'identification_type_id' => function () {
                return IdentificationType::inRandomOrder()->first(['id'])->id;
            },
            'country_id' => function () {
                return Country::inRandomOrder()->first(['id'])->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            }
        ];
    }
}
