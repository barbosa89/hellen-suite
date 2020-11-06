<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Models\IdentificationType;

$factory->define(Invoice::class, function (Faker $faker) {
    $value = $faker->numberBetween(100000, 200000);

    return [
        'number' => Str::random(12),
        'customer_name' => $faker->name,
        'customer_dni' => $faker->randomNumber(7),
        'value' => $value,
        'total' => $value,
        'status' => Invoice::PENDING,
        'identification_type_id' => IdentificationType::inRandomOrder()->first(),
        'currency_id' => Currency::where('code', Currency::COP)->first()->id
    ];
});
