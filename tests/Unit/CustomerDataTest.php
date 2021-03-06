<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Guest;
use App\Models\Company;
use App\Models\Voucher;
use App\Data\Views\Customer;
use CountriesTableSeeder;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerDataTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesTableSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
    }

    public function test_generate_empty_customer_data()
    {
        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create();

        $customer = new Customer($voucher);

        $this->assertEquals(
            [
                'name' => '',
                'tin' => '',
                'route' => '',
                'email' => '',
                'address' => '',
                'phone' => '',
            ],
            $customer->toArray()
        );
    }

    public function test_generate_customer_data_with_company()
    {
        /** @var Company $company */
        $company = factory(Company::class)->create();

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'company_id' => $company->id,
        ]);

        $voucher->load('company');

        $customer = new Customer($voucher);

        $this->assertEquals(
            [
                'name' => $company->business_name,
                'tin' => (string) $company->tin,
                'route' => route('companies.show', ['id' => $company->hash]),
                'email' => $company->email,
                'address' => $company->address,
                'phone' => $company->phone,
            ],
            $customer->build()->toArray()
        );
    }

    public function test_generate_customer_data_with_guests()
    {
        /** @var Guest $guest */
        $guest = factory(Guest::class)->create();

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create();

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true,
        ]);

        $voucher->load([
            'guests' => function ($query) {
                $query->withPivot('main');
            },
        ]);

        $customer = new Customer($voucher);

        $this->assertEquals(
            [
                'name' => $guest->full_name,
                'tin' => (string) $guest->dni,
                'route' => route('guests.show', ['id' => $guest->hash]),
                'email' => $guest->email ?? '',
                'address' => $guest->address ?? '',
                'phone' => $guest->phone ?? '',
            ],
            $customer->build()->toArray()
        );
    }

    public function test_generate_empty_customer_data_when_main_guest_is_missing()
    {
        /** @var Guest $guest */
        $guest = factory(Guest::class)->create();

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create();

        $voucher->guests()->attach($guest->id, [
            'main' => false,
            'active' => true,
        ]);

        $voucher->load([
            'guests' => function ($query) {
                $query->withPivot('main');
            },
        ]);

        $customer = new Customer($voucher);

        $this->assertEquals(
            [
                'name' => '',
                'tin' => '',
                'route' => '',
                'email' => '',
                'address' => '',
                'phone' => '',
            ],
            $customer->build()->toArray()
        );
    }

    public function test_generate_empty_customer_data_when_main_pivot_field_is_not_loaded()
    {
        /** @var Guest $guest */
        $guest = factory(Guest::class)->create();

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create();

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true,
        ]);

        $voucher->load('guests');

        $customer = new Customer($voucher);

        $this->assertEquals(
            [
                'name' => '',
                'tin' => '',
                'route' => '',
                'email' => '',
                'address' => '',
                'phone' => '',
            ],
            $customer->build()->toArray()
        );
    }
}
