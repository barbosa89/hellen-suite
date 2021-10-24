<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Country;
use App\Models\Voucher;
use CountriesTableSeeder;
use PermissionsTableSeeder;
use App\Exports\GuestsReport;
use Illuminate\Support\Carbon;
use App\Models\IdentificationType;
use IdentificationTypesTableSeeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestTest extends TestCase
{
    use WithFaker;
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
        /** @var \App\User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get('/guests');

        $response->assertForbidden();
    }

    public function test_authorized_user_can_access_to_guest_list()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.index');

        $response = $this->actingAs($user)
            ->get('/guests');

        $response->assertOk()
            ->assertViewIs('app.guests.index');
    }

    public function test_authorized_user_can_see_form_to_create_guest()
    {
        /** @var \App\User $user */
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
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var \App\Models\Guest $guest */
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
     * @dataProvider validationProvider
     */
    public function test_check_validation_error_with_wrong_data(string $field, $value)
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var \App\Models\Guest $guest */
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
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var \App\Models\Guest $guest */
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
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.create');

        /** @var \App\Models\Guest $guest */
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

    public function test_authorized_user_can_see_a_guest()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.show');

        /** @var \App\Models\Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
        ]);

        $voucher->guests()->attach($guest->id, ['active' => true]);

        $response = $this->actingAs($user)
            ->get("/guests/{$guest->hash}");

        $response->assertOk()
            ->assertViewIs('app.guests.show')
            ->assertViewHas('guest', function ($data) use ($guest, $voucher, $hotel) {
                return $data->id === $guest->id
                    && $data->country->id === $guest->country_id
                    && $data->vouchers->first()->hash === $voucher->hash
                    && $data->vouchers->first()->hotel->hash === $hotel->hash;
            });
    }

    public function test_unauthorized_user_cannot_see_a_guest()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.show');

        /** @var \App\Models\Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\User $other */
        $other = factory(User::class)->create();
        $other->givePermissionTo('guests.show');

        $response = $this->actingAs($other)
            ->get("/guests/{$guest->hash}");

        $response->assertNotFound();
    }

    public function test_authorized_user_can_see_form_to_edit_guests()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.edit');

        /** @var \App\Models\Guest $guest */
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

    public function test_authorized_user_can_update_a_guest()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.edit');

        /** @var \App\Models\Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Country $country */
        $country = Country::inRandomOrder()->first(['id']);

        /** @var \App\Models\IdentificationType $identificationType */
        $identificationType = IdentificationType::inRandomOrder()->first(['id']);

        $data = [
            'name' => $this->faker->name,
            'last_name' => $this->faker->lastName,
            'identification_type_id' => $identificationType->hash,
            'dni' => (string) $this->faker->randomNumber(7),
            'email' => $this->faker->unique()->freeEmail,
            'address' => $this->faker->streetAddress,
            'phone' => $this->faker->e164PhoneNumber,
            'gender' => $guest->gender,
            'birthdate' => now()->subYears(20)->format('Y-m-d'),
            'profession' => $this->faker->word,
            'country_id' => $country->hash,
        ];

        $response = $this->actingAs($user)
            ->put("/guests/{$guest->hash}", $data);

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.updatedSuccessfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $data['id'] = $guest->id;
        $data['status'] = false;
        $data['user_id'] = $user->id;
        $data['country_id'] = $country->id;
        $data['identification_type_id'] = $identificationType->id;

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('guests.show', ['id' => $guest->hash]));

        $this->assertDatabaseHas('guests', $data);
        $this->assertDatabaseCount('guests', 1);
    }

    public function test_authorized_user_can_delete_a_guest()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.destroy');

        /** @var \App\Models\Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/guests/{$guest->hash}");

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.deletedSuccessfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('guests.index'));

        $this->assertDatabaseMissing('guests', [
            'id' => $guest->id,
        ]);
    }

    public function test_authorized_user_can_download_guest_report()
    {
        Excel::fake();

        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.index');

        /** @var \App\Models\Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get("/guests/export");

        Excel::assertDownloaded('Guests.xlsx', function(GuestsReport $export) use ($guest) {
            $view = $export->view()->render();

            return str_contains($view, $guest->dni);
        });
    }

    public function test_authorized_user_can_not_download_when_guests_table_is_empty()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.index');

        $response = $this->actingAs($user)
            ->get("/guests/export");

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.noRecords'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $response->assertRedirect(route('guests.index'));
    }

    public function test_authorized_user_can_not_delete_a_guest_if_has_vouchers()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('guests.destroy');

        /** @var \App\Models\Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var \App\Models\Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'user_id' => $user->id,
        ]);

        $voucher->guests()->attach($guest->id, ['active' => true]);

        $response = $this->actingAs($user)
            ->delete("/guests/{$guest->hash}");

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.notRemovable'), $message->message);
        $this->assertEquals('info', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('guests.show', ['id' => $guest->hash]));

        $this->assertDatabaseHas('guests', [
            'id' => $guest->id,
        ]);
    }

    public function validationProvider(): array
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
