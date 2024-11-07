<?php

namespace Tests\Feature\Vouchers;

use App\Models\User;
use Database\Seeders\IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class VoucherIndexTest extends TestCase
{
    use InteractsWithViews;
    use RefreshDatabase;
    use WithFaker;

    private User $manager;

    private const PERMISSION = 'vouchers.index';

    public function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate(
            self::PERMISSION,
            config('auth.defaults.guard')
        );

        $this->seed(IdentificationTypesTableSeeder::class);

        $this->manager = User::factory()->create();

        Carbon::setTestNow(now());
    }

    public function test_user_cannot_see_voucher_list_when_he_does_not_have_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/vouchers');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_user_can_see_voucher_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::PERMISSION);

        $response = $this->actingAs($user)
            ->get('/vouchers');

        $response->assertOk()
            ->assertViewIs('app.vouchers.index');
    }
}
