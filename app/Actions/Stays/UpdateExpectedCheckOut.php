<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\StayStatus;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateExpectedCheckOut
{
    public function execute(Stay $stay, CarbonImmutable $expectedCheckOutOn): Stay
    {
        return DB::transaction(function () use ($stay, $expectedCheckOutOn): Stay {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'expected_check_out_on' => trans('stays.validation.stay_closed'),
                ]);
            }

            $stay->update(['expected_check_out_on' => $expectedCheckOutOn]);
            $stay->roomOccupancies()
                ->whereNull('checked_out_at')
                ->update(['expected_check_out_on' => $expectedCheckOutOn]);

            return $stay;
        });
    }
}
