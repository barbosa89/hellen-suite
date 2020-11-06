<?php

namespace Tests\Feature;

use PlanSeeder;
use Tests\TestCase;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
    }

    public function test_landing_page_is_ok()
    {
        $plans = Plan::active()->get();

        $this->get('/')
            ->assertOk()
            ->assertViewIs('landing')
            ->assertViewHas('plans', $plans);
    }
}
