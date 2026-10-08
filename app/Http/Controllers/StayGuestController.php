<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Stays\AddGuestToStay;
use App\Actions\Tra\DispatchTraSubmissions;
use App\Http\Requests\Stays\AddGuestToStayRequest;
use App\Models\Hotel;
use App\Models\Stay;
use Illuminate\Http\RedirectResponse;

class StayGuestController extends Controller
{
    public function __invoke(AddGuestToStayRequest $request, Hotel $hotel, Stay $stay, AddGuestToStay $addGuestToStay, DispatchTraSubmissions $dispatchTraSubmissions): RedirectResponse
    {
        $addGuestToStay->execute($hotel, $stay, $request->validated());
        $dispatchTraSubmissions->execute($stay->refresh());

        return back()->with('success', trans('stays.messages.guest_added'));
    }
}
