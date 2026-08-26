<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Stays\TransferRoomOccupancy;
use App\Http\Requests\Stays\TransferRoomOccupancyRequest;
use App\Models\Hotel;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Illuminate\Http\RedirectResponse;

class TransferRoomOccupancyController extends Controller
{
    public function __invoke(TransferRoomOccupancyRequest $request, Hotel $hotel, Stay $stay, RoomOccupancy $roomOccupancy, TransferRoomOccupancy $transferRoomOccupancy): RedirectResponse
    {
        $transferRoomOccupancy->execute(
            $hotel,
            $stay,
            $roomOccupancy,
            $request->integer('room_id'),
            $request->string('nightly_rate')->toString(),
        );

        return back()->with('success', trans('stays.messages.room_transferred'));
    }
}
