<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Constants\PaymentMethod;
use App\Models\CashShift;
use App\Models\Hotel;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class OpenCashShift
{
    public function __construct(
        private ConvertAmountToMinor $convertAmountToMinor,
        private GeneralSettings $settings,
    ) {}

    public function execute(Hotel $hotel, string $openingAmount, null|string $note = null, null|CashShift $previousShift = null): CashShift
    {
        return DB::transaction(function () use ($hotel, $openingAmount, $note, $previousShift): CashShift {
            $hotel = Hotel::query()->lockForUpdate()->findOrFail($hotel->id);

            if ($hotel->cashShifts()->whereNull('closed_at')->exists()) {
                throw ValidationException::withMessages(['cash_shift' => trans('cash.validation.shift_already_open')]);
            }

            $shift = $hotel->cashShifts()->create([
                'number' => ((int) $hotel->cashShifts()->max('number')) + 1,
                'previous_cash_shift_id' => $previousShift?->id,
                'opened_at' => now(),
                'opening_note' => $note,
            ]);

            $shift->reconciliations()->create([
                'payment_method' => PaymentMethod::Cash,
                'currency' => $this->settings->currency,
                'opening_minor' => $this->convertAmountToMinor->execute($openingAmount),
            ]);

            return $shift;
        }, attempts: 5);
    }
}
