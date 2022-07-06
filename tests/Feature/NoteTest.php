<?php

namespace Tests\Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\Note;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Shift;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AssignmentsSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Request;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    private const NOTES_ROUTE = '/notes';

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => 'manager',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'notes.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'notes.create',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('manager');
        $this->user->givePermissionTo('notes.index');
        $this->user->givePermissionTo('notes.create');

        $this->hotel = Hotel::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function test_user_can_see_form_to_search_notes(): void
    {
        $response = $this->get(self::NOTES_ROUTE);

        $response->assertOk()
            ->assertViewIs('app.notes.index');
    }

    public function test_user_can_see_notes_search_results(): void
    {
        $note = Note::factory()->create([
            'user_id' => $this->user->id,
            'hotel_id' => $this->hotel->id
        ]);

        $response = $this->call(Request::METHOD_GET, '/notes/search', [
            "hotel" => $this->hotel->hash,
            "start" => now()->subDay()->toDateString(),
            "end" => now()->toDateString(),
        ]);

        $response->assertOk()
            ->assertViewIs('app.notes.search')
            ->assertSee($note->content);
    }

    public function test_user_can_search_notes_by_text(): void
    {
        $note = Note::factory()->create([
            'content' => 'Custom content',
            'user_id' => $this->user->id,
            'hotel_id' => $this->hotel->id
        ]);

        $response = $this->call(Request::METHOD_GET, '/notes/search', [
            "hotel" => $this->hotel->hash,
            "start" => now()->subDay()->toDateString(),
            "end" => now()->toDateString(),
            "text" => "custom",
        ]);

        $response->assertOk()
            ->assertViewIs('app.notes.search')
            ->assertSee($note->content);
    }

    public function test_user_can_store_note(): void
    {
        $tags = Tag::factory(2)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->post(self::NOTES_ROUTE, [
            'hotel_id' => $this->hotel->hash,
            'content' => 'content',
            'tags' => $tags->toArray(),
            'add' => false
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => true
            ]);

        $this->assertDatabaseHas('notes', [
            'content' => '<p>content</p>',
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->user->id,
        ]);

        $noteId = Note::latest('id')->value('id');

        $this->assertDatabaseCount('note_tag', 2)
            ->assertDatabaseHas('note_tag', [
                'note_id' => $noteId,
                'tag_id' => $tags->get(0)->id,
            ])->assertDatabaseHas('note_tag', [
                'note_id' => $noteId,
                'tag_id' => $tags->get(1)->id,
            ]);
    }

    public function test_user_can_attach_note_to_shift(): void
    {
        $tags = Tag::factory(2)->for($this->user)->create();

        $response = $this->post(self::NOTES_ROUTE, [
            'hotel_id' => $this->hotel->hash,
            'content' => 'content',
            'tags' => $tags->toArray(),
            'add' => true
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => true
            ]);

        $noteId = Note::latest('id')->value('id');
        $shiftId = Shift::latest('id')->value('id');

        $this->assertDatabaseHas('note_shift', [
            'note_id' => $noteId,
            'shift_id' => $shiftId,
        ]);
    }
}
