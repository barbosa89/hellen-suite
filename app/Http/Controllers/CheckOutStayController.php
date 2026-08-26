<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Stays\CheckOutStay;
use App\Http\Requests\Stays\CheckOutStayRequest;
use App\Models\Hotel;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;

class CheckOutStayController extends Controller
{
    public function __invoke(CheckOutStayRequest $request, Hotel $hotel, Stay $stay, CheckOutStay $checkOutStay): RedirectResponse
    {
        $checkedOutAt = $request->date('checked_out_at')?->toImmutable() ?? CarbonImmutable::now();
        $checkOutStay->execute($stay, $checkedOutAt);

        return back()->with('success', trans('stays.messages.checked_out'));
    }
}
