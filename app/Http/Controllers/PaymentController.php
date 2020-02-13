<?php

namespace App\Http\Controllers;

use App\Helpers\Customer;
use App\Helpers\Fields;
use App\Helpers\Id;
use App\Http\Requests\StorePayment;
use App\Welkome\Voucher;
use App\Welkome\Payment;
use App\Welkome\Shift;
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
    public function index($voucherId)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($voucherId))
            ->where('losses', false)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
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

        $customer = Customer::get($voucher);

        return view('app.payments.index', compact('voucher', 'customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($voucherId)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($voucherId))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->payment_status) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        $voucher->load([
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

        if ((float) $voucher->value == $voucher->payments->sum('value')) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        $customer = Customer::get($voucher);

        return view('app.payments.create', compact('voucher', 'customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayment $request, $voucherId)
    {
        $status = false;
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($voucherId))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            },
            'hotel' => function ($query)
            {
                $query->select(['id']);
            }
        ]);

        if ($voucher->payment_status) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        if ((float) $voucher->value == $voucher->payments->sum('value')) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        // Calculate the total value with the other payments adding the payment to update
        $paymentsValue = $voucher->payments->sum('value') + (float) (float) $request->value;

        if ($paymentsValue > (float) $voucher->value) {
            flash(trans('payments.exceeds'))->info();

            return back();
        }

        DB::transaction(function () use (&$status, &$voucher, $request) {
            try {
                $payment = new Payment();
                $payment->date = $request->date;
                $payment->commentary = $request->commentary;
                $payment->payment_method = $request->method;
                $payment->value = (float) $request->value;
                $payment->invoice()->associate($voucher->id);

                if ($request->hasFile('invoice')) {
                    $path = $request->file('invoice')->storeAs(
                        'public',
                        time() . "_" . $request->file('invoice')->getClientOriginalName()
                    );

                    $payment->invoice = $path;
                }

                if ($payment->save()) {
                    if ($payment->payment_method == 'cash') {
                        $shift = Shift::current($voucher->hotel->id);
                        $shift->load([
                            'vouchers' => function ($query) use ($voucher)
                            {
                                $query->where('id', $voucher->id);
                            }
                        ]);

                        if ($shift->invoices->isNotEmpty()) {
                            $shift->cash += $payment->value;
                            $shift->save();
                        }
                    }

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('payment.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('payments.index', [
                'invoice' => Hashids::encode($voucher->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string $voucher
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function edit($voucher, $id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($voucher))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->payment_status) {
            flash(trans('vouchers.isClosed'))->info();

            return back();
        }

        $voucher->load([
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

        $customer = Customer::get($voucher);
        $payment = $voucher->payments->where('id', Id::get($id))->first();

        return view('app.payments.edit', compact('voucher', 'customer', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $voucher
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePayment $request, $voucher, $id)
    {
        $status = false;
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($voucher))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->payment_status) {
            flash(trans('vouchers.isClosed'))->info();

            return back();
        }

        $voucher->load([
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        // Calculate the total value with the other payments adding the payment to update
        $paymentsValue = $voucher->payments->where('id', '!=', Id::get($id))->sum('value') + (float) $request->value;

        // Check if payments value is greater than voucher value
        if ($paymentsValue > (float) $voucher->value) {
            flash(trans('payments.exceeds'))->info();

            return back();
        }

        DB::transaction(function () use (&$status, &$voucher, $request, $id) {
            try {
                // Get payment to update
                $payment = $voucher->payments->where('id', Id::get($id))->first();

                // subtract value to shift to prepare the change in value
                $shift = Shift::current($voucher->hotel->id);

                // Only if the payment method is cash,
                // the value is subtracted to the shift
                if ($payment->payment_method == 'cash') {
                    $shift->cash -= (float) $payment->value;
                }

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
                    // Only if the payment method is cash,
                    // the new value will be added to the shift
                    if ($payment->payment_method == 'cash') {
                        $shift->load([
                            'vouchers' => function ($query) use ($voucher)
                            {
                                $query->where('id', $voucher->id);
                            }
                        ]);

                        if ($shift->invoices->isNotEmpty()) {
                            $shift->cash += $payment->value;
                        }
                    }

                    $shift->save();

                    // Reload payments
                    $voucher->load([
                        'payments' => function ($query)
                        {
                            $query->select(Fields::get('payments'));
                        }
                    ]);

                    if ((float) $voucher->value < $voucher->payments->sum('value')) {
                        $voucher->payment_status = false;
                    }

                    $voucher->save();

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('payment.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('payments.index', [
                'invoice' => Hashids::encode($voucher->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $voucher
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($voucher, $id)
    {
        $status = false;
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($voucher))
            ->where('status', true)
            ->where('losses', false)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->payment_status) {
            flash(trans('vouchers.isClosed'))->info();

            return back();
        }

        $voucher->load([
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        DB::transaction(function () use (&$status, &$voucher, $id) {
            try {
                // Get payment to delete
                $payment = $voucher->payments->where('id', Id::get($id))->first();

                // Payment support
                $support = $payment->invoice;

                // subtract value to shift
                $shift = Shift::current($voucher->hotel->id);

                // Only if the payment method is cash,
                // the value is subtracted to the shift
                if ($payment->payment_method == 'cash') {
                    $shift->load([
                        'vouchers' => function ($query) use ($voucher)
                        {
                            $query->where('id', $voucher->id);
                        }
                    ]);

                    if ($shift->invoices->isNotEmpty()) {
                        $shift->cash -= $payment->value;
                    }
                }

                if ($payment->delete()) {
                    $shift->save();

                    // Reload payments
                    $voucher->load([
                        'payments' => function ($query)
                        {
                            $query->select(Fields::get('payments'));
                        }
                    ]);

                    if ((float) $voucher->value < $voucher->payments->sum('value')) {
                        $voucher->payment_status = false;
                    }

                    $voucher->save();

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
                'invoice' => Hashids::encode($voucher->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }
}
