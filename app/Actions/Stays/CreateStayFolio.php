<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Models\RoomOccupancy;
use App\Models\StayFolio;

final class CreateStayFolio
{
    public function execute(RoomOccupancy $occupancy): StayFolio
    {
        if ($occupancy->stay_folio_id !== null) {
            $occupancy->loadMissing('folio');

            return $occupancy->folio;
        }

        $occupancy->loadMissing(['stay', 'room:id,number']);

        $folio = $occupancy->stay->folios()->create([
            'hotel_id' => $occupancy->stay->hotel_id,
            'label' => trans('payments.folio.room', ['room' => $occupancy->room->number]),
            'currency' => $occupancy->stay->currency,
        ]);

        $occupancy->update(['stay_folio_id' => $folio->id]);

        return $folio;
    }
}
