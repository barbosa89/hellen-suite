<?php

namespace Tests\Feature;

use App\User;
use App\Welkome\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create user
        $this->user = factory(User::class)->create();

        // User login
        $this->be($this->user);
    }

    public function test_user_can_get_all_tags()
    {
        // Create tag
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/tags');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'hash' => id_encode($tag->id),
                        'value' => $tag->description,
                        'slug' => $tag->slug
                    ]
                ]
            ])->assertJsonFragment(['per_page' => 20]);

        $this->assertEquals(1, Tag::count());
    }

    public function test_user_can_store_tag()
    {
        $response = $this->post('/tags', [
            'tag' => 'foo'
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => true
            ]);

        $this->assertDatabaseHas('tags', [
            'description' => 'foo',
            'user_id' => $this->user->id
        ])->assertEquals(1, Tag::count());
    }
}
