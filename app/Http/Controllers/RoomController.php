<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Rooms\StoreRoomRequest;
use App\Http\Requests\Rooms\UpdateRoomRequest;
use App\Models\Hotel;
use App\Models\Room;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(Hotel $hotel, GeneralSettings $settings): Response
    {
        $rooms = $hotel->rooms()
            ->select([
                'id',
                'hotel_id',
                'room_type_id',
                'number',
                'floor',
                'reference_price',
                'housekeeping_status',
                'is_active',
            ])
            ->with('roomType:id,name,capacity')
            ->orderBy('number')
            ->paginate()
            ->withQueryString();

        return Inertia::render('Hotels/Rooms/Index', [
            'hotel' => $hotel,
            'rooms' => $rooms,
            'currency' => $settings->currency,
            'hasRoomTypes' => $hotel->roomTypes()->exists(),
        ]);
    }

    public function create(Hotel $hotel, GeneralSettings $settings): Response
    {
        return Inertia::render('Hotels/Rooms/Create', [
            'hotel' => $hotel,
            'roomTypes' => $hotel->roomTypes()->orderBy('name')->get(['id', 'name', 'capacity']),
            'currency' => $settings->currency,
        ]);
    }

    public function store(StoreRoomRequest $request, Hotel $hotel): RedirectResponse
    {
        $hotel->rooms()->create($request->validated());

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.created'));
    }

    public function edit(Hotel $hotel, Room $room, GeneralSettings $settings): Response
    {
        return Inertia::render('Hotels/Rooms/Edit', [
            'hotel' => $hotel,
            'room' => $room,
            'roomTypes' => $hotel->roomTypes()->orderBy('name')->get(['id', 'name', 'capacity']),
            'currency' => $settings->currency,
        ]);
    }

    public function update(UpdateRoomRequest $request, Hotel $hotel, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.updated'));
    }

    public function destroy(Hotel $hotel, Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.deleted'));
    }

    public function toggle(Hotel $hotel, Room $room): RedirectResponse
    {
        $room->update(['is_active' => ! $room->is_active]);

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.activity_updated'));
    }
}
