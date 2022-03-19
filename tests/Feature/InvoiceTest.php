<?php

namespace Tests\Feature;

use App\Models\User;
use Exception;
use PlanSeeder;
use CurrencySeeder;
use Tests\TestCase;
use App\Models\Plan;
use RolesTableSeeder;
use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Support\Str;
use App\Models\InvoicePayment;
use App\Services\PaymentGateway;
use App\Models\IdentificationType;
use IdentificationTypesTableSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PlanSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CurrencySeeder::class);

        $this->transaction_id = "17557-1604357312-75896";
    }

    public function test_user_can_see_all_invoices()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->get(route('invoices.index'))
            ->assertOk()
            ->assertViewIs('app.invoices.index')
            ->assertSeeText($invoice->number);
    }

    public function test_user_can_see_one_invoice()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id
        ]);

        $plan = Plan::where('type', Plan::BASIC)->first();

        $invoice->plans()->attach($plan);

        $payment = factory(InvoicePayment::class)->create([
            'value' => $invoice->total,
            'invoice_id' => $invoice->id
        ]);

        $invoice->load(['currency', 'identificationType', 'plans', 'payments']);

        $this->actingAs($user)
            ->get(route('invoices.show', ['invoice' => id_encode($invoice->id)]))
            ->assertOk()
            ->assertViewIs('app.invoices.show')
            ->assertSeeText($invoice->number)
            ->assertSeeText(Str::upper($invoice->identificationType->type))
            ->assertSeeText(Str::upper($invoice->currency->code))
            ->assertSeeText(trans('plans.type.' . Str::lower($invoice->plans()->first()->type)))
            ->assertSeeText(number_format($invoice->payments()->first()->value, 2, ',', '.'));
    }

    public function test_user_can_destroy_a_pending_invoice()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id
        ]);

        $plan = Plan::where('type', Plan::BASIC)->first();

        $invoice->plans()->attach($plan);

        $this->actingAs($user)
            ->followingRedirects()
            ->delete(route('invoices.destroy', ['invoice' => id_encode($invoice->id)]))
            ->assertOk();

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
            'number' => $invoice->number
        ]);

        $this->assertDatabaseMissing('invoice_plan', [
            'invoice_id' => $invoice->id,
            'plan_id' => $plan->id
        ]);
    }

    public function test_user_can_not_destroy_a_paid_invoice()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id,
            'status' => Invoice::PAID
        ]);

        $plan = Plan::where('type', Plan::BASIC)->first();

        $invoice->plans()->attach($plan);

        $this->actingAs($user)
            ->followingRedirects()
            ->delete(route('invoices.destroy', ['invoice' => id_encode($invoice->id)]))
            ->assertOk();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'number' => $invoice->number
        ]);

        $this->assertDatabaseHas('invoice_plan', [
            'invoice_id' => $invoice->id,
            'plan_id' => $plan->id
        ]);
    }

    public function test_redirect_to_payment_gateway_is_successfully_on_invoice_storing()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::BASIC)->first();
        $identificationType = IdentificationType::inRandomOrder()->limit(1)->first();
        $currency = Currency::where('code', Currency::COP)->first();

        $response = $this->actingAs($user)
            ->post(route('invoices.store', [
                'plan_id' => id_encode($plan->id),
                'type_id' => id_encode($identificationType->id),
                'customer_dni' => $this->faker->randomNumber(7),
                'customer_name' => $this->faker->name,
                'currency_id' => id_encode($currency->id)
            ]));

        $invoice = Invoice::latest()->first();
        $gateway = new PaymentGateway($invoice);
        $url = $gateway->generatePaymentUrl();

        $response->assertRedirect($url);
    }

    public function test_user_has_pending_invoices_with_same_plan()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id
        ]);

        $plan = Plan::where('type', Plan::BASIC)->first();

        $invoice->plans()->attach($plan);

        $identificationType = IdentificationType::inRandomOrder()->limit(1)->first();
        $currency = Currency::where('code', Currency::COP)->first();

        $response = $this->actingAs($user)
            ->post(route('invoices.store', [
                'plan_id' => id_encode($plan->id),
                'type_id' => id_encode($identificationType->id),
                'customer_dni' => $this->faker->randomNumber(7),
                'customer_name' => $this->faker->name,
                'currency_id' => id_encode($currency->id)
            ]));

        $response->assertRedirect(route('invoices.index'));
    }

    public function test_it_confirm_user_payment_successfully()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::BASIC)->first();

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id,
            'value' => $plan->price,
            'total' => $plan->price
        ]);

        $invoice->plans()->attach($plan);

        Http::fake(function ($request) use ($invoice) {
            return Http::response($this->getPaymentGatewayResponse($invoice), 200);
        });

        $this->actingAs($user)
            ->get(route('invoices.payments.confirm', ['number' => $invoice->number]) . "/?id={$this->transaction_id}")
            ->assertRedirect(route('home'));

        Http::assertSent(function ($request) {
            return $request->url() == config('settings.payments.confirm') . $this->transaction_id;
        });

        $this->assertDatabaseHas('plan_user', [
            'plan_id' => $plan->id,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('invoice_payments', [
            'number' => $this->transaction_id,
            'value' => $plan->price,
            'status' => InvoicePayment::APPROVED,
            'invoice_id' => $invoice->id
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'value' => $plan->price,
            'total' => $plan->price,
            'status' => Invoice::PAID,
            'user_id' => $user->id
        ]);
    }
    /**
     * @throws Exception
     */
    public function test_it_expects_exception_with_unknown_response_status_code()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::BASIC)->first();

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id,
            'value' => $plan->price,
            'total' => $plan->price
        ]);

        $invoice->plans()->attach($plan);

        Http::fake(function ($request) use ($invoice) {
            return Http::response($this->getPaymentGatewayResponse($invoice, 'CARD', 'UNKNOWN'), 200);
        });

        $this->actingAs($user)
            ->get(route('invoices.payments.confirm', ['number' => $invoice->number]) . "/?id={$this->transaction_id}")
            ->assertRedirect(route('invoices.index'));

        Http::assertSent(function ($request) {
            return $request->url() == config('settings.payments.confirm') . $this->transaction_id;
        });

        $this->assertDatabaseMissing('plan_user', [
            'plan_id' => $plan->id,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseMissing('invoice_payments', [
            'number' => $this->transaction_id,
            'value' => $plan->price,
            'status' => InvoicePayment::APPROVED,
            'invoice_id' => $invoice->id
        ]);

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
            'value' => $plan->price,
            'total' => $plan->price,
            'status' => Invoice::PAID,
            'user_id' => $user->id
        ]);
    }

    public function test_it_check_bad_payment_gateway_response()
    {
        $user = factory(User::class)->create();
        $user->assignRole('manager');

        $plan = Plan::where('type', Plan::BASIC)->first();

        $invoice = factory(Invoice::class)->create([
            'user_id' => $user->id,
            'value' => $plan->price,
            'total' => $plan->price
        ]);

        $invoice->plans()->attach($plan);

        Http::fake(function ($request) use ($invoice) {
            return Http::response($this->getPaymentGatewayResponse($invoice), 404);
        });

        $this->actingAs($user)
            ->get(route('invoices.payments.confirm', ['number' => $invoice->number]) . "/?id={$this->transaction_id}")
            ->assertRedirect(route('invoices.index'));

        Http::assertSent(function ($request) {
            return $request->url() == config('settings.payments.confirm') . $this->transaction_id;
        });

        $this->assertDatabaseMissing('plan_user', [
            'plan_id' => $plan->id,
            'user_id' => $user->id
        ]);

        $this->assertDatabaseMissing('invoice_payments', [
            'number' => $this->transaction_id,
            'value' => $plan->price,
            'status' => InvoicePayment::APPROVED,
            'invoice_id' => $invoice->id
        ]);

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
            'value' => $plan->price,
            'total' => $plan->price,
            'status' => Invoice::PAID,
            'user_id' => $user->id
        ]);
    }

    /**
     * Status: APPROVED, DECLINED, ERROR
     *
     * @param Invoice $invoice
     * @param string $method
     * @param string $status
     * @return array
     */
    private function getPaymentGatewayResponse(Invoice $invoice, string $method = 'CARD', string $status = 'APPROVED'): array
    {
        return [
            "data" => [
                "id" => $this->transaction_id,
                "created_at" => now()->toIso8601String(),
                "amount_in_cents" => number_format($invoice->total, 2, '', ''),
                "reference" => $invoice->number,
                "currency" => $invoice->currency->code,
                "payment_method_type" => $method,
                "redirect_url" => route('invoices.payments.confirm', ['number' => $invoice->number]),
                "status" => $status,
                "status_message" => null
            ]
        ];
    }
}
