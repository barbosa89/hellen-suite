<?php

namespace Tests\Feature\Control\Configuration;

use App\Constants\Roles;
use App\Constants\Views\Control;
use App\Models\Configuration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use RolesTableSeeder;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
    }

    public function test_guest_user_is_redirected_to_login()
    {
        $response = $this->get('/configurations');

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_without_root_role()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get('/configurations');

        $response->assertForbidden();
    }

    public function test_user_can_access_with_root_role()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(Roles::ROOT);

        /** @var Configuration $configuration */
        $configuration = factory(Configuration::class)->create();

        $response = $this->actingAs($user)
            ->get('/configurations');

        $response->assertOk()
            ->assertViewIs(Control::CONFIG_INDEX)
            ->assertViewHas('configurations', function ($data) use ($configuration) {
                return $data->first()->id === $configuration->id;
            });
    }
}
