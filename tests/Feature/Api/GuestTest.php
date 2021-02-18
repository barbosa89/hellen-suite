<?php

namespace Tests\Feature\Api;

use App\Models\Guest;
use App\User;
use Tests\TestCase;
use RolesTableSeeder;
use CountriesTableSeeder;
use IdentificationTypesTableSeeder;
use PermissionsTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestTest extends TestCase
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

    public function test_access_is_denied_if_user_dont_have_guest_index_permissions()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get('/api/v1/web/guests');

        $response->assertForbidden();
    }

    public function test_user_can_list_guests()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('guests.index');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
            ->get('/api/v1/web/guests');

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
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('guests.index');

        /** @var Guest $oldGuest */
        $oldGuest = factory(Guest::class)->create([
            'user_id' => $manager->id,
            'created_at' => now()->subDays(8),
        ]);

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $manager->id,
            'created_at' => now()->subDays(6)
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                '/api/v1/web/guests',
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
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('guests.index');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $manager->id,
            'created_at' => now()->subDays(8),
            'status' => $status,
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                '/api/v1/web/guests',
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
                        "country_id" => (string) $guest->country_id,
                        "created_at" => $guest->created_at,
                        "dni" => (string) $guest->dni,
                        "email" => $guest->email,
                        "gender" => $guest->gender ?? null,
                        "hash" => $guest->hash,
                        "identification_type_id" => (string) $guest->identification_type_id,
                        "last_name" => $guest->last_name,
                        "name" => $guest->name,
                        "phone" => $guest->phone ?? null,
                        "profession" => $guest->profession ?? null,
                        "responsible_adult" => "0",
                        "status" => $status ? "1" : "0",
                        "updated_at" => $guest->updated_at,
                        "user_id" => (string) $guest->user_id,
                    ]
                ]
            ]);
    }

    public function filterByStatus(): array
    {
        return [
            'filter guests staying at the hotel' => [
                true,
                'is_staying'
            ],
            'filter guests who are not staying at the hotel' => [
                false,
                'is_not_staying'
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
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('guests.index');

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $manager->id,
            'created_at' => now()->subDays(8),
            'status' => $status,
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                '/api/v1/web/guests',
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
                'is_staying'
            ],
            'filter guests who are not staying at the hotel' => [
                true,
                'is_not_staying'
            ],
        ];
    }
}

