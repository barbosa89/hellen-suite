<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Plan;
use App\Models\User;
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

        $this->seed(PermissionsTableSeeder::class);
        $this->seed(RolesTableSeeder::class);
        $this->seed(PlanSeeder::class);

        // Create hotel
        $this->hotel = Hotel::factory()->make();

        // Create plan
        $this->plan = Plan::factory()->create();
    }

    public function test_user_does_not_have_any_plans_is_redirected_to_choose()
    {
        $user = User::factory()->create();
        $user->assignRole('manager');
        $user->syncPermissions(['hotels.index', 'hotels.create']);

        $this->actingAs($user)
            ->get('/home')
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
        $user = User::factory()->create();
        $user->assignRole('manager');
        $user->syncPermissions(['hotels.index', 'hotels.create']);

        $user->plans()->attach($this->plan, ['ends_at' => now()->subDay()]);

        $this->actingAs($user)
            ->get('/home')
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
        $user = User::factory()->create();
        $user->assignRole('manager');
        $user->syncPermissions(['hotels.index', 'hotels.create']);

        $user->plans()->attach($this->plan, ['ends_at' => now()->addMonth()]);

        $this->actingAs($user)
            ->get('/home')
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
