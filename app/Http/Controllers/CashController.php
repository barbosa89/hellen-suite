<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Cash\RecordManualCashMovement;
use App\Constants\CashMovementType;
use App\Http\Requests\Cash\StoreCashMovementRequest;
use App\Models\Hotel;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CashController extends Controller
{
    public function index(Hotel $hotel, GeneralSettings $settings): Response
    {
        $movements = $hotel->cashMovements()
            ->with('payment.folio.stay.responsibleGuest:id,first_name,last_name')
            ->latest('occurred_at')
            ->paginate()
            ->withQueryString();

        $balanceMinor = (int) $hotel->cashMovements()
            ->where('currency', $settings->currency)
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'in' THEN amount_minor ELSE -amount_minor END), 0) AS balance")
            ->value('balance');

        return Inertia::render('Hotels/Cash/Index', [
            'hotel' => $hotel,
            'movements' => $movements,
            'balanceMinor' => $balanceMinor,
            'currency' => $settings->currency,
        ]);
    }

    public function store(StoreCashMovementRequest $request, Hotel $hotel, RecordManualCashMovement $recordManualCashMovement): RedirectResponse
    {
        $recordManualCashMovement->execute($hotel, CashMovementType::from($request->string('type')->toString()), $request->string('amount')->toString(), $request->string('comment')->toString(), $request->user()?->getKey());

        return back()->with('success', trans('cash.messages.recorded'));
    }
}
