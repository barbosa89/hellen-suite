<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\Welkome\Tag;
use App\Welkome\Note;
use RolesTableSeeder;
use UsersTableSeeder;
use App\Welkome\Hotel;
use AssignmentsSeeder;
use PermissionsTableSeeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => UsersTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => RolesTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => PermissionsTableSeeder::class]);
        Artisan::call('db:seed', ['--class' => AssignmentsSeeder::class]);

        // Create user
        $this->user = factory(User::class)->create();
        $this->user->assignRole('manager');
        $this->user->syncPermissions(Permission::all());

        // Create hotel
        $this->hotel = factory(Hotel::class)->create([
            'user_id' => $this->user->id
        ]);

        // User login
        $this->be($this->user);
    }

    public function test_user_can_see_form_to_search_notes()
    {
        $response = $this->get('/notes');

        $response->assertOk()
            ->assertViewIs('app.notes.index');
    }

    public function test_user_can_see_notes_search_results()
    {
        // Prepare note
        $note = factory(Note::class)->create([
            'user_id' => $this->user->id,
            'hotel_id' => $this->hotel->id
        ]);

        // Search params
        $start = now()->subDay();
        $end = now();
        $params = "?hotel=" . id_encode($this->hotel->id) . "&start={$start->toDateString()}&end={$end->toDateString()}";

        $response = $this->get('/notes/search' . $params);

        $response->assertOk()
            ->assertViewIs('app.notes.search')
            ->assertSee($note->content);
    }

    public function test_user_can_search_notes_by_text()
    {
        // Prepare note
        $note = factory(Note::class)->create([
            'content' => 'Custom content',
            'user_id' => $this->user->id,
            'hotel_id' => $this->hotel->id
        ]);

        // Search params
        $start = now()->subDay();
        $end = now();
        $params = "?hotel=" . id_encode($this->hotel->id) . "&start={$start->toDateString()}&end={$end->toDateString()}&text=custom";

        $response = $this->get('/notes/search' . $params);

        $response->assertOk()
            ->assertViewIs('app.notes.search')
            ->assertSee($note->content);
    }

    public function test_user_can_store_note()
    {
        $this->withoutExceptionHandling();

        $tags = factory(Tag::class, 3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->post('/notes', [
            'hotel_id' => id_encode($this->hotel->id),
            'content' => 'content',
            'tags' => $tags->toArray(),
            'add' => false
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => true
            ]);

        $this->assertDatabaseHas('notes', [
            'content' => '<p>content</p>'
        ])->assertDatabaseHas('note_tag', [
            'note_id' => 1,
            'tag_id' => 1
        ])->assertDatabaseHas('note_tag', [
            'note_id' => 1,
            'tag_id' => 2
        ])->assertDatabaseHas('note_tag', [
            'note_id' => 1,
            'tag_id' => 3
        ]);
    }

    public function test_user_can_attach_note_to_shift()
    {
        $this->withExceptionHandling();

        // Prepate tags
        $tags = factory(Tag::class, 3)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->post('/notes', [
            'hotel_id' => id_encode($this->hotel->id),
            'content' => 'content',
            'tags' => $tags->toArray(),
            'add' => true
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => true
            ]);

        $this->assertDatabaseHas('note_shift', [
            'note_id' => 1,
            'shift_id' => 1
        ]);
    }
}
