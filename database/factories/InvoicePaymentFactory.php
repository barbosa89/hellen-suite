<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Models\InvoicePayment;

$factory->define(InvoicePayment::class, function (Faker $faker) {
    return [
        'number' => Str::random(12),
        'value' => $faker->randomNumber(6),
        'payment_method' => $faker->word,
        'status' => InvoicePayment::APPROVED
    ];
});
