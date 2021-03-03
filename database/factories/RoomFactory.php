<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Room;
use App\Models\Hotel;
use Faker\Generator as Faker;

$factory->define(Room::class, function (Faker $faker) {
    return [
        'number' => $faker->randomNumber(3),
        'description' => $faker->text(200),
        'price' => $faker->numberBetween(40000, 50000),
        'min_price' => $faker->numberBetween(30000, 39000),
        'capacity' => 2,
        'floor' => 1,
        'is_suite' => 0,
        'status' => Room::AVAILABLE,
        'tax' => 0,
        'hotel_id' => function () {
            return factory(Hotel::class)->create()->id;
        },
    ];
});
