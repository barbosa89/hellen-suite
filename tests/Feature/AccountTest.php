<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use NunoMaduro\LaravelMojito\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccountTest extends TestCase
{
    use RefreshDatabase, WithFaker, InteractsWithViews;

    public function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => 'root',
            'guard_name' => config('auth.defaults.guard')
        ]);
    }

    public function test_user_can_see_link_to_change_password()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('root');

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertView('home')
            ->hasLink(route('accounts.password.change'));
    }

    public function test_user_can_see_form_to_change_password()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
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

        /** @var \App\Models\User $user */
        $user = User::factory()->create([
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

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

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
        $this->assertFalse($message->important);
        $this->assertFalse($message->overlay);

        $user->refresh();

        $this->assertFalse(Hash::check($newPassword, $user->password));
    }
}
