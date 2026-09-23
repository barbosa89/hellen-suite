<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Reservations\CheckInReservation;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;

class CheckInReservationController extends Controller
{
    public function __invoke(Hotel $hotel, Reservation $reservation, CheckInReservation $checkInReservation): RedirectResponse
    {
        $stay = $checkInReservation->execute($hotel, $reservation);

        return redirect()->route('hotels.stays.show', [$hotel, $stay])
            ->with('success', trans('reservations.messages.checked_in'));
    }
}
