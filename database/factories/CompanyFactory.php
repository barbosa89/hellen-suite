<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'business_name' => $faker->company,
        'tin' => $faker->randomNumber(6),
        'email' => $faker->unique()->email,
        'address' => $faker->streetAddress,
        'phone' => $faker->e164PhoneNumber,
        'mobile' => $faker->e164PhoneNumber,
        'is_supplier' => $faker->numberBetween(0, 1),
        'user_id' => function () {
            return factory(\App\User::class)->create()->id;
        },
    ];
});
