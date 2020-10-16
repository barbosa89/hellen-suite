<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Hotel;
use Faker\Generator as Faker;

$factory->define(Hotel::class, function (Faker $faker) {
    return [
        'business_name' => $faker->text(20),
        'tin' => $faker->randomNumber(3) . '-' . $faker->randomNumber(3) . '-' . $faker->randomNumber(3),
        'address' => $faker->address,
        'phone' => $faker->e164PhoneNumber,
        'mobile' => $faker->e164PhoneNumber,
        'email' => $faker->unique()->safeEmail
    ];
});
