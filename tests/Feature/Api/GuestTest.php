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
}
