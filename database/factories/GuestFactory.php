<?php

use Faker\Generator as Faker;

$factory->define(App\Welkome\Guest::class, function (Faker $faker) {
    $gender = ['x', 'y'];


    return [
        'dni' => $faker->randomNumber(7),
        'name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'address' => $faker->address,
        'phone' => $faker->e164PhoneNumber,
        'identification_type_id' => function ()
        {
            return \App\Welkome\IdentificationType::inRandomOrder()->first(['id'])->id;
        },
        'country_id' => function ()
        {
            return \App\Welkome\Country::inRandomOrder()->first(['id'])->id;
        },
    ];
});
