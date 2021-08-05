<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Constants\Config;
use ConfigurationsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfigurationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_configurations_were_successfully_seeded()
    {
        $this->seed(ConfigurationsTableSeeder::class);

        $this->assertDatabaseCount('configurations', count(Config::toArray()));
    }

    public function test_seeder_does_not_duplicate_records()
    {
        $this->seed(ConfigurationsTableSeeder::class);
        $this->seed(ConfigurationsTableSeeder::class);

        $this->assertDatabaseCount('configurations', count(Config::toArray()));
    }
}
