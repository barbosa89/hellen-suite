<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Room;
use App\Models\Hotel;
use RolesTableSeeder;
use UsersTableSeeder;
use AssignmentsSeeder;
use App\Models\Voucher;
use PermissionsTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomTest extends TestCase
{
    use RefreshDatabase, WithFaker, InteractsWithViews;

    /**
     * @var \App\User
     */
    public $manager;

    /**
     * @var \App\Models\Hotel
     */
    public $hotel;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UsersTableSeeder::class);
        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(AssignmentsSeeder::class);

        // Create user
        $this->manager = User::where('email', 'manager@dev.com')->first();

        // Create hotel
        $this->hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id
        ]);
    }

    public function test_manager_can_see_view_to_list_rooms()
    {
        $this->actingAs($this->manager)
            ->get(route('rooms.index'))
            ->assertViewIs('app.rooms.index');
    }

    public function test_admin_can_see_view_to_list_rooms()
    {
        $admin = User::where('email', 'admin@dev.com')->first();

        $this->actingAs($admin)
            ->get(route('rooms.index'))
            ->assertViewIs('app.rooms.index');
    }

    public function test_accountant_can_not_see_view_to_list_rooms()
    {
        $accountant = User::where('email', 'accountant@dev.com')->first();

        $this->actingAs($accountant)
            ->get(route('rooms.index'))
            ->assertStatus(403);
    }

    public function test_receptionist_can_see_view_to_list_rooms()
    {
        $receptionist = User::where('email', 'receptionist@dev.com')->first();

        $this->actingAs($receptionist)
            ->get(route('rooms.index'))
            ->assertViewIs('app.rooms.index');
    }

    public function test_cashier_can_not_see_view_to_list_rooms()
    {
        $cashier = User::where('email', 'cashier@dev.com')->first();

        $this->actingAs($cashier)
            ->get(route('rooms.index'))
            ->assertStatus(403);
    }

    public function test_manager_can_get_the_list_rooms_from_api()
    {
        $room = factory(Room::class)->create([
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
        $admin = User::where('email', 'admin@dev.com')->first();

        $room = factory(Room::class)->create([
            'user_id' => $this->manager->id,
            'hotel_id' => $this->hotel->id,
        ]);

        $this->actingAs($admin)
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

    public function test_accountant_can_get_the_list_rooms_from_api()
    {
        $accountant = User::where('email', 'accountant@dev.com')->first();

        $this->actingAs($accountant)
            ->get(route('api.web.rooms.index', ['hotel' => id_encode($this->hotel->id)]))
            ->assertStatus(403);
    }

    public function test_receptionist_can_get_the_list_rooms_from_api()
    {
        $receptionist = User::where('email', 'receptionist@dev.com')->first();

        $room = factory(Room::class)->create([
            'user_id' => $this->manager->id,
            'hotel_id' => $this->hotel->id,
        ]);

        $this->actingAs($receptionist)
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

    public function test_cashier_can_get_the_list_rooms_from_api()
    {
        $cashier = User::where('email', 'cashier@dev.com')->first();

        $this->actingAs($cashier)
            ->get(route('api.web.rooms.index', ['hotel' => id_encode($this->hotel->id)]))
            ->assertStatus(403);
    }

    public function test_manager_can_see_form_to_create_rooms()
    {
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

    public function test_manager_can_store_rooms()
    {
        $room = factory(Room::class)->make([
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
        $room = factory(Room::class)->make([
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
        $room = factory(Room::class)->create([
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
        $room = factory(Room::class)->create([
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
        $room = factory(Room::class)->create([
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
        $room = factory(Room::class)->create([
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
        $room = factory(Room::class)->create([
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->manager->id,
            'status' => Room::OCCUPIED,
        ]);

        $voucher = factory(Voucher::class)->create();

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
}
