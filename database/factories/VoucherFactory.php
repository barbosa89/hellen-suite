<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Models\Hotel;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Voucher::class, function (Faker $faker) {
    $value = $faker->randomNumber(5);

    return [
        'number' => Str::random(8),
        'origin' => $faker->city,
        'destination' => $faker->city,
        'subvalue' => $value,
        'value' => $value,
        'type' => Voucher::TYPES[$faker->numberBetween(0, 5)],
        'hotel_id' => function () {
            return factory(Hotel::class)->create()->id;
        },
        'user_id' => function () {
            return factory(\App\User::class)->create()->id;
        },
    ];
});
