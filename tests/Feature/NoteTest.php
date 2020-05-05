<?php

namespace Tests\Feature;

use App\User;
use App\Welkome\Hotel;
use App\Welkome\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create user
        $this->user = factory(User::class)->create();

        // Create hotel
        $this->hotel = factory(Hotel::class)->create([
            'user_id' => $this->user->id
        ]);

        // User login
        $this->be($this->user);
    }

    public function test_user_can_search_notes()
    {
        $response = $this->get('/notes');

        $response->assertOk()
            ->assertViewIs('app.notes.index');
    }
}
