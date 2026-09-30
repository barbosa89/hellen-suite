<?php

declare(strict_types=1);

namespace App\Actions\Folios;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Actions\Stays\SummarizeStayFolio;
use App\Constants\FolioAdjustmentDirection;
use App\Constants\FolioAdjustmentType;
use App\Models\FolioAdjustment;
use App\Models\StayFolio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RecordFolioAdjustment
{
    public function __construct(
        private ConvertAmountToMinor $convertAmountToMinor,
        private SummarizeStayFolio $summarizeStayFolio,
    ) {}

    public function execute(StayFolio $folio, FolioAdjustmentType $type, string $amount, string $reason, null|int $userId): FolioAdjustment
    {
        return DB::transaction(function () use ($folio, $type, $amount, $reason, $userId): FolioAdjustment {
            $folio = StayFolio::query()->with('stay')->lockForUpdate()->findOrFail($folio->id);

            if ($folio->closed_at !== null) {
                throw ValidationException::withMessages(['adjustment' => trans('payments.validation.folio_closed')]);
            }

            $amountMinor = $this->convertAmountToMinor->execute($amount);
            $summary = $this->summarizeStayFolio->execute($folio->stay);
            $folioSummary = collect($summary['folios'])->firstWhere('id', $folio->id);

            if ($amountMinor > $folioSummary['balance_minor']) {
                throw ValidationException::withMessages(['amount' => trans('payments.validation.over_adjustment')]);
            }

            return $folio->adjustments()->create([
                'type' => $type,
                'direction' => FolioAdjustmentDirection::Credit,
                'amount_minor' => $amountMinor,
                'reason' => $reason,
                'occurred_at' => now(),
                'idempotency_key' => (string) Str::uuid(),
                'recorded_by_user_id' => $userId,
            ]);
        });
    }
}
