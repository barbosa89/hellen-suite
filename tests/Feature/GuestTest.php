<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Guest;
use CountriesTableSeeder;
use PermissionsTableSeeder;
use Illuminate\Support\Carbon;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
    }

    public function test_guest_user_is_redirected_to_login()
    {
        $response = $this->get('/guests');

        $response->assertRedirect('/login');
    }

    public function test_unauthorized_user_can_not_access_to_guest_list()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get('/guests');

        $response->assertForbidden();
    }

    public function test_authorized_user_can_access_to_guest_list()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.index');

        $response = $this->actingAs($user)
            ->get('/guests');

        $response->assertOk()
            ->assertViewIs('app.guests.index');
    }

    public function test_authorized_user_can_see_form_to_create_guest()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        $response = $this->actingAs($user)
            ->get('/guests/create');

        $response->assertOk()
            ->assertViewIs('app.guests.create')
            ->assertViewHas('countries')
            ->assertViewHas('identificationTypes')
            ->assertViewHas('genders');
    }

    public function test_authorized_user_can_store_new_guest()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->make();

        $data = [
            'name' => $guest->name,
            'last_name' => $guest->last_name,
            'identification_type_id' => $guest->identificationType->hash,
            'dni' => $guest->dni,
            'email' => $guest->email,
            'address' => $guest->address,
            'phone' => $guest->phone,
            'gender' => $guest->gender,
            'birthdate' => $guest->birthdate,
            'profession' => $guest->profession,
            'country_id' => $guest->country->hash,
        ];

        $response = $this->actingAs($user)
            ->post('/guests', $data);

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.createdSuccessfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $data['status'] = false;
        $data['user_id'] = $user->id;
        $data['country_id'] = id_decode($data['country_id']);
        $data['identification_type_id'] = id_decode($data['identification_type_id']);

        $guest = Guest::latest('id')->first(['id']);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('guests.show', ['id' => $guest->hash]));

        $this->assertDatabaseHas('guests', $data);
        $this->assertDatabaseCount('guests', 1);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @dataProvider errorData
     */
    public function test_check_validation_error_with_wrong_data(string $field, $value)
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->make();

        $data = [
            'name' => $guest->name,
            'last_name' => $guest->last_name,
            'identification_type_id' => $guest->identificationType->hash,
            'dni' => $guest->dni,
            'email' => $guest->email,
            'address' => $guest->address,
            'phone' => $guest->phone,
            'gender' => $guest->gender,
            'birthdate' => $guest->birthdate,
            'profession' => $guest->profession,
            'country_id' => $guest->country->hash,
        ];

        $data[$field] = $value;

        $response = $this->actingAs($user)
            ->post('/guests', $data);

        $response->assertSessionHasErrors([$field]);

        $this->assertDatabaseCount('guests', 0);
    }

    public function test_user_can_store_a_guest_only_one()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'name' => $guest->name,
            'last_name' => $guest->last_name,
            'identification_type_id' => $guest->identificationType->hash,
            'dni' => $guest->dni,
            'email' => $guest->email,
            'address' => $guest->address,
            'phone' => $guest->phone,
            'gender' => $guest->gender,
            'birthdate' => $guest->birthdate,
            'profession' => $guest->profession,
            'country_id' => $guest->country->hash,
        ];

        $response = $this->actingAs($user)
            ->post('/guests', $data);

        $response->assertSessionHasErrors(['dni']);

        $this->assertDatabaseCount('guests', 1);
    }

    public function test_same_guest_can_be_stored_by_different_users()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create();

        $data = [
            'name' => $guest->name,
            'last_name' => $guest->last_name,
            'identification_type_id' => $guest->identificationType->hash,
            'dni' => $guest->dni,
            'email' => $guest->email,
            'address' => $guest->address,
            'phone' => $guest->phone,
            'gender' => $guest->gender,
            'birthdate' => $guest->birthdate,
            'profession' => $guest->profession,
            'country_id' => $guest->country->hash,
        ];

        $response = $this->actingAs($user)
            ->post('/guests', $data);

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.created.successfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $duplicateGuest = Guest::where('dni', $guest->dni)
            ->where('user_id', $user->id)
            ->first(['id']);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('guests.show', ['id' => $duplicateGuest->hash]));

        $this->assertDatabaseHas('guests', [
            'dni' => $guest->dni,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('guests', [
            'dni' => $guest->dni,
            'user_id' => $guest->user_id,
        ]);

        $this->assertDatabaseCount('guests', 2);
    }

    public function test_authorized_user_can_see_form_to_edit_guests()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.edit');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/guests/{$guest->hash}/edit");

        $response->assertOk()
            ->assertViewIs('app.guests.edit')
            ->assertViewHas('guest', function ($data) use ($guest) {
                return $data->id === $guest->id;
            })
            ->assertViewHas('countries', function ($data) use ($guest) {
                return $data->count() > 0
                    && $data->where('id', $guest->country_id)->isEmpty();
            })
            ->assertViewHas('identificationTypes', function ($data) use ($guest) {
                return $data->count() > 0
                    && $data->where('id', $guest->identification_type_id)->isEmpty();
            })
            ->assertViewHas('genders');
    }

    public function errorData(): array
    {
        return [
            'empty name' => [
                'name',
                '',
            ],
            'min name length' => [
                'name',
                'a',
            ],
            'max name length' => [
                'name',
                str_repeat('long name ', 20),
            ],
            'empty last_name' => [
                'last_name',
                '',
            ],
            'min last name length' => [
                'last_name',
                'a',
            ],
            'max last name length' => [
                'last_name',
                str_repeat('long last_name ', 20),
            ],
            'empty identification type' => [
                'identification_type_id',
                '',
            ],
            'unknown identification type' => [
                'identification_type_id',
                1000,
            ],
            'empty dni' => [
                'dni',
                '',
            ],
            'non alpha num dni' => [
                'dni',
                '-dni.',
            ],
            'short dni' => [
                'dni',
                '123',
            ],
            'non alpha num dni' => [
                'dni',
                '123456789123456789',
            ],
            'wrong email' => [
                'email',
                'dev',
            ],
            'long address' => [
                'address',
                str_repeat('long address', 200),
            ],
            'long phone number' => [
                'phone',
                '123456789123456789123',
            ],
            'wrong gender' => [
                'gender',
                'a',
            ],
            'empty gender' => [
                'gender',
                '',
            ],
            'wrong birthdate' => [
                'birthdate',
                'date',
            ],
            'wrong birthdate format' => [
                'birthdate',
                Carbon::now()->format('Y/m/d'),
            ],
            'long profession' => [
                'profession',
                str_repeat('profession', 15),
            ],
            'empty country id' => [
                'country_id',
                '',
            ],
            'unknown country id' => [
                'country_id',
                1000,
            ],
        ];
    }
}
