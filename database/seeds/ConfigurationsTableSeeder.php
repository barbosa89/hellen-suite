<?php

use App\Constants\Config;
use App\Constants\Modules;
use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationsTableSeeder extends Seeder
{
    public function run(): void
    {
        Configuration::firstOrCreate(
            ['name' => Config::CHECK_OUT],
            [
                'module' => Modules::HOTELS,
                'enabled_at' => now(),
            ],
        );
    }
}
