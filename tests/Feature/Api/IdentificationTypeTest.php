<?php

namespace Tests\Feature\Api;

use App\Models\IdentificationType;
use App\User;
use Tests\TestCase;
use Laravel\Passport\Passport;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IdentificationTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_get_identification_types()
    {
        $response = $this->getJson('/api/v1/identification-types');

        $response->assertUnauthorized();
    }

    public function test_user_can_get_all_identification_types()
    {
        $this->seed(IdentificationTypesTableSeeder::class);

        /** @var User $user */
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/identification-types');

        $identificationType = IdentificationType::first();

        $response->assertOk()
            ->assertJsonCount(8)
            ->assertJsonFragment([
                'hash' => $identificationType->hash,
                'type' => $identificationType->type,
                'description' => $identificationType->description,
            ]);
    }
}
