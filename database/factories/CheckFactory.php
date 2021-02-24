<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Check;
use Faker\Generator as Faker;

$factory->define(Check::class, function (Faker $faker) {
    return [
        'in_at' => now(),
        'out_at' => now(),
        'guest_id' => function () {
            return factory(\App\Models\Guest::class)->create()->id;
        },
        'voucher_id' => function () {
            return factory(\App\Models\Voucher::class)->create()->id;
        },
    ];
});
