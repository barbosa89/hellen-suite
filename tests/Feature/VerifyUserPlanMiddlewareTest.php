<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use App\Models\Hotel;
use App\Constants\Roles;
use Database\Seeders\PlanSeeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\RolesTableSeeder;
use Spatie\Permission\Models\Permission;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VerifyUserPlanMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => Roles::MANAGER,
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'hotels.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'hotels.create',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->seed(PlanSeeder::class);

        $this->hotel = Hotel::factory()->make();

        $this->plan = Plan::factory()->create();
    }

    public function test_user_does_not_have_any_plans_is_redirected_to_choose()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Roles::MANAGER);
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
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Roles::MANAGER);
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

    public function test_user_has_active_plan_is_redirected_successfully(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Roles::MANAGER);
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
