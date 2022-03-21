<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Services\PaymentGateway;
use Database\Seeders\PlanSeeder;
use Database\Seeders\CurrencySeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IdentificationTypesTableSeeder;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CurrencySeeder::class);
    }

    public function test_get_self_instance()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id
        ]);

        $gateway = PaymentGateway::create($invoice);

        $this->assertTrue($gateway instanceof PaymentGateway);
    }

    public function test_return_redirect_to_payment_gateway()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id
        ]);

        $redirect = PaymentGateway::create($invoice)->redirect();

        $this->assertTrue($redirect instanceof RedirectResponse);
    }

    public function test_generate_payment_gateway_url()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id
        ]);

        $gateway = new PaymentGateway($invoice);

        $validation = filter_var($gateway->generatePaymentUrl(), FILTER_VALIDATE_URL);

        $this->assertTrue($validation !== false);
    }

    public function test_send_request_to_payment_confirmation()
    {
        Http::fake();

        $id = '12312-123131-123123';

        PaymentGateway::confirm($id);


        Http::assertSent(function ($request) use ($id) {
            return $request->url() == config('settings.payments.confirm') . $id;
        });
    }
}
