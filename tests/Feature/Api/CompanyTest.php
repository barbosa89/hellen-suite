<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\CountriesTableSeeder;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IdentificationTypesTableSeeder;

class CompanyTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CountriesTableSeeder::class);
    }

    public function test_access_is_denied_if_user_dont_have_companies_index_permissions()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/api/v1/web/companies');

        $response->assertForbidden();
    }

    public function test_user_can_list_companies()
    {
        /** @var User $manager */
        $manager = User::factory()->create();
        $manager->givePermissionTo('companies.index');

        /** @var Company $company */
        $company = Company::factory()->create([
            'user_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
            ->get('/api/v1/web/companies');

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => id_encode($company->id),
                'business_name' => $company->business_name,
                'tin' => (string) $company->tin,
                'email' => $company->email,
            ]);
    }

    public function test_user_can_filter_new_companies_by_date()
    {
        /** @var User $manager */
        $manager = User::factory()->create();
        $manager->givePermissionTo('companies.index');

        /** @var Company $oldCompany */
        $oldCompany = Company::factory()->create([
            'user_id' => $manager->id,
            'created_at' => now()->subDays(8),
        ]);

        /** @var Company $company */
        $company = Company::factory()->create([
            'user_id' => $manager->id,
            'created_at' => now()->subDays(6)
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                '/api/v1/web/companies',
                [
                    'from_date' => now()->subDays(7)->format('Y-m-d'),
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => id_encode($company->id),
                'business_name' => $company->business_name,
                'tin' => (string)  $company->tin,
                'email' => $company->email,
            ])
            ->assertJsonMissing([
                'hash' => id_encode($oldCompany->id),
                'business_name' =>$oldCompany->business_name,
                'tin' => (string)  $oldCompany->tin,
                'email' => $oldCompany->email,
            ]);
    }
}
