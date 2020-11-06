<?php

namespace Tests\Feature;

use App\User;
use PlanSeeder;
use CurrencySeeder;
use Tests\TestCase;
use App\Models\Plan;
use RolesTableSeeder;
use App\Models\Currency;
use App\Models\IdentificationType;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class PlanTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PlanSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CurrencySeeder::class);
    }

    public function test_user_can_see_all_plans()
    {
        // Create user
        $user = factory(User::class)->create();
        $user->assignRole('root');

        $plan = Plan::first();

        $response = $this->actingAs($user)->get('/plans');

        $response->assertOk()
            ->assertViewIs('app.plans.index')
            ->assertSeeText(trans('plans.descriptions.' . $plan->getType()))
            ->assertSeeText(number_format($plan->price, 2, '.', ','))
            ->assertSeeText($plan->months)
            ->assertSee($plan->status)
            ->assertSee(route('plans.edit', ['id' => id_encode($plan->id)]));
    }

    public function test_user_can_see_plan_edition_form()
    {
        // Create user
        $user = factory(User::class)->create();
        $user->assignRole('root');

        $plan = Plan::first();

        $response = $this->actingAs($user)
            ->get(route('plans.edit', ['id' => id_encode($plan->id)]));

        $response->assertOk()
            ->assertViewIs('app.plans.edit');
    }

    public function test_user_can_update_a_plan()
    {
        // Create user
        $user = factory(User::class)->create();
        $user->assignRole('root');

        $plan = Plan::first();

        $data = [
            'price' => $this->faker->numberBetween(0, 300000),
            'months' => $this->faker->numberBetween(2, 12),
            'status' => $this->faker->numberBetween(0, 1)
        ];

        $response = $this->actingAs($user)
            ->put(route('plans.update', ['id' => id_encode($plan->id)]), $data);

        $response->assertRedirect(route('plans.index'));

        $this->assertDatabaseHas('plans', $data);
    }

    public function test_user_can_see_form_to_choose_a_plan()
    {
        // Create user
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $response = $this->actingAs($user)
            ->get(route('plans.choose'));

        $response->assertOk()
            ->assertViewIs('app.plans.choose');
    }

    public function test_user_can_choose_the_free_plan()
    {
        // Create user
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::FREE)->first();

        $response = $this->actingAs($user)
            ->get(route('plans.buy', ['id' => id_encode($plan->id)]));

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('plan_user', [
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'ends_at' => now()->addMonth()
        ]);

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('plans.ready', ['plan' => ucfirst($plan->getType())]), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);
    }

    public function test_user_can_choose_the_basic_plan()
    {
        // Create user
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::BASIC)->first();
        $types = IdentificationType::all(['id', 'type']);
        $currencies = Currency::all(['id', 'code']);

        $response = $this->actingAs($user)
            ->get(route('plans.buy', ['id' => id_encode($plan->id)]));

        $response->assertOk()
            ->assertViewIs('app.plans.buy')
            ->assertViewHas('plan', $plan)
            ->assertViewHas('currencies', $currencies)
            ->assertViewHas('types', $types);
    }

    public function test_user_can_buy_the_basic_plan()
    {
        // Create user
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::BASIC)->first();
        $types = IdentificationType::all(['id', 'type']);
        $currencies = Currency::all(['id', 'code']);

        $response = $this->actingAs($user)
            ->get(route('plans.buy', ['id' => id_encode($plan->id)]));

        $response->assertOk()
            ->assertViewIs('app.plans.buy')
            ->assertViewHas('plan', $plan)
            ->assertViewHas('currencies', $currencies)
            ->assertViewHas('types', $types);
    }
}
