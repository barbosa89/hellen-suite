<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Hotel;
use RolesTableSeeder;
use App\Models\Voucher;
use CountriesTableSeeder;
use PermissionsTableSeeder;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use NunoMaduro\LaravelMojito\InteractsWithViews;

class VoucherTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use InteractsWithViews;

    public User $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CountriesTableSeeder::class);

        $this->manager = factory(User::class)->create();
    }

    public function test_user_can_not_edit_a_voucher_without_permissions()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/search");

        $response->assertForbidden();
    }

    public function test_user_can_see_the_guest_search_form()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/search");

        $response->assertOk()
            ->assertViewIs('app.vouchers.search-guests');
    }

    public function test_user_can_not_see_the_guest_search_form_when_voucher_is_empty()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/search");

        $response->assertNotFound();
    }

    public function test_user_can_search_guests_to_add_to_voucher()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('guests.index');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->post(
                "/guests/search/unregistered?query={$guest->dni}",
                [
                    'voucher' => $voucher->hash
                ],
                [
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ],
            );

        $response->assertOk();
    }

    public function test_user_can__see_the_form_to_add_guest_to_voucher()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}");

        $response->assertOk()
            ->assertViewIs('app.vouchers.add-guests')
            ->assertViewHas('guest', $guest)
            ->assertViewHas('voucher', $voucher);
    }

    public function test_user_can_add_guest_to_voucher()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->post("vouchers/{$voucher->hash}/guests/add", [
                'guest' => $guest->hash,
                'room' => $room->hash,
            ]);

        $response->assertRedirect(route('vouchers.guests.search', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => true,
        ]);
    }

    public function test_user_can_add_guest_to_voucher_with_responsible_adult()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
            'birthdate' => now()->subYears(8),
        ]);

        /** @var Guest $adultGuest */
        $adultGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
            'birthdate' => now()->subYears(19),
        ]);

        $response = $this->actingAs($user)
            ->post("vouchers/{$voucher->hash}/guests/add", [
                'guest' => $guest->hash,
                'room' => $room->hash,
                'responsible_adult' => $adultGuest->hash,
            ]);

        $response->assertRedirect(route('vouchers.guests.search', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => true,
        ]);

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'responsible_adult' => $adultGuest->id,
        ]);
    }

    public function test_user_can_reactivate_previously_registered_guests()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
            'status' => false,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => false,
            'active' => false
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $response = $this->actingAs($user)
            ->post("vouchers/{$voucher->hash}/guests/add", [
                'guest' => $guest->hash,
                'room' => $room->hash,
            ]);

        $response->assertRedirect(route('vouchers.guests.search', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => false,
            'active' => true,
        ]);

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => true,
        ]);
    }

    public function test_user_can_remove_guest_from_voucher()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($secondaryGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}/remove");

        $response->assertRedirect(route('vouchers.show', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseMissing('guest_room', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseMissing('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => true,
        ]);
    }

    public function test_user_can_not_remove_guest_when_voucher_has_one_guest()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}/remove");

        $response->assertRedirect();

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('vouchers.onlyOne'), $message->message);
        $this->assertEquals('danger', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => true,
        ]);
    }

    public function test_user_can_not_remove_guest_when_guest_is_inactive()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => false,
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        /** @var Guest $mainGuest */
        $mainGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($mainGuest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $additionalGuest */
        $additionalGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($additionalGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}/remove");

        $response->assertRedirect();

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('vouchers.inactive.guest'), $message->message);
        $this->assertEquals('danger', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => false,
        ]);
    }

    public function test_user_can_not_remove_guest_when_room_was_delivered()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => false
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => false,
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        /** @var Guest $mainGuest */
        $mainGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($mainGuest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $additionalGuest */
        $additionalGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($additionalGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}/remove");

        $response->assertRedirect();

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('vouchers.delivered.room'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => false,
        ]);
    }
}
