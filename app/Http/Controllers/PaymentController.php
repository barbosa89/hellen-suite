<?php

namespace App\Http\Controllers;

use App\Helpers\Customer;
use App\Helpers\Fields;
use App\Helpers\Id;
use App\Http\Requests\StorePayment;
use App\Welkome\Invoice;
use App\Welkome\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($invoiceId)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($invoiceId))
            ->where('losses', false)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::get('guests'))
                    ->withPivot('main');
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        $customer = Customer::get($invoice);

        return view('app.payments.index', compact('invoice', 'customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($invoiceId)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($invoiceId))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->payment_status) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        $invoice->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::get('guests'))
                    ->withPivot('main');
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        if ((float) $invoice->value == $invoice->payments->sum('value')) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        $customer = Customer::get($invoice);

        return view('app.payments.create', compact('invoice', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayment $request, $invoiceId)
    {
        $status = false;
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($invoiceId))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->load([
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        if ($invoice->payment_status) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        if ((float) $invoice->value == $invoice->payments->sum('value')) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        // Calculate the total value with the other payments adding the payment to update
        $paymentsValue = $invoice->payments->sum('value') + (float) (float) $request->value;

        if ($paymentsValue > (float) $invoice->value) {
            flash(trans('payments.exceeds'))->info();

            return back();
        }

        DB::transaction(function () use (&$status, &$invoice, $request) {
            try {
                $payment = new Payment();
                $payment->date = $request->date;
                $payment->commentary = $request->commentary;
                $payment->payment_method = $request->method;
                $payment->value = (float) $request->value;
                $payment->invoice()->associate($invoice->id);

                if ($request->hasFile('invoice')) {
                    $path = $request->file('invoice')->storeAs(
                        'public',
                        time() . "_" . $request->file('invoice')->getClientOriginalName()
                    );

                    $payment->invoice = $path;
                }

                if ($payment->save()) {
                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('payment.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('payments.index', [
                'invoice' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string $invoice
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function edit($invoice, $id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($invoice))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->payment_status) {
            flash(trans('invoices.closed'))->info();

            return back();
        }

        $invoice->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::get('guests'))
                    ->withPivot('main');
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        $customer = Customer::get($invoice);
        $payment = $invoice->payments->where('id', Id::get($id))->first();

        return view('app.payments.edit', compact('invoice', 'customer', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $invoice
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePayment $request, $invoice, $id)
    {
        $status = false;
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($invoice))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->payment_status) {
            flash(trans('invoices.closed'))->info();

            return back();
        }

        $invoice->load([
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        // Calculate the total value with the other payments adding the payment to update
        $paymentsValue = $invoice->payments->where('id', '!=', Id::get($id))->sum('value') + (float) $request->value;

        // Check if payments value is greater than invoice value
        if ($paymentsValue > (float) $invoice->value) {
            flash(trans('payments.exceeds'))->info();

            return back();
        }

        DB::transaction(function () use (&$status, &$invoice, $request, $id) {
            try {
                // Get payment to update
                $payment = $invoice->payments->where('id', Id::get($id))->first();

                $payment->date = $request->date;
                $payment->commentary = $request->commentary;
                $payment->payment_method = $request->method;
                $payment->value = (float) $request->value;

                if ($request->hasFile('invoice')) {
                    if (!empty($payment->invoice)) {
                        Storage::delete($payment->invoice);
                    }

                    $path = $request->file('invoice')->storeAs(
                        'public',
                        time() . "_" . $request->file('invoice')->getClientOriginalName()
                    );

                    $payment->invoice = $path;
                }

                if ($payment->save()) {
                    // Reload payments
                    $invoice->load([
                        'payments' => function ($query)
                        {
                            $query->select(Fields::get('payments'));
                        }
                    ]);

                    if ((float) $invoice->value < $invoice->payments->sum('value')) {
                        $invoice->payment_status = false;
                    }

                    $invoice->save();

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('payment.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('payments.index', [
                'invoice' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $invoice
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice, $id)
    {
        $status = false;
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($invoice))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->payment_status) {
            flash(trans('invoices.closed'))->info();

            return back();
        }

        $invoice->load([
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        DB::transaction(function () use (&$status, &$invoice, $id) {
            try {
                // Get payment to delete
                $payment = $invoice->payments->where('id', Id::get($id))->first();

                // Payment support
                $support = $payment->invoice;

                if ($payment->delete()) {
                    // Reload payments
                    $invoice->load([
                        'payments' => function ($query)
                        {
                            $query->select(Fields::get('payments'));
                        }
                    ]);

                    if ((float) $invoice->value < $invoice->payments->sum('value')) {
                        $invoice->payment_status = false;
                    }

                    $invoice->save();

                    Storage::delete($support);

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('payment.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('payments.index', [
                'invoice' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }
}
