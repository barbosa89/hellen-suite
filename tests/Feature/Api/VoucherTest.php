<?php

namespace Tests\Feature\Api;

use App\Models\Hotel;
use App\User;
use Tests\TestCase;
use RolesTableSeeder;
use App\Models\Voucher;
use CountriesTableSeeder;
use PermissionsTableSeeder;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CountriesTableSeeder::class);
    }

    public function test_access_is_denied_if_user_dont_have_vouchers_index_permissions()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create();

        $hotelId = id_encode($hotel->id);

        $response = $this->actingAs($user)
            ->get("/api/v1/web/hotels/{$hotelId}/vouchers");

        $response->assertForbidden();
    }

    public function test_user_can_list_vouchers()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create();

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
        ]);

        $hotelId = id_encode($hotel->id);

        $response = $this->actingAs($manager)
            ->get("/api/v1/web/hotels/{$hotelId}/vouchers");

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => id_encode($voucher->id),
                'number' => (string) $voucher->number,
            ]);
    }

    public function test_user_can_filter_new_vouchers_by_date()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create();

        /** @var Voucher $oldVoucher */
        $oldVoucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
            'created_at' => now()->subDays(8),
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
        ]);

        $hotelId = id_encode($hotel->id);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                "/api/v1/web/hotels/{$hotelId}/vouchers",
                [
                    'from_date' => now()->subDays(7)->format('Y-m-d'),
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'number' => (string) $voucher->number,
            ])
            ->assertJsonMissing([
                'number' => (string) $oldVoucher->number,
            ]);
    }
}
