<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class CreateRootUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
    }

    public function test_user_can_update_password_to_existing_root_user()
    {
        $user = User::factory()->create();
        $user->assignRole('root');

        $password = 'php_is_powerful';

        $this->artisan('root:user')
            ->expectsOutput("Existing root user: ")
            ->expectsOutput("Name: {$user->name}")
            ->expectsOutput("Email: {$user->email}" . PHP_EOL)
            ->expectsQuestion('Type your Root password: ', $password)
            ->expectsOutput("Email: {$user->email}")
            ->expectsOutput("Password: {$password}")
            ->assertExitCode(0);
    }

    public function test_user_can_create_root_user()
    {
        $user = User::factory()->make([
            'email' => 'contacto@omarbarbosa.com'
        ]);

        $password = 'php_is_powerful';

        $this->artisan('root:user')
            ->expectsQuestion('Type your Root email: ', $user->email)
            ->expectsQuestion('Type your Root password: ', $password)
            ->expectsOutput("Email: {$user->email}")
            ->expectsOutput("Password: {$password}")
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $role = Role::where('name', 'root')->first();
        $user = User::where('email', $user->email)->first();

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id
        ]);
    }

    public function test_user_can_not_create_root_user_with_fake_email()
    {
        $user = User::factory()->make([
            'email' => 'fake_account@fakerserverservice.com'
        ]);

        $this->artisan('root:user')
            ->expectsQuestion('Type your Root email: ', $user->email)
            ->expectsOutput("You cannot register the email as root user")
            ->assertExitCode(0);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email,
        ]);
    }

    public function test_user_can_create_root_user_when_password_is_short()
    {
        $user = User::factory()->make([
            'email' => 'contacto@omarbarbosa.com'
        ]);

        $password = 'short';

        $this->artisan('root:user')
            ->expectsQuestion('Type your Root email: ', $user->email)
            ->expectsQuestion('Type your Root password: ', $password)
            ->expectsOutput("Email: {$user->email}")
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $role = Role::where('name', 'root')->first();
        $user = User::where('email', $user->email)->first();

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $role->id,
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id
        ]);
    }
}
