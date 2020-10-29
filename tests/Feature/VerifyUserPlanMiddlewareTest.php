<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Plan;
use App\User;
use PlanSeeder;
use Tests\TestCase;
use RolesTableSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PermissionsTableSeeder;

class VerifyUserPlanMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => PermissionsTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => RolesTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => PlanSeeder::class]);

        // Create hotel
        $this->hotel = factory(Hotel::class)->make();

        // Create plan
        $this->plan = factory(Plan::class)->create();
    }

    public function test_user_does_not_have_any_plans_is_redirected_to_choose()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');
        $user->syncPermissions(['hotels.index', 'hotels.create']);

        $this->actingAs($user)
            ->get('/hotels')
            ->assertRedirect(route('plans.choose'));

        $this->actingAs($user)
            ->get('/hotels/create')
            ->assertRedirect(route('plans.choose'));

        $this->actingAs($user)
            ->post('/hotels', [
                'business_name' => $this->hotel->business_name,
                'tin' => $this->hotel->tin,
                'address' => $this->hotel->address,
                'phone' => $this->hotel->phone,
                'mobile' => $this->hotel->mobile,
                'email' => $this->hotel->email,
            ])
            ->assertRedirect(route('plans.choose'));
    }

    public function test_user_has_expired_plan_is_redirected_to_renew()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');
        $user->syncPermissions(['hotels.index', 'hotels.create']);

        $user->plans()->attach($this->plan, ['ends_at' => now()->subDay()]);

        $this->actingAs($user)
            ->get('/hotels')
            ->assertRedirect(route('plans.renew'));

        $this->actingAs($user)
            ->get('/hotels/create')
            ->assertRedirect(route('plans.renew'));

        $this->actingAs($user)
            ->post('/hotels', [
                'business_name' => $this->hotel->business_name,
                'tin' => $this->hotel->tin,
                'address' => $this->hotel->address,
                'phone' => $this->hotel->phone,
                'mobile' => $this->hotel->mobile,
                'email' => $this->hotel->email,
            ])
            ->assertRedirect(route('plans.renew'));
    }

    public function test_user_has_active_plan_is_redirected_successfully()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');
        $user->syncPermissions(['hotels.index', 'hotels.create']);

        $user->plans()->attach($this->plan, ['ends_at' => now()->addMonth()]);

        $this->actingAs($user)
            ->get('/hotels')
            ->assertOk();

        $this->actingAs($user)
            ->get('/hotels/create')
            ->assertOk();

        $this->actingAs($user)
            ->followingRedirects()
            ->post('/hotels', [
                'business_name' => $this->hotel->business_name,
                'tin' => $this->hotel->tin,
                'address' => $this->hotel->address,
                'phone' => $this->hotel->phone,
                'mobile' => $this->hotel->mobile,
                'email' => $this->hotel->email,
            ])
            ->assertOk();
    }
}
