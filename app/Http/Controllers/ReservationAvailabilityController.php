<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Rooms\RoomAvailability;
use App\Http\Requests\Reservations\ReservationAvailabilityRequest;
use App\Models\Hotel;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class ReservationAvailabilityController extends Controller
{
    public function __invoke(ReservationAvailabilityRequest $request, RoomAvailability $roomAvailability, Hotel $hotel, null|Reservation $reservation = null): JsonResponse
    {
        $rooms = $roomAvailability
            ->query(
                $hotel,
                CarbonImmutable::parse($request->validated('planned_check_in_on')),
                CarbonImmutable::parse($request->validated('planned_check_out_on')),
                $reservation,
            )
            ->select(['id', 'hotel_id', 'room_type_id', 'number', 'floor', 'reference_price'])
            ->with('roomType:id,name,capacity')
            ->orderBy('number')
            ->get();

        return response()->json(['rooms' => $rooms]);
    }
}
