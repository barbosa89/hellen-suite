<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RoomTypes\StoreRoomTypeRequest;
use App\Http\Requests\RoomTypes\UpdateRoomTypeRequest;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoomTypeController extends Controller
{
    public function index(Hotel $hotel): Response
    {
        $roomTypes = $hotel->roomTypes()
            ->select(['id', 'hotel_id', 'name', 'capacity'])
            ->withCount('rooms')
            ->orderBy('name')
            ->paginate()
            ->withQueryString();

        return Inertia::render('Hotels/Rooms/Types/Index', [
            'hotel' => $hotel,
            'roomTypes' => $roomTypes,
        ]);
    }

    public function create(Hotel $hotel): Response
    {
        return Inertia::render('Hotels/Rooms/Types/Create', ['hotel' => $hotel]);
    }

    public function store(StoreRoomTypeRequest $request, Hotel $hotel): RedirectResponse
    {
        $hotel->roomTypes()->create($request->validated());

        return redirect()->route('hotels.room-types.index', $hotel)
            ->with('success', trans('room_types.messages.created'));
    }

    public function edit(Hotel $hotel, RoomType $roomType): Response
    {
        return Inertia::render('Hotels/Rooms/Types/Edit', [
            'hotel' => $hotel,
            'roomType' => $roomType,
        ]);
    }

    public function update(UpdateRoomTypeRequest $request, Hotel $hotel, RoomType $roomType): RedirectResponse
    {
        $roomType->update($request->validated());

        return redirect()->route('hotels.room-types.index', $hotel)
            ->with('success', trans('room_types.messages.updated'));
    }

    public function destroy(Hotel $hotel, RoomType $roomType): RedirectResponse
    {
        if ($roomType->rooms()->exists()) {
            return redirect()->route('hotels.room-types.index', $hotel)
                ->with('error', trans('room_types.messages.delete_blocked'));
        }

        $roomType->delete();

        return redirect()->route('hotels.room-types.index', $hotel)
            ->with('success', trans('room_types.messages.deleted'));
    }
}
