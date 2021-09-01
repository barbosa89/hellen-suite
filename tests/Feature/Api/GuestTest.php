<?php

namespace Tests\Feature\Api;

use App\User;
use Tests\TestCase;
use App\Models\Guest;
use RolesTableSeeder;
use CountriesTableSeeder;
use PermissionsTableSeeder;
use Laravel\Passport\Passport;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    private string $uri = '/api/v1/guests';
    private const GUESTS_INDEX = 'guests.index';
    private const GUESTS_CREATE = 'guests.create';

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CountriesTableSeeder::class);
    }

    public function test_access_is_denied_if_user_dont_have_guest_index_permissions()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->getJson($this->uri);

        $response->assertForbidden();
    }

    public function test_user_can_list_guests()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo(self::GUESTS_INDEX);

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
        ]);

        Passport::actingAs($user);

        $response = $this->getJson($this->uri);

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => id_encode($guest->id),
                'dni' => (string) $guest->dni,
                'name' => $guest->name,
                'last_name' => $guest->last_name,
                'email' => $guest->email,
            ]);
    }

    public function test_user_can_filter_new_guests_by_date()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo(self::GUESTS_INDEX);

        /** @var Guest $oldGuest */
        $oldGuest = factory(Guest::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(8),
        ]);

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(6)
        ]);

        Passport::actingAs($user);

        $response = $this->call(
                'GET',
                $this->uri,
                [
                    'from_date' => now()->subDays(7)->format('Y-m-d'),
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => id_encode($guest->id),
                'dni' => (string) $guest->dni,
                'name' => $guest->name,
                'last_name' => $guest->last_name,
                'email' => $guest->email,
            ])
            ->assertJsonMissing([
                'hash' => id_encode($oldGuest->id),
                'dni' => (string) $oldGuest->dni,
                'name' => $oldGuest->name,
                'last_name' => $oldGuest->last_name,
                'email' => $oldGuest->email,
            ]);
    }

    /**
     * @param bool $status
     * @param string $filter
     * @dataProvider filterByStatus
     */
    public function test_user_can_filter_guests_by_status(bool $status, string $filter)
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo(self::GUESTS_INDEX);

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(8),
            'status' => $status,
        ]);

        Passport::actingAs($user);

        $response = $this->call(
                'GET',
                $this->uri,
                [
                    'status' => $filter,
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                "data" => [
                    [
                        "address" => $guest->address,
                        "banned" => "0",
                        "birthdate" => $guest->birthdate ?? null,
                        "country_hash" => $guest->country->hash,
                        "created_at" => $guest->created_at,
                        "dni" => (string) $guest->dni,
                        "email" => $guest->email,
                        "gender" => $guest->gender ?? null,
                        "hash" => $guest->hash,
                        "identification_type_hash" => $guest->identificationType->hash,
                        "last_name" => $guest->last_name,
                        "name" => $guest->name,
                        "full_name" => $guest->full_name,
                        "phone" => $guest->phone ?? null,
                        "profession" => $guest->profession ?? null,
                        "responsible_adult" => "0",
                        "status" => $status ? "1" : "0",
                        "updated_at" => $guest->updated_at,
                        "user_hash" => $user->hash,
                    ]
                ]
            ]);
    }

    public function filterByStatus(): array
    {
        return [
            'filter guests staying at the hotel' => [
                true,
                Guest::IS_STAYING,
            ],
            'filter guests who are not staying at the hotel' => [
                false,
                Guest::IS_NOT_STAYING,
            ],
        ];
    }

    /**
     * @param bool $status
     * @param string $filter
     * @dataProvider filterByOppositeStatus
     */
    public function test_user_can_not_filter_guests_with_opposite_status(bool $status, string $filter)
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo(self::GUESTS_INDEX);

        factory(Guest::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(8),
            'status' => $status,
        ]);

        Passport::actingAs($user);

        $response = $this->call(
                'GET',
                $this->uri,
                [
                    'status' => $filter,
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                "data" => []
            ]);
    }

    public function filterByOppositeStatus(): array
    {
        return [
            'filter guests staying at the hotel' => [
                false,
                Guest::IS_STAYING,
            ],
            'filter guests who are not staying at the hotel' => [
                true,
                Guest::IS_NOT_STAYING,
            ],
        ];
    }

    public function test_user_can_search_guests()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo(self::GUESTS_INDEX);

        /** @var Guest $oldGuest */
        $oldGuest = factory(Guest::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(8),
        ]);

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(6)
        ]);

        Passport::actingAs($user);

        $response = $this->call(
                'GET',
                $this->uri,
                [
                    'search' => $guest->dni,
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => $guest->hash,
                'dni' => (string) $guest->dni,
                'name' => $guest->name,
                'last_name' => $guest->last_name,
                'email' => $guest->email,
            ])
            ->assertJsonMissing([
                'hash' => $oldGuest->hash,
                'dni' => (string) $oldGuest->dni,
                'name' => $oldGuest->name,
                'last_name' => $oldGuest->last_name,
                'email' => $oldGuest->email,
            ]);
    }

    public function test_user_can_store_guest()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo(self::GUESTS_CREATE);

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

        Passport::actingAs($user);

        $response = $this->postJson($this->uri, $data);

        $jsonData = $data;
        unset($jsonData['country_id']);
        unset($jsonData['identification_type_id']);

        $jsonData['user_hash'] = $user->hash;
        $jsonData['country_hash'] = $guest->country->hash;
        $jsonData['identification_type_hash'] = $guest->identificationType->hash;

        $response->assertOk()
            ->assertSessionDoesntHaveErrors()
            ->assertJsonFragment($jsonData);

        $data['user_id'] = $user->id;
        $data['country_id'] = $guest->country_id;
        $data['identification_type_id'] = $guest->identification_type_id;

        $this->assertDatabaseCount('guests', 1);
        $this->assertDatabaseHas('guests', $data);
    }
}

