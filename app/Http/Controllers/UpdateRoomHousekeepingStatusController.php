<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Rooms\UpdateRoomHousekeepingStatusRequest;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;

class UpdateRoomHousekeepingStatusController extends Controller
{
    public function __invoke(UpdateRoomHousekeepingStatusRequest $request, Hotel $hotel, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.housekeeping_updated'));
    }
}
