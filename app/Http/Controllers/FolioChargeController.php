<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Folios\RecordFolioCharge;
use App\Http\Requests\Folios\StoreFolioChargeRequest;
use App\Models\Hotel;
use App\Models\Stay;
use App\Models\StayFolio;
use Illuminate\Http\RedirectResponse;

class FolioChargeController extends Controller
{
    public function __invoke(StoreFolioChargeRequest $request, Hotel $hotel, Stay $stay, StayFolio $stayFolio, RecordFolioCharge $recordFolioCharge): RedirectResponse
    {
        $stayFolio = $stay->folios()
            ->where('hotel_id', $hotel->id)
            ->findOrFail($stayFolio->id);

        $recordFolioCharge->execute(
            $stayFolio,
            $request->string('description')->toString(),
            $request->string('amount')->toString(),
            $request->user()?->getKey()
        );

        return back()->with('success', trans('payments.messages.charge_recorded'));
    }
}
