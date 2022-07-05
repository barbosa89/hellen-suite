<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Note;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Country;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IdentificationTypesTableSeeder;

class NoteTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(IdentificationTypesTableSeeder::class);
    }

    public function test_access_is_denied_if_user_dont_have_note_index_permissions()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/api/v1/web/hotels/{$hotel->hash}/notes");

        $response->assertForbidden();
    }

    public function test_user_can_get_note_list()
    {
        Permission::findOrCreate(
            'notes.index',
            config('auth.defaults.guard')
        );

        /** @var User $user */
        $user = User::factory()->create();
        $user->givePermissionTo('notes.index');

        /** @var Hotel $hotel */
        $hotel = Hotel::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Note $note */
        $note = Note::factory()->create([
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
