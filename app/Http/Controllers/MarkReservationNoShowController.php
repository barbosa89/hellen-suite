<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Reservations\MarkReservationNoShow;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;

class MarkReservationNoShowController extends Controller
{
    public function __invoke(Hotel $hotel, Reservation $reservation, MarkReservationNoShow $markReservationNoShow): RedirectResponse
    {
        $markReservationNoShow->execute($hotel, $reservation);

        return back()->with('success', trans('reservations.messages.no_show'));
    }
}
