<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Rooms\RoomAvailability;
use App\Constants\ReservationStatus;
use App\Http\Requests\Rooms\RoomIndexRequest;
use App\Http\Requests\Rooms\StoreRoomRequest;
use App\Http\Requests\Rooms\UpdateRoomRequest;
use App\Models\Hotel;
use App\Models\Room;
use App\Settings\GeneralSettings;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(RoomIndexRequest $request, Hotel $hotel, GeneralSettings $settings, RoomAvailability $roomAvailability): Response
    {
        $checkInOn = CarbonImmutable::parse($request->validated('check_in_on'));
        $checkOutOn = CarbonImmutable::parse($request->validated('check_out_on'));
        $availableRoomIds = $roomAvailability->query($hotel, $checkInOn, $checkOutOn)
            ->pluck('rooms.id');

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
            ->with([
                'roomType:id,name,capacity',
                'reservedRooms' => fn ($query) => $query
                    ->select(['id', 'room_id', 'reservation_id', 'planned_check_in_on', 'planned_check_out_on'])
                    ->whereHas('reservation', fn ($reservationQuery) => $reservationQuery->where('status', ReservationStatus::Confirmed))
                    ->whereDate('planned_check_out_on', '>', today()->toDateString())
                    ->orderBy('planned_check_in_on'),
                'roomOccupancies' => fn ($query) => $query
                    ->select(['id', 'room_id', 'checked_in_at', 'expected_check_out_on'])
                    ->whereNull('checked_out_at'),
            ])
            ->orderBy('number')
            ->paginate()
            ->withQueryString()
            ->through(function (Room $room) use ($availableRoomIds): Room {
                $room->setAttribute('is_available', $availableRoomIds->contains($room->id));

                return $room;
            });

        return Inertia::render('Hotels/Rooms/Index', [
            'hotel' => $hotel,
            'rooms' => $rooms,
            'currency' => $settings->currency,
            'hasRoomTypes' => $hotel->roomTypes()->exists(),
            'availabilityPeriod' => [
                'check_in_on' => $checkInOn->toDateString(),
                'check_out_on' => $checkOutOn->toDateString(),
                'today' => today()->toDateString(),
            ],
            'availabilitySummary' => [
                'available' => $availableRoomIds->count(),
                'total' => $hotel->rooms()->count(),
            ],
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
        $data = $request->validated();
        $hasActiveOccupanciesOrConfirmedReservations = $room->roomOccupancies()->whereNull('checked_out_at')->exists()
            || $room->reservedRooms()->whereHas('reservation', fn ($query) => $query->where('status', ReservationStatus::Confirmed))->exists();

        if (! $data['is_active'] && $hasActiveOccupanciesOrConfirmedReservations) {
            throw ValidationException::withMessages(['is_active' => trans('rooms.messages.inventory_blocked')]);
        }

        $room->update($data);

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.updated'));
    }

    public function destroy(Hotel $hotel, Room $room): RedirectResponse
    {
        if ($room->roomOccupancies()->exists() || $room->reservedRooms()->exists()) {
            return back()->with('error', trans('rooms.messages.delete_blocked'));
        }

        $room->delete();

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.deleted'));
    }

    public function toggle(Hotel $hotel, Room $room): RedirectResponse
    {
        $hasActiveOccupanciesOrConfirmedReservations = $room->roomOccupancies()->whereNull('checked_out_at')->exists()
            || $room->reservedRooms()->whereHas('reservation', fn ($query) => $query->where('status', ReservationStatus::Confirmed))->exists();

        if ($room->is_active && $hasActiveOccupanciesOrConfirmedReservations) {
            return back()->with('error', trans('rooms.messages.inventory_blocked'));
        }

        $room->update(['is_active' => ! $room->is_active]);

        return redirect()->route('hotels.rooms.index', $hotel)
            ->with('success', trans('rooms.messages.activity_updated'));
    }
}
