<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\FolioAdjustmentDirection;
use App\Constants\PaymentType;
use App\Constants\PaymentVoucherFormat;
use App\Constants\StayStatus;
use App\Models\FolioAdjustment;
use App\Models\FolioCharge;
use App\Models\Payment;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use App\Models\StayFolio;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

use function is_string;

final class SummarizeStayFolio
{
    public function __construct(private CalculateOccupancyCharge $calculateOccupancyCharge) {}

    /** @return array<string, mixed> */
    public function execute(Stay $stay): array
    {
        $this->loadFolioRelations($stay);

        $folios = $stay->folios
            ->map(fn (StayFolio $folio): array => $this->summarizeFolio($stay, $folio))
            ->values();

        return [
            'folios' => $folios,
            'total_charge_minor' => $folios->sum(fn (array $folio): int => $folio['posted_charge_minor'] + $folio['projected_charge_minor']),
            'total_adjustment_minor' => $folios->sum('adjustment_minor'),
            'total_paid_minor' => $folios->sum('paid_minor'),
            'balance_minor' => $folios->sum('balance_minor'),
        ];
    }

    private function loadFolioRelations(Stay $stay): void
    {
        $stay->load([
            'folios.charges' => fn ($query) => $query->oldest('posted_at')->oldest('id'),
            'folios.adjustments' => fn ($query) => $query->oldest('occurred_at')->oldest('id'),
            'folios.payments' => fn ($query) => $query->oldest('paid_at')->oldest('id'),
            'folios.payments.voucher',
            'folios.roomOccupancies.room:id,number',
        ]);
    }

    /** @return array<string, mixed> */
    private function summarizeFolio(Stay $stay, StayFolio $folio): array
    {
        $postedChargeMinor = (int) $folio->charges->sum('total_amount_minor');
        $projectedChargeMinor = $this->projectUnchargedOccupancies($folio);
        $debits = $this->sumAdjustments($folio, FolioAdjustmentDirection::Debit);
        $credits = $this->sumAdjustments($folio, FolioAdjustmentDirection::Credit);
        $receipts = $this->sumPayments($folio, PaymentType::Receipt);
        $refunds = $this->sumPayments($folio, PaymentType::Refund);

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
            'charges' => $this->presentCharges($folio->charges),
            'adjustments' => $this->presentAdjustments($folio->adjustments),
            'payments' => $this->presentPayments($stay, $folio),
            'rooms' => $this->presentRooms($folio->roomOccupancies),
        ];
    }

    private function projectUnchargedOccupancies(StayFolio $folio): int
    {
        $projectedChargeMinor = 0;

        foreach ($folio->roomOccupancies as $occupancy) {
            if ($folio->charges->contains('room_occupancy_id', $occupancy->id)) {
                continue;
            }

            $effectiveCheckOutAt = $occupancy->checked_out_at === null ? CarbonImmutable::now() : null;
            $projectedChargeMinor += $this->calculateOccupancyCharge->execute($occupancy, $effectiveCheckOutAt)['total_amount_minor'];
        }

        return $projectedChargeMinor;
    }

    private function sumAdjustments(StayFolio $folio, FolioAdjustmentDirection $direction): int
    {
        return (int) $folio->adjustments->where('direction', $direction)->sum('amount_minor');
    }

    private function sumPayments(StayFolio $folio, PaymentType $type): int
    {
        return (int) $folio->payments->where('type', $type)->sum('amount_minor');
    }

    /**
     * @param  Collection<int, FolioCharge>  $charges
     * @return Collection<int, array<string, mixed>>
     */
    private function presentCharges(Collection $charges): Collection
    {
        return $charges->map(fn (FolioCharge $charge): array => [
            'id' => $charge->id,
            'type' => $charge->type->value,
            'description' => $charge->description,
            'quantity' => $charge->quantity,
            'unit_amount_minor' => $charge->unit_amount_minor,
            'total_amount_minor' => $charge->total_amount_minor,
            'service_start_on' => $charge->service_start_on?->toDateString(),
            'service_end_on' => $charge->service_end_on?->toDateString(),
            'posted_at' => $charge->posted_at->toDateTimeString(),
        ])->values();
    }

    /**
     * @param  Collection<int, FolioAdjustment>  $adjustments
     * @return Collection<int, array<string, mixed>>
     */
    private function presentAdjustments(Collection $adjustments): Collection
    {
        return $adjustments->map(fn (FolioAdjustment $adjustment): array => [
            'id' => $adjustment->id,
            'type' => $adjustment->type->value,
            'direction' => $adjustment->direction->value,
            'amount_minor' => $adjustment->amount_minor,
            'reason' => $adjustment->reason,
            'occurred_at' => $adjustment->occurred_at->toDateTimeString(),
        ])->values();
    }

    /** @return Collection<int, array<string, mixed>> */
    private function presentPayments(Stay $stay, StayFolio $folio): Collection
    {
        return $folio->payments
            ->map(fn (Payment $payment): array => $this->presentPayment($stay, $folio, $payment))
            ->values();
    }

    /** @return array<string, mixed> */
    private function presentPayment(Stay $stay, StayFolio $folio, Payment $payment): array
    {
        $hasSupport = is_string($payment->getRawOriginal('support_path'));
        $canPrintVoucher = $stay->status === StayStatus::CheckedOut
            && $payment->type === PaymentType::Receipt
            && $payment->voucher !== null;

        return [
            'id' => $payment->id,
            'type' => $payment->type->value,
            'method' => $payment->method->value,
            'amount_minor' => $payment->amount_minor,
            'currency' => $payment->currency,
            'comment' => $payment->comment,
            'paid_at' => $payment->paid_at->toDateTimeString(),
            'support_url' => $hasSupport
                ? route('hotels.stays.payments.support', [$folio->hotel_id, $stay->id, $payment->id])
                : null,
            'voucher_number' => $payment->voucher?->number,
            'voucher_a4_url' => $canPrintVoucher
                ? $this->voucherUrl($folio, $stay, $payment, PaymentVoucherFormat::A4)
                : null,
            'voucher_thermal_url' => $canPrintVoucher
                ? $this->voucherUrl($folio, $stay, $payment, PaymentVoucherFormat::Thermal)
                : null,
        ];
    }

    private function voucherUrl(StayFolio $folio, Stay $stay, Payment $payment, PaymentVoucherFormat $format): string
    {
        return route('hotels.stays.payments.voucher', [$folio->hotel_id, $stay->id, $payment->id, $format]);
    }

    /**
     * @param  Collection<int, RoomOccupancy>  $occupancies
     * @return Collection<int, array<string, mixed>>
     */
    private function presentRooms(Collection $occupancies): Collection
    {
        return $occupancies->map(fn (RoomOccupancy $occupancy): array => [
            'id' => $occupancy->id,
            'number' => $occupancy->room->number,
        ])->values();
    }
}
