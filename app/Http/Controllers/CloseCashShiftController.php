<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Cash\CloseCashShift;
use App\Http\Requests\Cash\ReconcileCashShiftRequest;
use App\Models\CashShift;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;

class CloseCashShiftController extends Controller
{
    public function __invoke(ReconcileCashShiftRequest $request, Hotel $hotel, CashShift $cashShift, CloseCashShift $closeCashShift): RedirectResponse
    {
        abort_unless($cashShift->hotel_id === $hotel->id, RedirectResponse::HTTP_NOT_FOUND);

        $closeCashShift->execute($cashShift, $request->array('reconciliations'), $request->string('closing_note')->toString() ?: null);

        return redirect()->route('hotels.cash.shifts.show', [$hotel, $cashShift])->with('success', trans('cash.messages.shift_closed'));
    }
}
