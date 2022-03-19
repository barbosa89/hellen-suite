<?php

namespace Tests\Feature;

use App\Models\User;
use CurrencySeeder;
use Tests\TestCase;
use App\Models\Invoice;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurgeInvoicesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CurrencySeeder::class);
    }

    public function test_it_delete_all_pending_invoices()
    {
        $user = factory(User::class)->create();

        $invoices = factory(Invoice::class, 10)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2)
        ]);

        $this->artisan('invoices:purge')
            ->expectsOutput("Affected records: {$invoices->count()}");
    }

    public function test_it_does_not_delete_paid_invoices()
    {
        $user = factory(User::class)->create();

        factory(Invoice::class, 10)->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
            'status' => Invoice::PAID
        ]);

        $this->artisan('invoices:purge')
            ->expectsOutput("Affected records: 0");
    }
}
