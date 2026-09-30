<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\FolioAdjustmentDirection;
use App\Constants\PaymentType;
use App\Models\StayFolio;

final class CalculateFolioBalance
{
    public function execute(StayFolio $folio): int
    {
        $folio->loadMissing(['charges', 'adjustments', 'payments']);

        $charges = $folio->charges->sum('total_amount_minor');
        $debits = $folio->adjustments->where('direction', FolioAdjustmentDirection::Debit)->sum('amount_minor');
        $credits = $folio->adjustments->where('direction', FolioAdjustmentDirection::Credit)->sum('amount_minor');
        $receipts = $folio->payments->where('type', PaymentType::Receipt)->sum('amount_minor');
        $refunds = $folio->payments->where('type', PaymentType::Refund)->sum('amount_minor');

        return (int) ($charges + $debits - $credits - $receipts + $refunds);
    }
}
