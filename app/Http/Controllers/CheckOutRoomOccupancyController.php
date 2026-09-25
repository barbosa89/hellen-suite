<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Stays\CheckOutRoomOccupancy;
use App\Http\Requests\Stays\CheckOutRoomOccupancyRequest;
use App\Models\Hotel;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;

class CheckOutRoomOccupancyController extends Controller
{
    public function __invoke(
        CheckOutRoomOccupancyRequest $request,
        Hotel $hotel,
        Stay $stay,
        RoomOccupancy $roomOccupancy,
        CheckOutRoomOccupancy $checkOutRoomOccupancy,
    ): RedirectResponse {
        $checkedOutAt = $request->date('checked_out_at')?->toImmutable() ?? CarbonImmutable::now();
        $checkOutRoomOccupancy->execute($stay, $roomOccupancy, $checkedOutAt);

        return back()->with('success', trans('stays.messages.room_checked_out'));
    }
}
