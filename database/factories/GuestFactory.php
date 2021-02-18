<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Guest::class, function (Faker $faker) {
    $gender = ['x', 'y'];


    return [
        'dni' => $faker->randomNumber(7),
        'name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'address' => $faker->streetAddress,
        'phone' => $faker->e164PhoneNumber,
        'identification_type_id' => function () {
            return \App\Models\IdentificationType::inRandomOrder()->first(['id'])->id;
        },
        'country_id' => function () {
            return \App\Models\Country::inRandomOrder()->first(['id'])->id;
        },
    ];
});
