<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;
use App\Models\Note;
use App\Models\Hotel;
use RolesTableSeeder;
use CountriesTableSeeder;
use PermissionsTableSeeder;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoteTest extends TestCase
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

    public function test_access_is_denied_if_user_dont_have_note_index_permissions()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/api/v1/web/hotels/{$hotel->hash}/notes");

        $response->assertForbidden();
    }

    public function test_user_can_get_note_list()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('notes.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $user->id,
        ]);

        /** @var Note $note */
        $note = factory(Note::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/api/v1/web/hotels/{$hotel->hash}/notes");

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => $note->hash,
                'content' => $note->content,
            ]);
    }
}
