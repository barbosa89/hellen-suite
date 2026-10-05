<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Cash\OpenCashShift;
use App\Actions\Cash\SummarizeCashShift;
use App\Http\Requests\Cash\OpenCashShiftRequest;
use App\Models\CashShift;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CashShiftController extends Controller
{
    public function store(OpenCashShiftRequest $request, Hotel $hotel, OpenCashShift $openCashShift): RedirectResponse
    {
        $openCashShift->execute(
            $hotel,
            $request->string('opening_amount')->toString(),
            $request->string('opening_note')->toString() ?: null,
        );

        return back()->with('success', trans('cash.messages.shift_opened'));
    }

    public function show(Hotel $hotel, CashShift $cashShift, SummarizeCashShift $summarizeCashShift): Response
    {
        abort_unless($cashShift->hotel_id === $hotel->id, RedirectResponse::HTTP_NOT_FOUND);

        $cashShift->load('reconciliations');

        return Inertia::render('Hotels/Cash/Show', [
            'hotel' => $hotel,
            'shift' => $cashShift,
            'summary' => $summarizeCashShift->execute($cashShift),
            'movements' => $cashShift->cashMovements()
                ->with('payment.folio.stay.responsibleGuest:id,first_name,last_name')
                ->latest('occurred_at')
                ->paginate()
                ->withQueryString(),
        ]);
    }
}
