<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Folios\RecordFolioAdjustment;
use App\Constants\FolioAdjustmentType;
use App\Http\Requests\Folios\StoreFolioAdjustmentRequest;
use App\Models\Hotel;
use App\Models\Stay;
use App\Models\StayFolio;
use Illuminate\Http\RedirectResponse;

class FolioAdjustmentController extends Controller
{
    public function __invoke(StoreFolioAdjustmentRequest $request, Hotel $hotel, Stay $stay, StayFolio $stayFolio, RecordFolioAdjustment $recordFolioAdjustment): RedirectResponse
    {
        $stayFolio = $stay->folios()
            ->where('hotel_id', $hotel->id)
            ->findOrFail($stayFolio->id);

        $recordFolioAdjustment->execute(
            $stayFolio,
            FolioAdjustmentType::from($request->string('type')->toString()),
            $request->string('amount')->toString(),
            $request->string('reason')->toString(),
            $request->user()?->getKey()
        );

        return back()->with('success', trans('payments.messages.adjustment_recorded'));
    }
}
