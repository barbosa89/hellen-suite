<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Guests\StoreGuestRequest;
use App\Http\Requests\Guests\UpdateGuestRequest;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use App\Support\Countries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{
    public function index(Request $request, Hotel $hotel): Response
    {
        $search = $request->string('search')->trim()->toString();

        $guests = $hotel->guests()
            ->select(['id', 'hotel_id', 'identification_type_id', 'first_name', 'last_name', 'identification_number', 'mobile', 'email'])
            ->with('identificationType:id,code')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('identification_number', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate()
            ->withQueryString();

        return Inertia::render('Hotels/Guests/Index', [
            'hotel' => $hotel,
            'guests' => $guests,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(Hotel $hotel): Response
    {
        return Inertia::render('Hotels/Guests/Create', [
            'hotel' => $hotel,
            'identificationTypes' => IdentificationType::query()->orderBy('code')->get(['id', 'code']),
            'countries' => Countries::alpha3(),
        ]);
    }

    public function store(StoreGuestRequest $request, Hotel $hotel): RedirectResponse
    {
        $guest = $hotel->guests()->create($request->validated());

        return redirect()->route('hotels.guests.show', [$hotel, $guest])
            ->with('success', trans('guests.messages.created'));
    }

    public function show(Hotel $hotel, Guest $guest): Response
    {
        $guest->load('identificationType:id,code');
        $stayGuests = $guest->stayGuests()
            ->with(['stay.responsibleGuest:id,first_name,last_name'])
            ->latest()
            ->paginate()
            ->withQueryString();

        return Inertia::render('Hotels/Guests/Show', [
            'hotel' => $hotel,
            'guest' => $guest,
            'stayGuests' => $stayGuests,
        ]);
    }

    public function edit(Hotel $hotel, Guest $guest): Response
    {
        return Inertia::render('Hotels/Guests/Edit', [
            'hotel' => $hotel,
            'guest' => $guest,
            'identificationTypes' => IdentificationType::query()->orderBy('code')->get(['id', 'code']),
            'countries' => Countries::alpha3(),
        ]);
    }

    public function update(UpdateGuestRequest $request, Hotel $hotel, Guest $guest): RedirectResponse
    {
        $guest->update($request->validated());

        return redirect()->route('hotels.guests.show', [$hotel, $guest])
            ->with('success', trans('guests.messages.updated'));
    }
}
