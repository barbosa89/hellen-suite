<?php

namespace Tests\Feature\Vouchers;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Country;
use App\Models\Voucher;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IdentificationTypesTableSeeder;

class VoucherStoreTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use InteractsWithViews;

    private User $manager;

    private const VOUCHERS_CREATE = 'vouchers.create';

    public function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate(
            self::VOUCHERS_CREATE,
            config('auth.defaults.guard')
        );

        $this->seed(IdentificationTypesTableSeeder::class);

        $this->manager = User::factory()->create();

        Carbon::setTestNow(now());
    }

    public function test_user_can_store_a_voucher(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.create');

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
            ->post('/vouchers', [
                'hotel' => $hotel->hash,
                'registry' => 'checkin',
                'origin' => $this->faker->city,
                'destination' => $this->faker->city,
                'room' => [
                    [
                        'number' => $room->number,
                        'price' => $room->price,
                        'start' => now()->format('Y-m-d'),
                        'end' => now()->addDay()->format('Y-m-d'),
                    ]
                ],
            ]);

        $voucher = Voucher::latest('id')->first();

        $response->assertRedirect(route('vouchers.guests.search', ['id' => $voucher->hash]))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseCount('vouchers', 1);

        $this->assertDatabaseCount('room_voucher', 1);

        $this->assertDatabaseHas('room_voucher', [
            'voucher_id' => $voucher->id,
            'room_id' => $room->id,
            'quantity' => 1,
            'price' => $room->price,
            'discount' => 0,
            'subvalue' => $room->price,
            'taxes' => 0,
            'value' => $room->price,
            'start' => now()->format('Y-m-d'),
            'end' => now()->addDay()->format('Y-m-d'),
            'enabled' => true,
        ]);
    }

    public function test_user_can_store_a_voucher_as_reservation(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_CREATE);

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
            ->post('/vouchers', [
                'hotel' => $hotel->hash,
                'registry' => Voucher::RESERVATION,
                'origin' => $this->faker->city,
                'destination' => $this->faker->city,
                'room' => [
                    [
                        'number' => $room->number,
                        'price' => $room->price,
                        'start' => now()->format('Y-m-d'),
                        'end' => now()->addDay()->format('Y-m-d'),
                    ]
                ],
            ]);

        $voucher = Voucher::latest('id')->first();

        $response->assertRedirect(route('vouchers.guests.search', ['id' => $voucher->hash]))
            ->assertSessionDoesntHaveErrors();

        $this->assertTrue($voucher->reservation);

        $this->assertDatabaseCount('vouchers', 1);

        $this->assertDatabaseCount('room_voucher', 1);

        $this->assertDatabaseHas('room_voucher', [
            'voucher_id' => $voucher->id,
            'room_id' => $room->id,
            'quantity' => 1,
            'price' => $room->price,
            'discount' => 0,
            'subvalue' => $room->price,
            'taxes' => 0,
            'value' => $room->price,
            'start' => now()->format('Y-m-d'),
            'end' => now()->addDay()->format('Y-m-d'),
            'enabled' => true,
        ]);
    }
}


