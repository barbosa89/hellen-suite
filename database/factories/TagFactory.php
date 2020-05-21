<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Welkome\Tag;
use Faker\Generator as Faker;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'description' => $faker->unique()->word
    ];
});
