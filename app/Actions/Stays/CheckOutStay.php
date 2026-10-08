<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\LodgingChargePolicy;
use App\Constants\RoomOccupancyEndReason;
use App\Constants\RoomOccupancyEventType;
use App\Constants\StayStatus;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckOutStay
{
    public function __construct(
        private RoomOccupancySnapshot $roomOccupancySnapshot,
        private PostLodgingCharge $postLodgingCharge,
        private CalculateFolioBalance $calculateFolioBalance,
    ) {}

    public function execute(Stay $stay, CarbonImmutable $checkedOutAt, null|int $userId = null): Stay
    {
        return DB::transaction(function () use ($stay, $checkedOutAt, $userId): Stay {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.stay_closed'),
                ]);
            }

            $occupancies = $stay->roomOccupancies()
                ->whereNull('checked_out_at')
                ->with(['room', 'guests:id'])
                ->lockForUpdate()
                ->get();

            if ($checkedOutAt->isFuture()) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.check_out_future'),
                ]);
            }

            foreach ($occupancies as $occupancy) {
                if ($checkedOutAt->isBefore($occupancy->checked_in_at)) {
                    throw ValidationException::withMessages([
                        'checked_out_at' => trans('stays.validation.check_out_before_check_in'),
                    ]);
                }

                $before = $this->roomOccupancySnapshot->execute($occupancy);

                $occupancy->update([
                    'checked_out_at' => $checkedOutAt,
                    'end_reason' => RoomOccupancyEndReason::CheckOut,
                ]);

                $this->postLodgingCharge->execute($occupancy->refresh(), $checkedOutAt, $userId);

                $occupancy->room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);
                $occupancy->events()->create([
                    'type' => RoomOccupancyEventType::CheckedOut,
                    'user_id' => $userId,
                    'before_data' => $before,
                    'after_data' => [
                        ...$this->roomOccupancySnapshot->execute($occupancy->refresh()),
                        'lodging_charge_policy' => LodgingChargePolicy::ConsumedNights->value,
                    ],
                ]);
            }

            $folios = $stay->folios()->with(['charges', 'adjustments', 'payments'])->lockForUpdate()->get();

            foreach ($folios as $folio) {
                if ($this->calculateFolioBalance->execute($folio) !== 0) {
                    throw ValidationException::withMessages([
                        'payment' => trans('payments.validation.balance_due'),
                    ]);
                }

                $folio->update([
                    'closed_at' => $checkedOutAt,
                    'closed_by_user_id' => $userId,
                ]);
            }

            $stay->update([
                'status' => StayStatus::CheckedOut,
                'checked_out_at' => $checkedOutAt,
            ]);

            $stay->stayGuests()->whereNull('checked_out_at')->update(['checked_out_at' => $checkedOutAt]);

            return $stay;
        });
    }
}
