<?php

declare(strict_types=1);

namespace App\Actions\Hotels;

use App\Constants\FolioAdjustmentDirection;
use App\Constants\HousekeepingStatus;
use App\Constants\PaymentType;
use App\Constants\ReservationStatus;
use App\Constants\StayStatus;
use App\Models\Hotel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SummarizeHotelDashboard
{
    /** @return array<string, mixed> */
    public function execute(Hotel $hotel, null|string $currency): array
    {
        $today = today();
        $forecastEnd = $today->copy()->addDays(7);

        $activeRooms = $hotel->rooms()->where('is_active', true)->count();
        $currentOccupancies = DB::table('room_occupancies as room_occupancies')
            ->join('rooms', 'rooms.id', '=', 'room_occupancies.room_id')
            ->where('rooms.hotel_id', $hotel->getKey())
            ->where('rooms.is_active', true)
            ->whereNull('room_occupancies.checked_out_at')
            ->groupBy('room_occupancies.room_id')
            ->selectRaw('room_occupancies.room_id, MAX(room_occupancies.nightly_rate) AS nightly_rate');

        $occupancySnapshot = DB::query()
            ->fromSub($currentOccupancies, 'current_occupancies')
            ->selectRaw('COUNT(*) AS occupied_rooms, COALESCE(SUM(nightly_rate), 0) AS nightly_revenue')
            ->first();

        $occupiedRooms = (int) ($occupancySnapshot?->occupied_rooms ?? 0);
        $nightlyRevenue = (float) ($occupancySnapshot?->nightly_revenue ?? 0);
        $occupancyRate = $activeRooms > 0 ? round(($occupiedRooms / $activeRooms) * 100, 1) : 0.0;
        $adr = $occupiedRooms > 0 ? round($nightlyRevenue / $occupiedRooms, 2) : 0.0;
        $revPar = $activeRooms > 0 ? round($nightlyRevenue / $activeRooms, 2) : 0.0;

        $arrivalsToday = $hotel->reservations()
            ->where('status', ReservationStatus::Confirmed)
            ->where('planned_check_in_on', '>=', $today->toDateString())
            ->where('planned_check_in_on', '<', $today->copy()->addDay()->toDateString())
            ->count();

        $departuresToday = $hotel->stays()
            ->where('status', StayStatus::Active)
            ->where('expected_check_out_on', '>=', $today->toDateString())
            ->where('expected_check_out_on', '<', $today->copy()->addDay()->toDateString())
            ->count();

        $guestsInHouse = DB::table('guest_room_occupancy as guest_room')
            ->join('room_occupancies as occupancy', 'occupancy.id', '=', 'guest_room.room_occupancy_id')
            ->join('rooms', 'rooms.id', '=', 'occupancy.room_id')
            ->where('rooms.hotel_id', $hotel->getKey())
            ->whereNull('occupancy.checked_out_at')
            ->distinct()
            ->count('guest_room.guest_id');

        $dirtyRooms = $hotel->rooms()
            ->where('is_active', true)
            ->where('housekeeping_status', HousekeepingStatus::Dirty)
            ->count();

        return [
            'asOf' => $today->toDateString(),
            'currency' => $currency,
            'metrics' => [
                'activeRooms' => $activeRooms,
                'occupiedRooms' => $occupiedRooms,
                'availableRooms' => max($activeRooms - $occupiedRooms, 0),
                'dirtyRooms' => $dirtyRooms,
                'occupancyRate' => $occupancyRate,
                'adr' => $adr,
                'revPar' => $revPar,
                'arrivalsToday' => $arrivalsToday,
                'departuresToday' => $departuresToday,
                'guestsInHouse' => $guestsInHouse,
                'outstandingBalanceMinor' => $this->outstandingBalance($hotel, $currency),
            ],
            'forecast' => $this->occupancyForecast($hotel, $today, $forecastEnd, $activeRooms),
        ];
    }

    private function outstandingBalance(Hotel $hotel, null|string $currency): int
    {
        if ($currency === null) {
            return 0;
        }

        $scopeFolio = static function (Builder $query) use ($hotel, $currency): void {
            $query->where('folios.hotel_id', $hotel->getKey())
                ->where('folios.currency', $currency);
        };

        $charges = DB::table('folio_charges as entry')
            ->join('stay_folios as folios', 'folios.id', '=', 'entry.stay_folio_id')
            ->where($scopeFolio)
            ->selectRaw('CAST(entry.total_amount_minor AS INTEGER) AS amount_minor');

        $adjustments = DB::table('folio_adjustments as entry')
            ->join('stay_folios as folios', 'folios.id', '=', 'entry.stay_folio_id')
            ->where($scopeFolio)
            ->selectRaw(
                'CASE WHEN entry.direction = ? THEN CAST(entry.amount_minor AS INTEGER) ELSE -CAST(entry.amount_minor AS INTEGER) END AS amount_minor',
                [FolioAdjustmentDirection::Debit->value],
            );

        $payments = DB::table('payments as entry')
            ->join('stay_folios as folios', 'folios.id', '=', 'entry.stay_folio_id')
            ->where($scopeFolio)
            ->selectRaw(
                'CASE WHEN entry.type = ? THEN CAST(entry.amount_minor AS INTEGER) ELSE -CAST(entry.amount_minor AS INTEGER) END AS amount_minor',
                [PaymentType::Refund->value],
            );

        $ledger = $charges->unionAll($adjustments)->unionAll($payments);

        return (int) DB::query()->fromSub($ledger, 'ledger')->sum('amount_minor');
    }

    /** @return array<int, array{date: string, occupiedRooms: int, occupancyRate: float}> */
    private function occupancyForecast(Hotel $hotel, Carbon $start, Carbon $end, int $activeRooms): array
    {
        $occupancies = DB::table('room_occupancies as occupancy')
            ->join('rooms', 'rooms.id', '=', 'occupancy.room_id')
            ->where('rooms.hotel_id', $hotel->getKey())
            ->where('rooms.is_active', true)
            ->whereNull('occupancy.checked_out_at')
            ->where('occupancy.checked_in_at', '<', $end)
            ->where('occupancy.expected_check_out_on', '>', $start)
            ->get(['occupancy.room_id', 'occupancy.checked_in_at', 'occupancy.expected_check_out_on']);

        $reservations = DB::table('reserved_rooms as reserved_room')
            ->join('reservations', 'reservations.id', '=', 'reserved_room.reservation_id')
            ->join('rooms', 'rooms.id', '=', 'reserved_room.room_id')
            ->where('rooms.hotel_id', $hotel->getKey())
            ->where('rooms.is_active', true)
            ->where('reservations.status', ReservationStatus::Confirmed)
            ->where('reserved_room.planned_check_in_on', '<', $end)
            ->where('reserved_room.planned_check_out_on', '>', $start)
            ->get(['reserved_room.room_id', 'reserved_room.planned_check_in_on', 'reserved_room.planned_check_out_on']);

        return collect(range(0, 6))->map(function (int $offset) use ($start, $activeRooms, $occupancies, $reservations): array {
            $date = $start->copy()->addDays($offset)->toDateString();
            $occupiedRoomIds = [];

            foreach ($occupancies as $occupancy) {
                $checkInOn = Carbon::parse($occupancy->checked_in_at)->toDateString();
                $checkOutOn = Carbon::parse($occupancy->expected_check_out_on)->toDateString();

                if ($checkInOn <= $date && $checkOutOn > $date) {
                    $occupiedRoomIds[(int) $occupancy->room_id] = true;
                }
            }

            foreach ($reservations as $reservation) {
                $checkInOn = Carbon::parse($reservation->planned_check_in_on)->toDateString();
                $checkOutOn = Carbon::parse($reservation->planned_check_out_on)->toDateString();

                if ($checkInOn <= $date && $checkOutOn > $date) {
                    $occupiedRoomIds[(int) $reservation->room_id] = true;
                }
            }

            $occupiedRooms = count($occupiedRoomIds);

            return [
                'date' => $date,
                'occupiedRooms' => $occupiedRooms,
                'occupancyRate' => $activeRooms > 0 ? round(($occupiedRooms / $activeRooms) * 100, 1) : 0.0,
            ];
        })->all();
    }
}
