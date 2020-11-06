<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Requests\BuyPlan;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\InvoiceRepository;
use App\Services\PaymentGateway;
use Exception;

class InvoiceController extends Controller
{
    /**
     * @var \App\Repositories\InvoiceRepository
     */
    public InvoiceRepository $repository;

    /**
     * Constructor
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
        $invoices = $this->invoice->paginate();

        return view('app.invoices.index', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BuyPlan $request)
    {
        // Get pending invoices with the plan to buy
        $pending = $this->invoice->pendingWithPlan($request->plan_id);

        // Redirect to check pending invoices
        if ($pending->isNotEmpty()) {
            return redirect()->route('invoices.index');
        }

        $invoice = $this->invoice->create($request->validated());

        return PaymentGateway::create($invoice)->redirect();
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(string $invoice)
    {
        $invoice = $this->invoice->findById(id_decode($invoice));

        return view('app.invoices.show', compact('invoice'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $invoice)
    {
        if ($this->invoice->destroy(id_decode($invoice))) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('invoices.index');
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Check the invoice payment status.
     *
     * @param  string  $number
     * @return \Illuminate\Http\Response
     */
    public function confirmPayment(Request $request, string $number)
    {
        $response = PaymentGateway::confirm($request->id);

        if ($response->ok()) {
            $data = json_decode(json_encode($response->json()));

            if ($data->data->status == InvoicePayment::APPROVED) {
                DB::beginTransaction();

                try {
                    $invoice = $this->invoice->processPayment($number, $data);

                    DB::commit();

                    Log::info(trans('payments.confirmation.success', ['number' => $number]));

                    $type = trans('plans.type.' . $invoice->plans->first()->getType());

                    flash(trans('plans.ready', ['plan' => $type]))->success();

                    return redirect()->route('home');
                } catch (Exception $e) {
                    DB::rollBack();

                    report($e);

                    Log::error(trans('payments.confirmation.error', ['number' => $number]), [
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]);

                    flash(trans('payments.confirmation.pending', ['number' => $number]))->info();

                    return redirect()->route('invoices.index');
                }
            }
        }

        Log::info(trans('payments.confirmation.error', ['number' => $number]), $response->json());

        flash(trans('payments.confirmation.pending', ['number' => $number]))->info();

        return redirect()->route('invoices.index');
    }
}
