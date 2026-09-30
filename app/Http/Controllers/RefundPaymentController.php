<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Payments\RefundPayment;
use App\Http\Requests\Payments\RefundPaymentRequest;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Stay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;

class RefundPaymentController extends Controller
{
    public function __invoke(RefundPaymentRequest $request, Hotel $hotel, Stay $stay, Payment $payment, RefundPayment $refundPayment): RedirectResponse
    {
        $payment = Payment::query()->whereHas(
            'folio',
            fn (Builder $query): Builder => $query->where('hotel_id', $hotel->id)
                ->where('stay_id', $stay->id)
        )->findOrFail($payment->id);

        $refundPayment->execute($payment, $request->string('amount')->toString(), $request->string('reason')->toString(), $request->user()?->getKey());

        return back()->with('success', trans('payments.messages.refunded'));
    }
}
