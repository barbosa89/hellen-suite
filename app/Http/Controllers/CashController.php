<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Cash\RecordManualCashMovement;
use App\Actions\Cash\SummarizeCashShift;
use App\Constants\CashMovementType;
use App\Http\Requests\Cash\StoreCashMovementRequest;
use App\Models\Hotel;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CashController extends Controller
{
    public function index(Hotel $hotel, GeneralSettings $settings, SummarizeCashShift $summarizeCashShift): Response
    {
        $activeShift = $hotel->cashShifts()->with('reconciliations')->whereNull('closed_at')->first();
        $movements = $hotel->cashMovements()
            ->when($activeShift, fn ($query) => $query->where('cash_shift_id', $activeShift->id), fn ($query) => $query->whereNull('cash_shift_id'))
            ->with('payment.folio.stay.responsibleGuest:id,first_name,last_name')
            ->latest('occurred_at')
            ->paginate()
            ->withQueryString();

        $summary = $activeShift ? $summarizeCashShift->execute($activeShift) : [];
        $cashSummary = collect($summary)->first(fn (array $row): bool => $row['method'] === 'cash' && $row['currency'] === $settings->currency);

        return Inertia::render('Hotels/Cash/Index', [
            'hotel' => $hotel,
            'movements' => $movements,
            'balanceMinor' => $cashSummary['expected_closing_minor'] ?? 0,
            'currency' => $settings->currency,
            'activeShift' => $activeShift,
            'shiftSummary' => $summary,
            'recentShifts' => $hotel->cashShifts()->whereNotNull('closed_at')->with('reconciliations')->latest('closed_at')->limit(8)->get(),
            'unassignedMovementCount' => $hotel->cashMovements()->whereNull('cash_shift_id')->count(),
        ]);
    }

    public function store(StoreCashMovementRequest $request, Hotel $hotel, RecordManualCashMovement $recordManualCashMovement): RedirectResponse
    {
        $recordManualCashMovement->execute($hotel, CashMovementType::from($request->string('type')->toString()), $request->string('amount')->toString(), $request->string('comment')->toString());

        return back()->with('success', trans('cash.messages.recorded'));
    }
}
