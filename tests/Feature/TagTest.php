<?php

namespace Tests\Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\User;
use App\Models\Hotel;
use App\Constants\Roles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => Roles::MANAGER,
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'tags.index',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole(Roles::MANAGER);
        $this->user->givePermissionTo('tags.index');

        $this->be($this->user);
    }

    public function test_user_can_get_all_tags_as_json(): void
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

    public function test_user_can_see_all_tags(): void
    {
        $tag = Tag::factory()->for($this->user)->create();

        $response = $this->get('/tags');

        $response->assertOk()
            ->assertViewIs('app.tags.index')
            ->assertSee($tag->slug);
    }

    public function test_user_can_store_tag(): void
    {
        Permission::create([
            'name' => 'tags.create',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->user->givePermissionTo('tags.create');

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

    public function test_user_can_see_tag(): void
    {
        Permission::create([
            'name' => 'tags.show',
            'guard_name' => config('auth.defaults.guard')
        ]);

        $this->user->givePermissionTo('tags.show');

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
