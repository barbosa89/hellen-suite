<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');
    }

    /**
     * @dataProvider privilegedRoleProvider
     */
    public function test_manager_cannot_create_a_team_member_with_a_privileged_role(string $role): void
    {
        $response = $this->actingAs($this->manager)
            ->post(route('team.store'), ['role' => $role]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * @dataProvider privilegedRoleProvider
     */
    public function test_manager_cannot_update_a_team_member_to_a_privileged_role(string $role): void
    {
        $member = User::factory()->create();
        $member->boss()->associate($this->manager);
        $member->save();
        $member->assignRole('admin');

        $response = $this->actingAs($this->manager)
            ->put(route('team.update', ['id' => id_encode($member->id)]), [
                'name' => 'Escalated user',
                'role' => $role,
            ]);

        $response->assertSessionHasErrors('role');

        $this->assertSame($member->name, $member->fresh()->name);
        $this->assertTrue($member->fresh()->hasRole('admin'));
        $this->assertFalse($member->fresh()->hasRole($role));
    }

    public static function privilegedRoleProvider(): array
    {
        return [
            'root' => ['root'],
            'manager' => ['manager'],
        ];
    }
}
