<?php

declare(strict_types=1);

namespace App\Actions\Folios;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Constants\FolioChargeType;
use App\Models\FolioCharge;
use App\Models\StayFolio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RecordFolioCharge
{
    public function __construct(private ConvertAmountToMinor $convertAmountToMinor) {}

    public function execute(StayFolio $folio, string $description, string $amount, null|int $userId): FolioCharge
    {
        return DB::transaction(function () use ($folio, $description, $amount, $userId): FolioCharge {
            $folio = StayFolio::query()->lockForUpdate()->findOrFail($folio->id);

            if ($folio->closed_at !== null) {
                throw ValidationException::withMessages(['charge' => trans('payments.validation.folio_closed')]);
            }

            $amountMinor = $this->convertAmountToMinor->execute($amount);

            return $folio->charges()->create([
                'type' => FolioChargeType::Manual,
                'description' => $description,
                'quantity' => 1,
                'unit_amount_minor' => $amountMinor,
                'total_amount_minor' => $amountMinor,
                'posted_at' => now(),
                'idempotency_key' => (string) Str::uuid(),
                'recorded_by_user_id' => $userId,
            ]);
        });
    }
}
