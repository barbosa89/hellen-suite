<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Helpers\Random;
use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Requests\BuyPlan;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Repositories\InvoiceRepository;
use Exception;

class InvoiceController extends Controller
{
    /**
     * @var \App\Repositories\InvoiceRepository
     */
    public InvoiceRepository $repository;

    /**
     * Invoice repository
     *
     * @param InvoiceRepository $invoice
     */
    public function __construct(InvoiceRepository $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->invoice->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BuyPlan $request)
    {
        $invoice = $this->invoice->create($request->validated());

        return redirect()->away($this->buildPaymentUrl($invoice));

    }

    /**
     * Build the URL to the payment gateway.
     *
     * @param \App\Models\Invoice $invoice
     * @return string
     */
    public function buildPaymentUrl(Invoice $invoice): string
    {
        return external_url(config('settings.payments.url'), [
            'public-key' => config('settings.payments.key'),
            'currency' => $invoice->currency->code,
            'amount-in-cents' => number_format($invoice->total, 2, '', ''),
            'reference' => $invoice->number,
            'redirect-url' => route(config('settings.payments.redirect'), ['number' => $invoice->number])
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    /**
     * Check the invoice payment status.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function confirmPayment(Request $request, string $number)
    {
        $response = Http::get(config('settings.payments.confirm') . $request->id);

        $invoice = Invoice::where('number', $number)
            ->with([
                'plans' => function ($query)
                {
                    return $query->select(get_columns('plans', true));
                },
                'user' => function ($query)
                {
                    return $query->select(['users.id']);
                }
            ])
            ->firstOrFail(get_columns('invoices', true));

        if ($response->ok()) {
            $data = json_decode(json_encode($response->json()));

            if ($data->data->status == InvoicePayment::APPROVED) {
                DB::beginTransaction();

                try {
                    $payment = new InvoicePayment();
                    $payment->number = $data->data->id;
                    $payment->value = $data->data->amount_in_cents;
                    $payment->payment_method = $data->data->payment_method_type;
                    $payment->status = InvoicePayment::APPROVED;
                    $payment->invoice()->associate($invoice);
                    $payment->save();

                    $invoice->status = Invoice::PAID;
                    $invoice->save();

                    $plan = $invoice->plans->first();
                    $plan->users()->attach($invoice->user->id, [
                        'ends_at' => now()->addMonths($plan->months)
                    ]);

                    DB::commit();

                    Log::info(trans('payments.confirmation.success', ['number' => $invoice->number]));

                    $type = trans('plans.type.' . $plan->getType());

                    flash(trans('plans.ready', ['plan' => $type]))->success();

                    return redirect()->route('home');
                } catch (Exception $e) {
                    DB::rollBack();

                    report($e);

                    Log::error(trans('payments.confirmation.error', ['number' => $invoice->number]));

                    flash(trans('payments.confirmation.pending', ['number' => $invoice->number]))->info();

                    return redirect()->route('invoices.index');
                }
            }
        }

        Log::info(trans('payments.confirmation.error', ['number' => $invoice->number]), $response->json());

        flash(trans('payments.confirmation.pending', ['number' => $invoice->number]))->info();

        return redirect()->route('invoices.index');
    }
}
