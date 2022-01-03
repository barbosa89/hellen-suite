<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Constants\Modules;
use App\Models\Hotel;
use App\Models\Setting;
use App\Models\Configuration;
use Faker\Generator as Faker;

$factory->define(Setting::class, function (Faker $faker) {
    $configurableType = $faker->randomElement([Hotel::class]);
    $configurable = factory($configurableType)->create();

    $modules = [
        Hotel::class => Modules::HOTELS,
    ];

    $module = $modules[$configurableType];

    return [
        'value' => $faker->word,
        'configurable_id' => fn() => $configurable->id,
        'configurable_type' => fn() => $configurableType,
        'configuration_id' => fn() => Configuration::where('module', $module)->first()->id,
    ];
});
