<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\FolioChargeType;
use App\Models\FolioCharge;
use App\Models\RoomOccupancy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

final class PostLodgingCharge
{
    public function __construct(
        private CreateStayFolio $createStayFolio,
        private CalculateOccupancyCharge $calculateOccupancyCharge,
    ) {}

    public function execute(RoomOccupancy $occupancy, CarbonImmutable $checkedOutAt, null|int $userId = null): null|FolioCharge
    {
        $existing = $occupancy->charges()->where('type', FolioChargeType::Lodging)->first();

        if ($existing instanceof FolioCharge) {
            return $existing;
        }

        $folio = $this->createStayFolio->execute($occupancy);
        $amounts = $this->calculateOccupancyCharge->execute($occupancy, $checkedOutAt);

        if ($amounts['nights'] === 0) {
            return null;
        }

        return $folio->charges()->create([
            'type' => FolioChargeType::Lodging,
            'description' => trans('payments.charges.lodging'),
            'quantity' => $amounts['nights'],
            'unit_amount_minor' => $amounts['unit_amount_minor'],
            'total_amount_minor' => $amounts['total_amount_minor'],
            'service_start_on' => $amounts['start_on'],
            'service_end_on' => $amounts['end_on'],
            'posted_at' => now(),
            'idempotency_key' => (string) Str::uuid(),
            'room_occupancy_id' => $occupancy->id,
            'recorded_by_user_id' => $userId,
        ]);
    }
}
