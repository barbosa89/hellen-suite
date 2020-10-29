<?php

namespace Tests\Feature;

use App\User;
use PlanSeeder;
use Tests\TestCase;
use App\Models\Plan;
use RolesTableSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => RolesTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => PlanSeeder::class]);

        // Create user
        $this->user = factory(User::class)->create();
        $this->user->assignRole('root');

        // User login
        $this->be($this->user);
    }

    public function test_user_can_see_all_plans()
    {
        $plan = Plan::first();

        $response = $this->get('/plans');

        $response->assertOk()
            ->assertViewIs('app.plans.index')
            ->assertSeeText(trans('plans.descriptions.' . strtolower($plan->type)))
            ->assertSeeText(number_format($plan->price, 2, '.', ','))
            ->assertSeeText($plan->months)
            ->assertSee($plan->status)
            ->assertSee(route('plans.edit', ['id' => id_encode($plan->id)]));
    }

    public function test_user_can_see_plan_edition_form()
    {
        $plan = Plan::first();

        $data = [
            'price' => $this->faker->numberBetween(0, 300000),
            'months' => $this->faker->numberBetween(2, 12),
            'status' => $this->faker->numberBetween(0, 1)
        ];

        $response = $this->put(route('plans.update', ['id' => id_encode($plan->id)]), $data);

        $response->assertRedirect(route('plans.index'));

        $this->assertDatabaseHas('plans', $data);
    }
}
