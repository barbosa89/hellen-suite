<?php

namespace Tests\Feature;

use App\Models\User;
use PlanSeeder;
use Tests\TestCase;
use App\Models\Plan;
use RolesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PlanSeeder::class);
    }

    public function test_root_user_can_assign_the_sponsor_plan()
    {
        $root = factory(User::class)->create();
        $root->assignRole('root');

        $manager = factory(User::class)->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($root)
            ->post(route('users.assign', ['user' => id_encode($manager->id)]));

        $response->assertRedirect(route('users.index'));

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.updatedSuccessfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('plan_user', [
            'plan_id' => Plan::where('type', Plan::SPONSOR)->first(['id'])->id,
            'user_id' => $manager->id
        ]);
    }
}
