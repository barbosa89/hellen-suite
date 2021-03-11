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
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $response = $this->actingAs($user)
            ->get(route('plans.choose'));

        $response->assertOk()
            ->assertViewIs('app.plans.choose')
            ->assertSee(trans('plans.type.free'))
            ->assertSee(trans('plans.type.basic'))
            ->assertSee(trans('plans.type.sponsor'))
            ->assertViewHas('plans', function ($data) {
                return $data
                    ->whereIn('type', [Plan::FREE, Plan::BASIC, Plan::SPONSOR])
                    ->count() == 3;
            });
    }

    public function test_user_can_choose_the_free_plan()
    {
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

    public function test_user_can_not_see_expired_free_plan_on_choose_plan()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::FREE)->first();

        $user->plans()->attach($plan, ['ends_at' => now()->subMonth()]);

        $response = $this->actingAs($user)
            ->get(route('plans.choose'));

        $response->assertOk()
            ->assertViewIs('app.plans.choose')
            ->assertViewHas('plans', function ($data) {
                return $data
                    ->where('type', Plan::FREE)
                    ->count() == 0;
            });
    }

    public function test_user_can_buy_the_basic_plan()
    {
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

    public function test_user_can_not_renew_with_active_plans()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::BASIC)->first();

        $user->plans()->attach($plan, ['ends_at' => now()->addWeek()]);

        $response = $this->actingAs($user)
            ->get(route('plans.renew'));

        $response->assertRedirect(route('home'));
    }

    public function test_user_without_plans_is_redirected_to_choose_plans()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $response = $this->actingAs($user)
            ->get(route('plans.renew'));

        $response->assertRedirect(route('plans.choose'));
    }

    public function test_user_with_free_expired_plan_is_redirected_to_choose_plans()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::FREE)->first();

        $user->plans()->attach($plan, ['ends_at' => now()->subWeek()]);

        $response = $this->actingAs($user)
            ->get(route('plans.renew'));

        $response->assertRedirect(route('plans.choose'));
    }

    public function test_user_can_see_the_form_to_renew_plans()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $free = Plan::where('type', Plan::FREE)->first();
        $basic = Plan::where('type', Plan::BASIC)->first();

        $user->plans()->attach($free, ['ends_at' => now()->subWeek()]);
        $user->plans()->attach($basic, ['ends_at' => now()->subWeek()]);

        $response = $this->actingAs($user)
            ->get(route('plans.renew'));

        $plans = Plan::active()
            ->nonFree()
            ->get(['id', 'price', 'months', 'type', 'status']);

        $response->assertOk()
            ->assertViewIs('app.plans.choose')
            ->assertViewHas('plans', $plans)
            ->assertSeeText(trans('plans.type.basic'))
            ->assertSeeText(trans('plans.type.sponsor'));
    }
}
