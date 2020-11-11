<?php

namespace Tests\Feature;

use App\User;
use App\Models\Hotel;
use App\Models\Tag;
use AssignmentsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PermissionsTableSeeder;
use RolesTableSeeder;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use UsersTableSeeder;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UsersTableSeeder::class);
        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(AssignmentsSeeder::class);

        // Create user
        $this->user = factory(User::class)->create();
        $this->user->assignRole('manager');
        $this->user->syncPermissions(Permission::all());

        // User login
        $this->be($this->user);
    }

    public function test_user_can_get_all_tags_as_json()
    {
        // Create tag
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/tags', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'description' => $tag->description,
                    'slug' => $tag->slug,
                    'hash' => id_encode($tag->id),
                    'value' => $tag->description
                ]
            ]);

        $this->assertEquals(1, Tag::count());
    }

    public function test_user_can_see_all_tags()
    {
        // Create tag
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/tags');

        $tags = Tag::all();

        $response->assertOk()
            ->assertViewIs('app.tags.index')
            ->assertSee($tag->slug);
    }

    public function test_user_can_store_tag()
    {
        $response = $this->post('/tags', [
            'tag' => 'foo'
        ]);

        $response->assertOk()
            ->assertJson([
                'value' => 'foo',
                'hash' => id_encode(1)
            ]);

        $this->assertDatabaseHas('tags', [
            'description' => 'foo',
            'user_id' => $this->user->id
        ])->assertEquals(1, Tag::count());
    }

    public function test_user_can_see_tag()
    {
        $this->withExceptionHandling();

        // Create tag
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $hotel = factory(Hotel::class)->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get('/tags/' . id_encode($tag->id) . '/hotel/' . id_encode($hotel->id));

        $response->assertOk()
            ->assertViewIs('app.tags.show')
            ->assertViewHas('tag', $tag);
    }
}
