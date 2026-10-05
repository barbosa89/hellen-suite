<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Models\CashShift;
use App\Models\Hotel;
use Illuminate\Validation\ValidationException;

final class GetOpenCashShift
{
    public function execute(Hotel $hotel, bool $lock = false): CashShift
    {
        $query = $hotel->cashShifts()->whereNull('closed_at');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first() ?? throw ValidationException::withMessages([
            'cash_shift' => trans('cash.validation.shift_required'),
        ]);
    }
}
