<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Reservations\CancelReservation;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;

class CancelReservationController extends Controller
{
    public function __invoke(Hotel $hotel, Reservation $reservation, CancelReservation $cancelReservation): RedirectResponse
    {
        $cancelReservation->execute($hotel, $reservation);

        return back()->with('success', trans('reservations.messages.cancelled'));
    }
}
