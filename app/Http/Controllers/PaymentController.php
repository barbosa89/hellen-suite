<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Payments\RecordPayment;
use App\Constants\PaymentMethod;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Models\Hotel;
use App\Models\Stay;
use App\Models\StayFolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PaymentController extends Controller
{
    public function __invoke(StorePaymentRequest $request, Hotel $hotel, Stay $stay, StayFolio $stayFolio, RecordPayment $recordPayment): RedirectResponse
    {
        $stayFolio = $stay->folios()->where('hotel_id', $hotel->id)->findOrFail($stayFolio->id);
        $supportPath = $request->file('support')?->store('payments', 'public');

        try {
            $recordPayment->execute(
                $stayFolio,
                $request->string('amount')->toString(),
                PaymentMethod::from($request->string('method')->toString()),
                $request->string('comment')->trim()->toString() ?: null,
                $supportPath ?: null,
                $request->user()?->getKey(),
            );
        } catch (Throwable $exception) {
            if ($supportPath !== false && $supportPath !== null) {
                Storage::disk('public')->delete($supportPath);
            }

            throw $exception;
        }

        return back()->with('success', trans('payments.messages.recorded'));
    }
}
