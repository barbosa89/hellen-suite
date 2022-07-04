<?php

namespace Tests\Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\User;
use App\Models\Hotel;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AssignmentsSeeder;
use Spatie\Permission\Models\Permission;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(UsersTableSeeder::class);
        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(AssignmentsSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('manager');
        $this->user->syncPermissions(Permission::all());

        $this->be($this->user);
    }

    public function test_user_can_get_all_tags_as_json()
    {
        $tag = Tag::factory()->for($this->user)->create();

        $response = $this->get('/tags', ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'description' => $tag->description,
                    'slug' => $tag->slug,
                    'hash' => $tag->hash,
                    'value' => $tag->description
                ]
            ]);
    }

    public function test_user_can_see_all_tags()
    {
        $tag = Tag::factory()->for($this->user)->create();

        $response = $this->get('/tags');

        $response->assertOk()
            ->assertViewIs('app.tags.index')
            ->assertSee($tag->slug);
    }

    public function test_user_can_store_tag()
    {
        $response = $this->post('/tags', [
            'tag' => 'foo',
        ]);

        $response->assertOk()
            ->assertJson([
                'value' => 'foo',
            ]);

        $this->assertDatabaseCount('tags', 1)
            ->assertDatabaseHas('tags', [
                'description' => 'foo',
                'user_id' => $this->user->id
            ]);
    }

    public function test_user_can_see_tag()
    {
        $tag = Tag::factory()->for($this->user)->create();

        $hotel = Hotel::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->get("/tags/{$tag->hash}/hotel/{$hotel->hash}");

        $response->assertOk()
            ->assertViewIs('app.tags.show')
            ->assertViewHas('tag', $tag);
    }
}
