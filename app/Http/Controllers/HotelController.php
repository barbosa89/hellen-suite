<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HotelController extends Controller
{
    public function index(): Response
    {
        $hotels = Hotel::orderByDesc('id')->paginate(10);

        return Inertia::render('Hotels/Index', ['hotels' => $hotels]);
    }

    public function create(): Response
    {
        return Inertia::render('Hotels/Create');
    }

    public function store(StoreHotelRequest $request): RedirectResponse
    {
        Hotel::create($request->validated());

        return redirect()->route('hotels.index')
            ->with('success', trans('hotels.messages.created'));
    }

    public function show(Hotel $hotel): Response
    {
        return Inertia::render('Hotels/Show', ['hotel' => $hotel]);
    }

    public function edit(Hotel $hotel): Response
    {
        return Inertia::render('Hotels/Edit', ['hotel' => $hotel]);
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        $hotel->update($request->validated());

        return redirect()->route('hotels.index')
            ->with('success', trans('hotels.messages.updated'));
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $hotel->delete();

        return redirect()->route('hotels.index')
            ->with('success', trans('hotels.messages.deleted'));
    }
}
