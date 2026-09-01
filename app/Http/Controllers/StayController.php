<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Stays\CreateStay;
use App\Actions\Stays\CreateStayData;
use App\Actions\Stays\SummarizeStayCosts;
use App\Constants\HousekeepingStatus;
use App\Http\Requests\Stays\StoreStayRequest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use App\Models\Stay;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StayController extends Controller
{
    public function index(Hotel $hotel): Response
    {
        $stays = $hotel->stays()
            ->select(['id', 'hotel_id', 'responsible_guest_id', 'status', 'checked_in_at', 'expected_check_out_on', 'checked_out_at'])
            ->with('responsibleGuest:id,first_name,last_name,identification_number')
            ->withCount(['roomOccupancies as active_room_occupancies_count' => fn ($query) => $query->whereNull('checked_out_at')])
            ->latest('checked_in_at')
            ->paginate()
            ->withQueryString();

        return Inertia::render('Hotels/Stays/Index', [
            'hotel' => $hotel,
            'stays' => $stays,
        ]);
    }

    public function create(Hotel $hotel, GeneralSettings $settings): Response
    {
        $rooms = $hotel->rooms()
            ->select(['id', 'hotel_id', 'room_type_id', 'number', 'floor', 'reference_price'])
            ->with('roomType:id,name,capacity')
            ->where('is_active', true)
            ->where('housekeeping_status', HousekeepingStatus::Clean)
            ->whereDoesntHave('roomOccupancies', fn ($query) => $query->whereNull('checked_out_at'))
            ->orderBy('number')
            ->get();

        return Inertia::render('Hotels/Stays/Create', [
            'hotel' => $hotel,
            'rooms' => $rooms,
            'identificationTypes' => IdentificationType::query()->orderBy('code')->get(['id', 'code']),
            'currency' => $settings->currency,
        ]);
    }

    public function store(StoreStayRequest $request, Hotel $hotel, CreateStay $createStay): RedirectResponse
    {
        $stay = $createStay->execute($hotel, CreateStayData::fromValidated($request->validated()));

        return redirect()->route('hotels.stays.show', [$hotel, $stay])
            ->with('success', trans('stays.messages.checked_in'));
    }

    public function show(Hotel $hotel, Stay $stay, GeneralSettings $settings, SummarizeStayCosts $summarizeStayCosts): Response
    {
        $stay->load([
            'responsibleGuest:id,first_name,last_name,identification_number',
            'stayGuests.guest:id,first_name,last_name,identification_number',
            'roomOccupancies' => fn ($query) => $query->oldest('checked_in_at')->oldest('id'),
            'roomOccupancies.room:id,number,floor,room_type_id',
            'roomOccupancies.room.roomType:id,name,capacity',
            'roomOccupancies.guests:id,first_name,last_name,identification_number',
        ]);

        return Inertia::render('Hotels/Stays/Show', [
            'hotel' => $hotel,
            'stay' => $stay,
            'stayCostSummary' => $summarizeStayCosts->execute($stay),
            'identificationTypes' => IdentificationType::query()->orderBy('code')->get(['id', 'code']),
            'rooms' => $hotel->rooms()
                ->select(['id', 'hotel_id', 'room_type_id', 'number', 'floor', 'reference_price'])
                ->with('roomType:id,name,capacity')
                ->where('is_active', true)
                ->where('housekeeping_status', HousekeepingStatus::Clean)
                ->whereDoesntHave('roomOccupancies', fn ($query) => $query->whereNull('checked_out_at'))
                ->orderBy('number')
                ->get(),
            'currency' => $settings->currency,
        ]);
    }
}
