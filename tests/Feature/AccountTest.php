<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use RolesTableSeeder;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AccountTest extends TestCase
{
    use RefreshDatabase, WithFaker, InteractsWithViews;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
    }

    public function test_user_can_see_link_to_change_password()
    {
        $user = factory(User::class)->create();
        $user->assignRole('root');

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertView('home')
            ->hasLink(route('accounts.password.change'));
    }

    public function test_user_can_see_form_to_change_password()
    {
        $user = factory(User::class)->create();
        $user->assignRole('root');

        $this->actingAs($user)
            ->get(route('accounts.password.change'))
            ->assertOk()
            ->assertView('app.accounts.password')
            ->hasLink(route('accounts.password.update'))
            ->has('input[type=password]#password')
            ->has('input[type=password]#new_password')
            ->has('input[type=password]#new_password_confirmation');
    }

    public function test_user_can_update_password()
    {
        $password = Str::random('8');
        $newPassword = Str::random('8');

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $user->assignRole('root');

        $this->actingAs($user)
            ->post(route('accounts.password.update'), [
                'password' => $password,
                'new_password' => $newPassword,
                'new_password_confirmation' => $newPassword,
            ])
            ->assertRedirect(route('home'));

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('accounts.password.updated'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $user->refresh();

        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    public function test_user_can_not_update_password_with_wrong_password()
    {
        $password = Str::random('8');
        $newPassword = Str::random('8');

        $user = factory(User::class)->create();

        $user->assignRole('root');

        $this->actingAs($user)
            ->from(route('accounts.password.change'))
            ->post(route('accounts.password.update'), [
                'password' => $password,
                'new_password' => $newPassword,
                'new_password_confirmation' => $newPassword,
            ])
            ->assertRedirect(route('accounts.password.change'));

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('accounts.password.wrong'), $message->message);
        $this->assertEquals('danger', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $user->refresh();

        $this->assertFalse(Hash::check($newPassword, $user->password));
    }
}
