<?php

use App\Constants\Genders;
use App\User;
use App\Models\Guest;
use App\Models\Country;
use Faker\Generator as Faker;
use App\Models\IdentificationType;

$factory->define(Guest::class, function (Faker $faker) {
    return [
        'dni' => (string) $faker->randomNumber(7),
        'name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->freeEmail,
        'address' => $faker->streetAddress,
        'phone' => $faker->e164PhoneNumber,
        'profession' => $faker->word,
        'gender' => $faker->randomElement(Genders::toArray()),
        'identification_type_id' => function () {
            return IdentificationType::inRandomOrder()->first(['id'])->id;
        },
        'country_id' => function () {
            return Country::inRandomOrder()->first(['id'])->id;
        },
        'user_id' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
