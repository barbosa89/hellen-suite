<?php

namespace Tests\Feature\Api;

use App\User;
use Tests\TestCase;
use App\Models\Country;
use CountriesTableSeeder;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_get_countries()
    {
        $response = $this->getJson('/api/v1/countries');

        $response->assertUnauthorized();
    }

    public function test_user_can_get_all_countries()
    {
        $this->seed(CountriesTableSeeder::class);

        /** @var User $user */
        $user = factory(User::class)->create();

        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/countries');

        $country = Country::first();

        $response->assertOk()
            ->assertJsonCount(194)
            ->assertJsonFragment([
                'hash' => $country->hash,
                'name' => $country->name,
            ]);
    }
}
