<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Reservations\CreateReservation;
use App\Actions\Reservations\UpdateReservation;
use App\Constants\ReservationStatus;
use App\Data\Reservations\ReservationData;
use App\Http\Requests\Reservations\UpsertReservationRequest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use App\Models\Reservation;
use App\Settings\GeneralSettings;
use App\Support\Countries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use function in_array;

class ReservationController extends Controller
{
    public function index(Request $request, Hotel $hotel, GeneralSettings $settings): Response
    {
        $status = $request->string('status')->toString();
        $search = $request->string('search')->trim()->toString();

        $reservations = $hotel->reservations()
            ->select(['id', 'hotel_id', 'responsible_guest_id', 'status', 'planned_check_in_on', 'planned_check_out_on'])
            ->with('responsibleGuest:id,first_name,last_name,identification_number')
            ->with(['reservedRooms:id,reservation_id,room_id,nightly_rate', 'reservedRooms.room:id,number'])
            ->when(in_array($status, ReservationStatus::toArray(), true), fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->whereHas('responsibleGuest', fn ($guestQuery) => $guestQuery
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('identification_number', 'like', "%{$search}%")))
            ->orderBy('planned_check_in_on')
            ->orderBy('id')
            ->paginate()
            ->withQueryString();

        return Inertia::render('Hotels/Reservations/Index', [
            'hotel' => $hotel,
            'reservations' => $reservations,
            'filters' => ['status' => $status, 'search' => $search],
            'statuses' => ReservationStatus::toArray(),
            'currency' => $settings->currency,
        ]);
    }

    public function create(Hotel $hotel, GeneralSettings $settings): Response
    {
        return Inertia::render('Hotels/Reservations/Create', [
            'hotel' => $hotel,
            'identificationTypes' => IdentificationType::query()->orderBy('code')->get(['id', 'code']),
            'currency' => $settings->currency,
            'countries' => Countries::alpha3(),
            'subdivisions' => Countries::subdivisions($hotel->country_code ?? 'CO'),
        ]);
    }

    public function store(UpsertReservationRequest $request, Hotel $hotel, CreateReservation $createReservation): RedirectResponse
    {
        $reservation = $createReservation->execute($hotel, ReservationData::fromValidated($request->validated()));

        return redirect()->route('hotels.reservations.show', [$hotel, $reservation])
            ->with('success', trans('reservations.messages.created'));
    }

    public function show(Hotel $hotel, Reservation $reservation, GeneralSettings $settings): Response
    {
        $reservation->load([
            'responsibleGuest:id,first_name,last_name,identification_number',
            'reservationGuests.guest:id,first_name,last_name,identification_number,mobile,email',
            'reservedRooms.room:id,number,floor,room_type_id',
            'reservedRooms.room.roomType:id,name,capacity',
            'reservedRooms.reservationGuests.guest:id,first_name,last_name',
            'events' => fn ($query) => $query->latest(),
            'stay:id,hotel_id,reservation_id,status,checked_in_at,expected_check_out_on,checked_out_at',
        ]);

        $nights = $reservation->planned_check_in_on->diffInDays($reservation->planned_check_out_on);
        $quotedTotal = $reservation->reservedRooms->sum(fn ($room): float => (float) $room->nightly_rate * $nights);

        return Inertia::render('Hotels/Reservations/Show', [
            'hotel' => $hotel,
            'reservation' => $reservation,
            'quote' => ['nights' => $nights, 'total' => number_format($quotedTotal, 2, '.', '')],
            'currency' => $settings->currency,
        ]);
    }

    public function edit(Hotel $hotel, Reservation $reservation, GeneralSettings $settings): Response
    {
        $reservation->load([
            'reservationGuests.guest.identificationType:id,code',
            'reservedRooms.room.roomType:id,name,capacity',
            'reservedRooms.reservationGuests',
        ]);

        return Inertia::render('Hotels/Reservations/Edit', [
            'hotel' => $hotel,
            'reservation' => $reservation,
            'identificationTypes' => IdentificationType::query()->orderBy('code')->get(['id', 'code']),
            'currency' => $settings->currency,
            'countries' => Countries::alpha3(),
            'subdivisions' => Countries::subdivisions($hotel->country_code ?? 'CO'),
        ]);
    }

    public function update(UpsertReservationRequest $request, Hotel $hotel, Reservation $reservation, UpdateReservation $updateReservation): RedirectResponse
    {
        $reservation = $updateReservation->execute($hotel, $reservation, ReservationData::fromValidated($request->validated()));

        return redirect()->route('hotels.reservations.show', [$hotel, $reservation])
            ->with('success', trans('reservations.messages.updated'));
    }
}
