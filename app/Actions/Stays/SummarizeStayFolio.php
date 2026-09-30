<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\FolioAdjustmentDirection;
use App\Constants\PaymentType;
use App\Models\Stay;
use Carbon\CarbonImmutable;

final class SummarizeStayFolio
{
    public function __construct(private CalculateOccupancyCharge $calculateOccupancyCharge) {}

    /** @return array<string, mixed> */
    public function execute(Stay $stay): array
    {
        $stay->loadMissing([
            'folios.charges',
            'folios.adjustments',
            'folios.payments',
            'folios.roomOccupancies.room:id,number',
        ]);

        $folios = $stay->folios->map(function ($folio): array {
            $postedChargeMinor = (int) $folio->charges->sum('total_amount_minor');
            $projectedChargeMinor = 0;

            foreach ($folio->roomOccupancies as $occupancy) {
                $hasCharge = $folio->charges->contains('room_occupancy_id', $occupancy->id);

                if (! $hasCharge) {
                    $effectiveCheckOutAt = $occupancy->checked_out_at === null ? CarbonImmutable::now() : null;
                    $projectedChargeMinor += $this->calculateOccupancyCharge->execute($occupancy, $effectiveCheckOutAt)['total_amount_minor'];
                }
            }

            $debits = (int) $folio->adjustments->where('direction', FolioAdjustmentDirection::Debit)->sum('amount_minor');
            $credits = (int) $folio->adjustments->where('direction', FolioAdjustmentDirection::Credit)->sum('amount_minor');
            $receipts = (int) $folio->payments->where('type', PaymentType::Receipt)->sum('amount_minor');
            $refunds = (int) $folio->payments->where('type', PaymentType::Refund)->sum('amount_minor');

            return [
                'id' => $folio->id,
                'label' => $folio->label,
                'currency' => $folio->currency,
                'closed_at' => $folio->closed_at?->toDateTimeString(),
                'posted_charge_minor' => $postedChargeMinor,
                'projected_charge_minor' => $projectedChargeMinor,
                'adjustment_minor' => $debits - $credits,
                'paid_minor' => $receipts - $refunds,
                'balance_minor' => $postedChargeMinor + $projectedChargeMinor + $debits - $credits - $receipts + $refunds,
                'charges' => $folio->charges->values(),
                'adjustments' => $folio->adjustments->values(),
                'payments' => $folio->payments->values(),
                'rooms' => $folio->roomOccupancies->map(fn ($occupancy): array => [
                    'id' => $occupancy->id,
                    'number' => $occupancy->room->number,
                ])->values(),
            ];
        })->values();

        return [
            'folios' => $folios,
            'total_charge_minor' => $folios->sum(fn (array $folio): int => $folio['posted_charge_minor'] + $folio['projected_charge_minor']),
            'total_adjustment_minor' => $folios->sum('adjustment_minor'),
            'total_paid_minor' => $folios->sum('paid_minor'),
            'balance_minor' => $folios->sum('balance_minor'),
        ];
    }
}
