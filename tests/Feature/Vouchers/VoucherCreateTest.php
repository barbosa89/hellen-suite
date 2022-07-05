<?php

namespace Tests\Feature\Vouchers;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Country;
use App\Models\Voucher;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IdentificationTypesTableSeeder;

class VoucherCreateTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use InteractsWithViews;

    private User $manager;

    private const PERMISSION = 'vouchers.create';

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

    public function test_user_cannot_see_form_to_create_a_voucher_when_he_does_not_have_permission(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/vouchers/create');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_user_can_see_form_to_create_a_voucher(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::PERMISSION);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->call(
                'GET',
                '/vouchers/create',
                [
                    'hotel' => $hotel->hash,
                    'rooms' => [
                        $room->hash,
                    ],
                ],
            );

        $response->assertOk()
            ->assertViewIs('app.vouchers.create')
            ->assertViewHas('hotel', function ($hotel) use ($room) {
                return $hotel
                    ->rooms
                    ->where('id', $room->id)
                    ->isNotEmpty();
            });
    }

    public function test_user_cannot_see_form_to_create_a_voucher_when_params_are_wrong(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::PERMISSION);

        $response = $this->actingAs($user)
            ->call(
                'GET',
                '/vouchers/create',
            );

        $response->assertRedirect()
            ->assertSessionHasErrors(['hotel']);
    }
}


