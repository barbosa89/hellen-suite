<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Check;
use App\Models\Guest;
use App\Models\Hotel;
use App\Events\CheckIn;
use App\Models\Country;
use App\Models\Voucher;
use App\Events\CheckOut;
use Illuminate\Support\Str;
use App\Events\RoomCheckOut;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IdentificationTypesTableSeeder;

class VoucherTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use InteractsWithViews;

    private User $manager;

    private const VOUCHERS_EDIT = 'vouchers.edit';

    private const VOUCHERS_CLOSE = 'vouchers.close';

    private const GUESTS_EDIT = 'guests.edit';

    public function setUp(): void
    {
        parent::setUp();

        Permission::findOrCreate(
            self::VOUCHERS_EDIT,
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            self::VOUCHERS_CLOSE,
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            self::GUESTS_EDIT,
            config('auth.defaults.guard')
        );

        $this->seed(IdentificationTypesTableSeeder::class);

        $this->manager = User::factory()->create();

        Carbon::setTestNow(now());
    }

    public function test_user_cannot_edit_a_voucher_without_permissions(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/search");

        $response->assertForbidden();
    }

    public function test_user_can_see_the_guest_search_form(): void
    {
        Permission::findOrCreate(
            self::VOUCHERS_EDIT,
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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

    public function test_user_cannot_see_the_guest_search_form_when_voucher_is_empty(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/search");

        $response->assertNotFound();
    }

    public function test_user_can_search_guests_to_add_to_voucher(): void
    {
        Permission::findOrCreate(
            'guests.index',
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('guests.index');

        /** @var Guest $guest */
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->call(
                'GET',
                '/api/v1/web/guests',
                [
                    'query_by' => $guest->dni,
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => $guest->hash,
                'dni' => (string) $guest->dni,
                'name' => $guest->name,
                'last_name' => $guest->last_name,
                'email' => $guest->email,
            ]);
    }

    public function test_user_can_see_the_form_to_add_guest_to_voucher(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $response = $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}");

        $response->assertOk()
            ->assertViewIs('app.vouchers.add-guests')
            ->assertViewHas('guest', $guest)
            ->assertViewHas('voucher', $voucher);
    }

    public function test_user_can_add_guest_to_voucher(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
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
            'in_at' => now()->format('Y-m-d H:i:s'),
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

    public function test_it_dispatch_check_in_event(): void
    {
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $this->actingAs($user)
            ->post("vouchers/{$voucher->hash}/guests/add", [
                'guest' => $guest->hash,
                'room' => $room->hash,
            ]);

        Event::assertDispatched(CheckIn::class, function ($event) use ($guest) {
            return $event->guest->id == $guest->id;
        });
    }

    public function test_user_can_add_guest_to_voucher_with_responsible_adult(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'birthdate' => now()->subYears(8),
        ]);

        /** @var Guest $adultGuest */
        $adultGuest = Guest::factory()->create([
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

    public function test_user_can_reactivate_previously_registered_guests(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
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
            'in_at' => now()->format('Y-m-d H:i:s'),
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

    public function test_it_dispatch_check_out_event(): void
    {
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($secondaryGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        Check::factory()->create([
            'in_at' => now()->format('Y-m-d H:i:s'),
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $this->actingAs($user)
            ->get("vouchers/{$voucher->hash}/guests/{$guest->hash}/remove");

        Event::assertDispatched(CheckOut::class, function ($event) use ($guest) {
            return $event->guest->id == $guest->id;
        });
    }

    public function test_user_can_remove_guest_from_voucher(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($secondaryGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        $now = now()->format('Y-m-d H:i:s');

        Check::factory()->create([
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
            'out_at' => now()->format('Y-m-d H:i:s'),
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

    public function test_user_cannot_remove_guest_when_voucher_has_one_guest(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
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

    public function test_user_cannot_remove_guest_when_guest_is_inactive(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
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
        $mainGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($mainGuest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $additionalGuest */
        $additionalGuest = Guest::factory()->create([
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

    public function test_user_cannot_remove_guest_when_room_was_delivered(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
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
        $mainGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($mainGuest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $additionalGuest */
        $additionalGuest = Guest::factory()->create([
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

    public function test_user_can_toggle_guest_status_when_guest_leaves_of_hotel(): void
    {
        Permission::findOrCreate(
            self::GUESTS_EDIT,
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::GUESTS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($secondaryGuest->id, [
            'main' => false,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        Check::factory()->create([
            'in_at' => now()->format('Y-m-d H:i:s'),
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

    public function test_user_can_toggle_guest_status_when_room_is_active_to_enter_to_hotel(): void
    {
        Permission::findOrCreate(
            self::GUESTS_EDIT,
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::GUESTS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => false,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => false,
            'active' => false
        ]);

        /** @var Guest $mainGuest */
        $mainGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($mainGuest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        Check::factory()->create([
            'in_at' => now()->format('Y-m-d H:i:s'),
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

    public function test_user_cannot_toggle_guest_status_when_room_is_disabled_to_changes(): void
    {
        Permission::findOrCreate(
            self::GUESTS_EDIT,
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::GUESTS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => false
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = Guest::factory()->create([
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

    public function test_user_cannot_toggle_guest_status_when_voucher_has_unique_guest(): void
    {
        Permission::findOrCreate(
            self::GUESTS_EDIT,
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::GUESTS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
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

    public function test_user_can_close_an_open_voucher(): void
    {
        Permission::findOrCreate(
            self::VOUCHERS_CLOSE,
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_CLOSE);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = Guest::factory()->create([
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

        $now = now()->format('Y-m-d H:i:s');

        Check::factory()->create([
            'in_at' => $now,
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        Check::factory()->create([
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

    public function test_it_dispatch_room_check_out_event_on_voucher_close(): void
    {
        Event::fake();

        Permission::findOrCreate(
            self::VOUCHERS_CLOSE,
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_CLOSE);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
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
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        /** @var Guest $secondaryGuest */
        $secondaryGuest = Guest::factory()->create([
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

        $now = now()->format('Y-m-d H:i:s');

        Check::factory()->create([
            'in_at' => $now,
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        Check::factory()->create([
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

    public function test_user_can_see_form_to_change_assigned_room_to_any_available_room_in_hotel(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $assignedRoom */
        $assignedRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        /** @var Room $availableRoom */
        $availableRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $assignedRoom->id,
            [
                'price' => $assignedRoom->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $assignedRoom->price,
                'taxes' => 0,
                'value' => $assignedRoom->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($assignedRoom, [
            'voucher_id' => $voucher->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('vouchers.rooms.change.form', [
                'id' => $voucher->hash,
                'room' => $assignedRoom->hash,
            ]));

        $response->assertOk()
            ->assertViewIs('app.vouchers.change-room')
            ->assertViewHas('voucher', function ($data) use ($voucher) {
                return $data->id === $voucher->id;
            })
            ->assertViewHas('rooms', function ($data) use ($availableRoom) {
                return $data->count() === 1
                    && $data->first()->id == $availableRoom->id;
            })
            ->assertViewHas('room', function ($data) use ($assignedRoom) {
                return $data->id == $assignedRoom->id;
            });
    }

    public function test_user_can_change_assigned_room_to_any_available_room(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $assignedRoom */
        $assignedRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        /** @var Room $availableRoom */
        $availableRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $voucher->rooms()->attach(
            $assignedRoom->id,
            [
                'price' => $assignedRoom->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $assignedRoom->price,
                'taxes' => 0,
                'value' => $assignedRoom->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($assignedRoom, [
            'voucher_id' => $voucher->id
        ]);

        $data = [
            'number' => (string) $availableRoom->number,
            'price' => $availableRoom->price,
            'hotel' => $hotel->hash,
        ];

        $response = $this->actingAs($user)
            ->post(route('vouchers.rooms.change', [
                'id' => $voucher->hash,
                'room' => $assignedRoom->hash,
            ]), $data);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('vouchers.show', ['id' => $voucher->hash]));

        $this->assertDatabaseHas('room_voucher', [
            'voucher_id' => $voucher->id,
            'room_id' => $availableRoom->id,
            'price' => $availableRoom->price,
            'quantity' => 1,
            'discount' => 0,
            'subvalue' => $availableRoom->price,
            'taxes' => 0,
            'value' => $availableRoom->price,
            'start' => now()->toDateString(),
            'end' => now()->toDateString(),
            'enabled' => true
        ]);

        $this->assertDatabaseMissing('room_voucher', [
            'voucher_id' => $voucher->id,
            'room_id' => $assignedRoom->id,
        ]);

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'room_id' => $availableRoom->id,
            'guest_id' => $guest->id,
        ]);

        $this->assertDatabaseMissing('guest_room', [
            'voucher_id' => $voucher->id,
            'room_id' => $assignedRoom->id,
            'guest_id' => $guest->id,
        ]);

        $this->assertDatabaseHas('rooms', [
            'id' => $assignedRoom->id,
            'status' => Room::CLEANING,
        ]);

        $this->assertDatabaseHas('rooms', [
            'id' => $availableRoom->id,
            'status' => Room::OCCUPIED,
        ]);
    }

    public function test_user_can_see_form_to_change_guest_to_any_available_room_in_voucher(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $assignedRoom */
        $assignedRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        /** @var Room $availableRoom */
        $availableRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        $voucher->rooms()->attach(
            $assignedRoom->id,
            [
                'price' => $assignedRoom->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $assignedRoom->price,
                'taxes' => 0,
                'value' => $assignedRoom->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        $voucher->rooms()->attach(
            $availableRoom->id,
            [
                'price' => $availableRoom->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $availableRoom->price,
                'taxes' => 0,
                'value' => $availableRoom->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($assignedRoom, [
            'voucher_id' => $voucher->id
        ]);

        /** @var Guest $anotherGuest */
        $anotherGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($anotherGuest->id, [
            'main' => true,
            'active' => true
        ]);

        $anotherGuest->rooms()->attach($assignedRoom, [
            'voucher_id' => $voucher->id
        ]);

        $response = $this->actingAs($user)
            ->get(route('vouchers.guests.change.form', [
                'id' => $voucher->hash,
                'guest' => $guest->hash,
            ]));

        $response->assertSessionHasNoErrors()
            ->assertOk()
            ->assertViewIs('app.vouchers.change-guest-room')
            ->assertViewHas('voucher', function ($data) use ($voucher, $assignedRoom, $availableRoom) {
                return $data->id === $voucher->id
                    && $data->rooms->whereIn('id', [
                        $assignedRoom->id,
                        $availableRoom->id,
                    ])->count() === 2;
            })
            ->assertViewHas('room', function ($data) use ($assignedRoom) {
                return $data->id == $assignedRoom->id;
            })
            ->assertViewHas('guest', function ($data) use ($guest) {
                return $data->id == $guest->id;
            });
    }

    public function test_user_can_change_guest_to_any_available_room_in_voucher(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo(self::VOUCHERS_EDIT);

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = Voucher::factory()->create([
            'open' => true,
            'status' => true,
            'user_id' => $this->manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $assignedRoom */
        $assignedRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        /** @var Room $availableRoom */
        $availableRoom = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        $voucher->rooms()->attach(
            $assignedRoom->id,
            [
                'price' => $assignedRoom->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $assignedRoom->price,
                'taxes' => 0,
                'value' => $assignedRoom->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        $voucher->rooms()->attach(
            $availableRoom->id,
            [
                'price' => $availableRoom->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $availableRoom->price,
                'taxes' => 0,
                'value' => $availableRoom->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true
        ]);

        $guest->rooms()->attach($assignedRoom, [
            'voucher_id' => $voucher->id
        ]);

        /** @var Guest $anotherGuest */
        $anotherGuest = Guest::factory()->create([
            'user_id' => $this->manager->id,
            'status' => true,
        ]);

        $voucher->guests()->attach($anotherGuest->id, [
            'main' => true,
            'active' => true
        ]);

        $anotherGuest->rooms()->attach($assignedRoom, [
            'voucher_id' => $voucher->id
        ]);

        $data = [
            'number' => (string) $availableRoom->number,
        ];

        $response = $this->actingAs($user)
            ->post(route('vouchers.guests.change', [
                'id' => $voucher->hash,
                'guest' => $guest->hash,
            ]), $data);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('vouchers.show', ['id' => $voucher->hash]));

        $this->assertDatabaseHas('guest_room', [
            'voucher_id' => $voucher->id,
            'room_id' => $availableRoom->id,
            'guest_id' => $guest->id,
        ]);

        $this->assertDatabaseMissing('guest_room', [
            'voucher_id' => $voucher->id,
            'room_id' => $assignedRoom->id,
            'guest_id' => $guest->id,
        ]);
    }
}


