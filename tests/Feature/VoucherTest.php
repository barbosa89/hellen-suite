<?php

namespace Tests\Feature;

use Mockery;
use App\User;
use Exception;
use Tests\TestCase;
use App\Models\Room;
use App\Models\Check;
use App\Models\Guest;
use App\Models\Hotel;
use RolesTableSeeder;
use App\Events\CheckIn;
use App\Models\Voucher;
use App\Events\CheckOut;
use CountriesTableSeeder;
use Illuminate\Support\Str;
use PermissionsTableSeeder;
use App\Events\RoomCheckOut;
use Illuminate\Support\Carbon;
use IdentificationTypesTableSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

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

        Carbon::setTestNow();
    }

    public function test_user_cannot_see_voucher_list_when_he_does_not_have_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/vouchers');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_user_can_see_voucher_list()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.index');

        $response = $this->actingAs($user)
            ->get('/vouchers');

        $response->assertOk()
            ->assertViewIs('app.vouchers.index');
    }

    public function test_user_cannot_see_form_to_create_a_voucher_when_he_does_not_have_permission()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/vouchers/create');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_user_can_see_form_to_create_a_voucher()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.create');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
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

    public function test_user_cannot_see_form_to_create_a_voucher_when_params_are_wrong()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.create');

        $response = $this->actingAs($user)
            ->call(
                'GET',
                '/vouchers/create',
            );

        $response->assertRedirect()
            ->assertSessionHasErrors(['hotel']);
    }

    public function test_user_can_store_a_voucher()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.create');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
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

    public function test_user_can_store_a_voucher_as_reservation()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.create');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
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

    public function test_user_cannot_edit_a_voucher_without_permissions()
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

    public function test_user_cannot_see_the_guest_search_form_when_voucher_is_empty()
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

    public function test_user_can_see_the_form_to_add_guest_to_voucher()
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
            'hotel_id' => $hotel->id,
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

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => true,
        ]);

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

        $this->assertDatabaseHas('checks', [
            'in_at' => now(),
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $route = route('vouchers.show', [
            'id' => $voucher->hash,
        ]);

        $link = "<a href='{$route}' target='_blank' rel='noopener noreferrer'>{$voucher->number}</a>";
        $type = Str::upper($guest->identificationType->type);

        $content = str_replace('{link}', $link, trans('notes.checkin.of'));
        $content .= " {$guest->full_name} {$type} {$guest->dni}, ";
        $content .= lcfirst(trans('rooms.number', ['number' => $room->number])) . '.';

        $this->assertDatabaseHas('notes', [
            'content' => $content,
            'team_member_name' => $user->name,
            'team_member_email' => $user->email,
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);
    }

    public function test_it_dispatch_check_in_event()
    {
        Event::fake();

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
            'hotel_id' => $hotel->id,
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

        Event::assertDispatched(CheckIn::class, function ($event) use ($guest) {
            return $event->guest->id == $guest->id;
        });
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
            'hotel_id' => $hotel->id,
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
            'active' => false,
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

        $this->assertDatabaseHas('checks', [
            'in_at' => now(),
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $this->assertDatabaseHas('notes', [
            'team_member_name' => $user->name,
            'team_member_email' => $user->email,
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);
    }

    public function test_it_dispatch_check_out_event()
    {
        Event::fake();

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
            'hotel_id' => $hotel->id,
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

        $now = now();

        factory(Check::class)->create([
            'in_at' => $now,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}/remove");

        Event::assertDispatched(CheckOut::class, function ($event) use ($guest) {
            return $event->guest->id == $guest->id;
        });
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
            'hotel_id' => $hotel->id,
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

        $now = now();

        factory(Check::class)->create([
            'in_at' => $now,
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}/remove");

        $response->assertRedirect(route('vouchers.show', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => false,
        ]);

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

        $this->assertDatabaseHas('checks', [
            'in_at' => $now,
            'out_at' => now(),
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $route = route('vouchers.show', [
            'id' => $voucher->hash,
        ]);

        $link = "<a href='{$route}' target='_blank' rel='noopener noreferrer'>{$voucher->number}</a>";
        $type = Str::upper($guest->identificationType->type);

        $content = str_replace('{link}', $link, trans('notes.checkout.of'));
        $content .= " {$guest->full_name} {$type} {$guest->dni}, ";
        $content .= lcfirst(trans('rooms.number', ['number' => $room->number])) . '.';

        $this->assertDatabaseHas('notes', [
            'content' => $content,
            'team_member_name' => $user->name,
            'team_member_email' => $user->email,
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);
    }

    public function test_user_cannot_remove_guest_when_voucher_has_one_guest()
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

    public function test_user_cannot_remove_guest_when_guest_is_inactive()
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

    public function test_user_cannot_remove_guest_when_room_was_delivered()
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

    public function test_user_can_toggle_guest_status_when_guest_leaves_of_hotel()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('guests.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
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
            'status' => true,
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

        factory(Check::class)->create([
            'in_at' => now(),
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $response = $this->actingAs($user)
            ->get("guests/{$guest->hash}/toggle/{$voucher->hash}");

        $response->assertRedirect(route('vouchers.show', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => false,
            'active' => false,
        ]);

        $route = route('vouchers.show', [
            'id' => $voucher->hash,
        ]);

        $link = "<a href='{$route}' target='_blank' rel='noopener noreferrer'>{$voucher->number}</a>";
        $type = Str::upper($guest->identificationType->type);

        $content = str_replace('{link}', $link, trans('notes.checkout.of'));
        $content .= " {$guest->full_name} {$type} {$guest->dni}, ";
        $content .= lcfirst(trans('rooms.number', ['number' => $room->number])) . '.';

        $this->assertDatabaseHas('notes', [
            'content' => $content,
            'team_member_name' => $user->name,
            'team_member_email' => $user->email,
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $checkOut = Check::where('guest_id', $guest->id)
            ->where('voucher_id', $voucher->id)
            ->whereNotNull('in_at')
            ->whereNotNull('out_at')
            ->first();

        $this->assertTrue($checkOut instanceof Check);
    }

    public function test_user_can_toggle_guest_status_when_room_is_active_to_enter_to_hotel()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('guests.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
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

        /** @var Guest $mainGuest */
        $mainGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($mainGuest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        factory(Check::class)->create([
            'in_at' => now(),
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $response = $this->actingAs($user)
            ->get("guests/{$guest->hash}/toggle/{$voucher->hash}");

        $response->assertRedirect(route('vouchers.show', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => true,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => false,
            'active' => true,
        ]);

        $route = route('vouchers.show', [
            'id' => $voucher->hash,
        ]);

        $link = "<a href='{$route}' target='_blank' rel='noopener noreferrer'>{$voucher->number}</a>";
        $type = Str::upper($guest->identificationType->type);

        $content = str_replace('{link}', $link, trans('notes.checkin.of'));
        $content .= " {$guest->full_name} {$type} {$guest->dni}, ";
        $content .= lcfirst(trans('rooms.number', ['number' => $room->number])) . '.';

        $this->assertDatabaseHas('notes', [
            'content' => $content,
            'team_member_name' => $user->name,
            'team_member_email' => $user->email,
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $checkOut = Check::where('guest_id', $guest->id)
            ->where('voucher_id', $voucher->id)
            ->whereNotNull('in_at')
            ->whereNull('out_at')
            ->first();

        $this->assertTrue($checkOut instanceof Check);
    }

    public function test_user_cannot_toggle_guest_status_when_room_is_disabled_to_changes()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('guests.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
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
            'active' => false
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($secondaryGuest->id, [
            'main' => false,
            'active' => false
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $response = $this->actingAs($user)
            ->get("guests/{$guest->hash}/toggle/{$voucher->hash}");

        $response->assertRedirect(route('vouchers.show', [
            'id' => $voucher->hash,
        ]));

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.error'), $message->message);
        $this->assertEquals('danger', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => false,
        ]);
    }

    public function test_user_cannot_toggle_guest_status_when_voucher_has_unique_guest()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('guests.edit');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
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
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $response = $this->actingAs($user)
            ->get("guests/{$guest->hash}/toggle/{$voucher->hash}");

        $response->assertRedirect(route('vouchers.show', [
            'id' => $voucher->hash,
        ]));

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('vouchers.onlyOne'), $message->message);
        $this->assertEquals('danger', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => true,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => true,
        ]);
    }

    public function test_user_can_close_an_open_voucher()
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.close');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
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
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($secondaryGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $secondaryGuest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $now = now();

        factory(Check::class)->create([
            'in_at' => $now,
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        factory(Check::class)->create([
            'in_at' => $now,
            'out_at' => null,
            'guest_id' => $secondaryGuest->id,
            'voucher_id' => $voucher->id,
        ]);

        $response = $this->actingAs($user)
            ->post("vouchers/{$voucher->hash}/close");

        $response->assertRedirect(route('vouchers.show', [
            'id' => $voucher->hash,
        ]));

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'open' => false,
            'status' => true,
        ]);

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('guests', [
            'id' => $secondaryGuest->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $guest->id,
            'main' => true,
            'active' => true,
        ]);

        $this->assertDatabaseHas('guest_voucher', [
            'voucher_id' => $voucher->id,
            'guest_id' => $secondaryGuest->id,
            'main' => false,
            'active' => true,
        ]);

        $route = route('vouchers.show', [
            'id' => $voucher->hash,
        ]);

        $link = "<a href='{$route}' target='_blank' rel='noopener noreferrer'>{$voucher->number}</a>";

        $content = str_replace('{link}', $link, trans('notes.checkout.of'));

        $type = Str::upper($guest->identificationType->type);
        $content .= " {$guest->full_name} {$type} {$guest->dni}, ";
        $content .= lcfirst(trans('rooms.number', ['number' => $room->number])) . ',';

        $type = Str::upper($secondaryGuest->identificationType->type);
        $content .= " {$secondaryGuest->full_name} {$type} {$secondaryGuest->dni}, ";
        $content .= lcfirst(trans('rooms.number', ['number' => $room->number])) . '.';

        $this->assertDatabaseHas('notes', [
            'content' => $content,
            'team_member_name' => $user->name,
            'team_member_email' => $user->email,
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $checkOuts = Check::whereIn('guest_id', [$guest->id, $secondaryGuest->id])
            ->where('voucher_id', $voucher->id)
            ->whereNotNull('in_at')
            ->whereNotNull('out_at')
            ->get();

        $this->assertEquals(2, $checkOuts->count());
    }

    public function test_it_dispatch_room_check_out_event_on_voucher_close()
    {
        Event::fake();

        /** @var User $user */
        $user = factory(User::class)->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('vouchers.close');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
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
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = factory(Guest::class)->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($secondaryGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $secondaryGuest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $now = now();

        factory(Check::class)->create([
            'in_at' => $now,
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        factory(Check::class)->create([
            'in_at' => $now,
            'out_at' => null,
            'guest_id' => $secondaryGuest->id,
            'voucher_id' => $voucher->id,
        ]);

        $this->actingAs($user)
            ->post("vouchers/{$voucher->hash}/close");

        Event::assertDispatched(RoomCheckOut::class, function ($event) use ($voucher) {
            return $event->voucher->id == $voucher->id;
        });
    }
}
