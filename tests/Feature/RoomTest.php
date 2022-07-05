<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoomTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use InteractsWithViews;

    public User $manager;

    public Hotel $hotel;

    public function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => 'manager',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $this->hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id
        ]);
    }

    public function test_manager_can_see_view_to_list_rooms()
    {
        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.index');

        $this->actingAs($this->manager)
            ->get(route('rooms.index'))
            ->assertViewIs('app.rooms.index');
    }

    public function test_admin_can_see_view_to_list_rooms()
    {
        Role::create([
            'name' => 'admin',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $admin->givePermissionTo('rooms.index');

        $this->actingAs($admin)
            ->get(route('rooms.index'))
            ->assertViewIs('app.rooms.index');
    }

    public function test_accountant_can_not_see_view_to_list_rooms()
    {
        Role::create([
            'name' => 'accountant',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $accountant */
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $this->actingAs($accountant)
            ->get(route('rooms.index'))
            ->assertStatus(403);
    }

    public function test_receptionist_can_see_view_to_list_rooms()
    {
        Role::create([
            'name' => 'receptionist',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $receptionist */
        $receptionist = User::factory()->create();
        $receptionist->assignRole('receptionist');
        $receptionist->givePermissionTo('rooms.index');

        $this->actingAs($receptionist)
            ->get(route('rooms.index'))
            ->assertViewIs('app.rooms.index');
    }

    public function test_cashier_can_not_see_view_to_list_rooms()
    {
        Role::create([
            'name' => 'cashier',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $cashier */
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $this->actingAs($cashier)
            ->get(route('rooms.index'))
            ->assertStatus(403);
    }

    public function test_manager_can_get_the_list_rooms_from_api()
    {
        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.index');

        $room = Room::factory()->create([
            'user_id' => $this->manager->id,
            'hotel_id' => $this->hotel->id,
        ]);

        $this->actingAs($this->manager)
            ->get(route('api.web.rooms.index', ['hotel' => id_encode($this->hotel->id)]))
            ->assertJsonFragment([
                'rooms' => [
                    [
                        'hash' => id_encode($room->id),
                        'number' => (string) $room->number,
                        'hotel_hash' => id_encode($this->hotel->id),
                        'description' => $room->description,
                        'price' => $room->price,
                        'min_price' => $room->min_price,
                        'capacity' => 2,
                        'floor' => 1,
                        'created_at' => $room->created_at,
                        'updated_at' => $room->updated_at,
                        'is_suite' => $room->is_suite,
                        'status' => (string) $room->status,
                        'tax' => $room->tax,
                    ]
                ]
            ]);
    }

    public function test_admin_can_get_the_list_rooms_from_api()
    {
        Role::create([
            'name' => 'admin',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $admin */
        $admin = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $admin->assignRole('admin');
        $admin->givePermissionTo('rooms.index');

        $room = Room::factory()->create([
            'user_id' => $this->manager->id,
            'hotel_id' => $this->hotel->id,
        ]);

        $this->actingAs($admin)
            ->get(route('api.web.rooms.index', ['hotel' => $this->hotel->hash]))
            ->assertJsonFragment([
                'rooms' => [
                    [
                        'hash' => $room->hash,
                        'number' => (string) $room->number,
                        'hotel_hash' => $this->hotel->hash,
                        'description' => $room->description,
                        'price' => $room->price,
                        'min_price' => $room->min_price,
                        'capacity' => 2,
                        'floor' => 1,
                        'created_at' => $room->created_at,
                        'updated_at' => $room->updated_at,
                        'is_suite' => $room->is_suite,
                        'status' => (string) $room->status,
                        'tax' => $room->tax,
                    ]
                ]
            ]);
    }

    public function test_accountant_cannot_get_the_list_rooms_from_api()
    {
        Role::create([
            'name' => 'accountant',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $accountant */
        $accountant = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $accountant->assignRole('accountant');

        $this->actingAs($accountant)
            ->get(route('api.web.rooms.index', ['hotel' => id_encode($this->hotel->id)]))
            ->assertStatus(403);
    }

    public function test_receptionist_can_get_the_list_rooms_from_api()
    {
        Role::create([
            'name' => 'receptionist',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $receptionist */
        $receptionist = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $receptionist->assignRole('receptionist');
        $receptionist->givePermissionTo('rooms.index');

        $room = Room::factory()->create([
            'user_id' => $this->manager->id,
            'hotel_id' => $this->hotel->id,
        ]);

        $this->actingAs($receptionist)
            ->get(route('api.web.rooms.index', ['hotel' => $this->hotel->hash]))
            ->assertJsonFragment([
                'rooms' => [
                    [
                        'hash' => id_encode($room->id),
                        'number' => (string) $room->number,
                        'hotel_hash' => id_encode($this->hotel->id),
                        'description' => $room->description,
                        'price' => $room->price,
                        'min_price' => $room->min_price,
                        'capacity' => 2,
                        'floor' => 1,
                        'created_at' => $room->created_at,
                        'updated_at' => $room->updated_at,
                        'is_suite' => $room->is_suite,
                        'status' => (string) $room->status,
                        'tax' => $room->tax,
                    ]
                ]
            ]);
    }

    public function test_cashier_can_get_the_list_rooms_from_api()
    {
        Role::create([
            'name' => 'cashier',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $cashier */
        $cashier = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $cashier->assignRole('cashier');

        $this->actingAs($cashier)
            ->get(route('api.web.rooms.index', ['hotel' => id_encode($this->hotel->id)]))
            ->assertStatus(403);
    }

    public function test_manager_can_see_form_to_create_rooms()
    {
        Permission::create([
            'name' => 'rooms.create',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.create');

        $this->actingAs($this->manager)
            ->get(route('rooms.create'))
            ->assertViewIs('app.rooms.create')
            ->assertSee(route('rooms.store'))
            ->assertView()
            ->has('select[name=hotel_id]')
            ->has('input[name=floor]')
            ->has('input[name=number]')
            ->has('textarea[name=description]')
            ->has('select[name=is_suite]')
            ->has('input[name=price]')
            ->has('input[name=min_price]')
            ->has('input[name=capacity]')
            ->has('select[name=tax_status]')
            ->has('input[name=tax]');

    }

    public function test_manager_is_redirected_if_there_are_no_hotels()
    {
        Permission::create([
            'name' => 'rooms.create',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.create');

        Hotel::where('user_id', $this->manager->id)->delete();

        $this->actingAs($this->manager)
            ->get(route('rooms.create'))
            ->assertRedirect(route('hotels.index'));

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('hotels.no.registered'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);
    }

    public function test_manager_can_store_rooms()
    {
        Permission::create([
            'name' => 'rooms.create',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.create');

        $room = Room::factory()->make([
            'hotel_id' => $this->hotel->id,
        ]);

        $data = [
            'number' => (string) $room->number,
            'description' => $room->description,
            'price' => $room->price,
            'min_price' => $room->min_price,
            'capacity' => $room->capacity,
            'floor' => $room->floor,
            'is_suite' => (int) $room->is_suite,
            'tax' => 0.19,
            'hotel_id' => id_encode($this->hotel->id),
        ];

        $this->actingAs($this->manager)
            ->post(route('rooms.store'), array_merge($data, ['tax_status' => 1]))
            ->assertStatus(302);

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.createdSuccessfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $data['capacity'] = (string) $data['capacity'];
        $data['floor'] = (string) $data['floor'];
        $data['is_suite'] = (string) $data['is_suite'];
        $data['tax'] = (string) $data['tax'];
        $data['price'] = (string) $data['price'];
        $data['min_price'] = (string) $data['min_price'];
        $data['hotel_id'] = id_decode($data['hotel_id']);

        $this->assertDatabaseHas('rooms', $data);
    }

    public function test_manager_can_store_rooms_from_api()
    {
        Permission::create([
            'name' => 'rooms.create',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.create');

        $room = Room::factory()->make([
            'hotel_id' => $this->hotel->id,
        ]);

        $data = [
            'number' => (string) $room->number,
            'description' => $room->description,
            'price' => $room->price,
            'min_price' => $room->min_price,
            'capacity' => $room->capacity,
            'floor' => $room->floor,
            'is_suite' => (int) $room->is_suite,
            'tax' => 0.19,
            'hotel_id' => id_encode($this->hotel->id),
        ];

        $response = $this->actingAs($this->manager)
            ->post(route('api.web.rooms.store'), array_merge($data, ['tax_status' => 1]));

        $response->assertJsonFragment([
            'number' => (string) $room->number,
            'hotel_hash' => id_encode($this->hotel->id),
            'description' => $room->description,
            'price' => $room->price,
            'min_price' => $room->min_price,
            'capacity' => 2,
            'floor' => 1,
            'is_suite' => $room->is_suite,
            'status' => (string) $room->status,
            'tax' => 0.19,
        ]);

        $data['capacity'] = (string) $data['capacity'];
        $data['floor'] = (string) $data['floor'];
        $data['is_suite'] = (string) $data['is_suite'];
        $data['tax'] = (string) $data['tax'];
        $data['price'] = (string) $data['price'];
        $data['min_price'] = (string) $data['min_price'];
        $data['hotel_id'] = id_decode($data['hotel_id']);

        $this->assertDatabaseHas('rooms', $data);
    }

    public function test_manager_can_see_form_to_edit_rooms()
    {
        Permission::create([
            'name' => 'rooms.edit',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.edit');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $this->actingAs($this->manager)
            ->get(route('rooms.edit', ['id' => id_encode($room->id)]))
            ->assertViewIs('app.rooms.edit')
            ->assertViewHas('room', $room)
            ->assertSee(route('rooms.update', ['id' => id_encode($room->id)]))
            ->assertView()
            ->has('textarea[name=description]')
            ->has('select[name=is_suite]')
            ->has('input[name=price]')
            ->has('input[name=min_price]')
            ->has('input[name=capacity]')
            ->has('select[name=tax_status]')
            ->has('input[name=tax]');
    }

    public function test_manager_can_update_rooms()
    {
        Permission::create([
            'name' => 'rooms.edit',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.edit');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $data = [
            'description' => $this->faker->sentence(3),
            'price' => $room->price * 1.1,
            'min_price' => $room->min_price * 1.1,
            'capacity' => $room->capacity,
            'is_suite' => (int) $room->is_suite,
            'tax' => 0.19,
        ];

        $this->actingAs($this->manager)
            ->put(route('rooms.update', ['id' => id_encode($room->id)]), array_merge($data, ['tax_status' => 1]))
            ->assertStatus(302);

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.updatedSuccessfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $data['capacity'] = (string) $data['capacity'];
        $data['is_suite'] = (string) $data['is_suite'];
        $data['tax'] = (string) $data['tax'];
        $data['price'] = (string) $data['price'];
        $data['min_price'] = (string) $data['min_price'];

        $this->assertDatabaseHas('rooms', $data);
    }

    public function test_manager_can_see_room_details()
    {
        Permission::create([
            'name' => 'rooms.show',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.show');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $this->actingAs($this->manager)
            ->get(route('rooms.show', ['id' => id_encode($room->id)]))
            ->assertViewIs('app.rooms.show')
            ->assertViewHas('room', $room)
            ->assertSee(route('rooms.edit', ['id' => id_encode($room->id)]))
            ->assertSee(route('rooms.destroy', ['id' => id_encode($room->id)]));
    }

    public function test_manager_can_delete_rooms()
    {
        Permission::create([
            'name' => 'rooms.destroy',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.destroy');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $this->actingAs($this->manager)
            ->delete(route('rooms.destroy', ['id' => id_encode($room->id)]))
            ->assertStatus(302);

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.deletedSuccessfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id,
            'number' => $room->number,
            'hotel_id' => $room->hotel_id,
        ]);
    }

    public function test_manager_can_not_delete_room_when_it_has_vouchers()
    {
        Permission::create([
            'name' => 'rooms.destroy',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.destroy');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        $voucher = Voucher::factory()->create();

        $voucher->rooms()->attach($room, [
            'quantity' => 1,
            'price' => $room->price,
            'discount' => 0,
            'subvalue' => $room->price,
            'taxes' => 0,
            'value' => $room->price,
            'start' => now()->toDateString(),
            'end' => now()->toDateString(),
            'enabled' => true
        ]);

        $this->actingAs($this->manager)
            ->delete(route('rooms.destroy', ['id' => id_encode($room->id)]))
            ->assertRedirect(route('rooms.show', ['id' => id_encode($room->id)]));

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('rooms.cannot.destroy'), $message->message);
        $this->assertEquals('danger', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'number' => $room->number,
            'hotel_id' => $room->hotel_id,
            'status' => Room::OCCUPIED,
        ]);
    }

    public function test_user_can_search_rooms()
    {
        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.index');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $query = $room->number;

        $this->actingAs($this->manager)
            ->get(route('rooms.search') . "?query={$query}")
            ->assertViewIs('app.rooms.search')
            ->assertViewHas('query', $query)
            ->assertSee($this->hotel->business_name)
            ->assertSee($room->number)
            ->assertSee(number_format($room->price, 2, ',', '.'))
            ->assertSee($room->capacity)
            ->assertSee(trans('rooms.occupied'))
            ->assertSee(route('rooms.create'))
            ->assertSee(route('rooms.index'))
            ->assertSee(route('rooms.search'))
            ->assertSee(route('rooms.show', ['id' => id_encode($room->id)]))
            ->assertSee(route('rooms.edit', ['id' => id_encode($room->id)]))
            ->assertSee(route('rooms.destroy', ['id' => id_encode($room->id)]));
    }

    public function test_it_check_redirection_on_empty_query_search()
    {
        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.index');

        $this->actingAs($this->manager)
            ->get(route('rooms.search') . "?query=")
            ->assertRedirect(route('rooms.index'));
    }

    public function test_user_can_get_room_price_and_tax_data()
    {
        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.index');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
        ]);

        $this->actingAs($this->manager)
            ->post(route('rooms.price'), [
                'hotel' => id_encode($this->hotel->id),
                'number' => $room->number,
            ])
            ->assertJsonFragment([
                'price' => $room->price,
                'min_price' => $room->min_price,
                'tax' => $room->tax,
            ]);
    }

    public function test_user_cannot_change_room_status_when_it_is_occupied()
    {
        Permission::create([
            'name' => 'rooms.toggle',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.toggle');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        $this->actingAs($this->manager)
            ->post(route('api.web.rooms.toggle'), [
                'hotel' => id_encode($this->hotel->id),
                'room' => id_encode($room->id),
                'status' => Room::AVAILABLE,
            ])
            ->assertStatus(404);
    }

    public function test_user_can_change_room_status_to_available_when_not_occupied()
    {
        Permission::create([
            'name' => 'rooms.toggle',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.toggle');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::MAINTENANCE,
        ]);

        $this->actingAs($this->manager)
            ->post(route('api.web.rooms.toggle'), [
                'hotel' => id_encode($this->hotel->id),
                'room' => id_encode($room->id),
                'status' => Room::AVAILABLE,
            ])
            ->assertJsonFragment([
                'hash' => id_encode($room->id),
                'status' => Room::AVAILABLE,
            ]);
    }

    public function test_user_can_change_room_status_to_disabled_when_not_occupied()
    {
        Permission::create([
            'name' => 'rooms.toggle',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.toggle');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::AVAILABLE,
        ]);

        $this->actingAs($this->manager)
            ->post(route('api.web.rooms.toggle'), [
                'hotel' => id_encode($this->hotel->id),
                'room' => id_encode($room->id),
                'status' => Room::DISABLED,
            ])
            ->assertJsonFragment([
                'hash' => id_encode($room->id),
                'status' => Room::DISABLED,
            ]);
    }

    public function test_user_can_change_room_status_to_under_maintenance_when_not_occupied()
    {
        Permission::create([
            'name' => 'rooms.toggle',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.toggle');

        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::AVAILABLE,
        ]);

        $this->actingAs($this->manager)
            ->post(route('api.web.rooms.toggle'), [
                'hotel' => id_encode($this->hotel->id),
                'room' => id_encode($room->id),
                'status' => Room::MAINTENANCE,
            ])
            ->assertJsonFragment([
                'hash' => id_encode($room->id),
                'status' => Room::MAINTENANCE,
            ]);
    }

    public function test_user_can_enable_room()
    {
        Permission::create([
            'name' => 'rooms.toggle',
            'guard_name' => config('auth.defaults.guard')
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'parent' => $this->manager->id,
        ]);

        $user->givePermissionTo('rooms.toggle');

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $this->manager->id,
        ]);

        /** @var Room $room */
        $room = Room::factory()->create([
            'hotel_id' => $hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::CLEANING,
        ]);

        $response = $this->actingAs($user)
            ->post(route('api.web.rooms.toggle'), [
                'room' => $room->hash,
                'status' => Room::AVAILABLE,
            ]);

        $response->assertJsonFragment([
            'hash' => $room->hash,
            'status' => Room::AVAILABLE,
        ]);

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => Room::AVAILABLE,
        ]);
    }

    public function test_user_can_get_room_data_from_api()
    {
        $room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::AVAILABLE,
        ]);

        Permission::create([
            'name' => 'rooms.show',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->manager->givePermissionTo('rooms.show');

        $this->actingAs($this->manager)
            ->get(route('api.web.rooms.show', ['id' => id_encode($room->id)]))
            ->assertJsonFragment([
                'hash' => id_encode($room->id),
                'number' => (string) $room->number,
                'hotel_hash' => id_encode($this->hotel->id),
            ]);
    }
}
