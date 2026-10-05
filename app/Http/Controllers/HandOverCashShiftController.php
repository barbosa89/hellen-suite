<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Cash\HandOverCashShift;
use App\Http\Requests\Cash\ReconcileCashShiftRequest;
use App\Models\CashShift;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;

class HandOverCashShiftController extends Controller
{
    public function __invoke(ReconcileCashShiftRequest $request, Hotel $hotel, CashShift $cashShift, HandOverCashShift $handOverCashShift): RedirectResponse
    {
        abort_unless($cashShift->hotel_id === $hotel->id, RedirectResponse::HTTP_NOT_FOUND);

        $handOverCashShift->execute(
            $cashShift,
            $request->array('reconciliations'),
            $request->string('closing_note')->toString() ?: null,
            $request->string('opening_note')->toString() ?: null,
        );

        return redirect()->route('hotels.cash.index', $hotel)->with('success', trans('cash.messages.shift_handed_over'));
    }
}
