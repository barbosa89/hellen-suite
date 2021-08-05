<?php

use App\Constants\Config;
use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Config::toArray() as $config) {
            if (!Configuration::where('name', $config)->exists()) {
                Configuration::create(['name' => $config]);
            }
        }
    }
}
