<?php

namespace App\Helpers;

use App\Models\Check;
use App\Models\Guest;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GuestChart
{
    protected Collection $vouchers;

    protected Carbon $startDate;

    protected Carbon $endDate;

    protected array $data = [];

    public function __construct(Collection $vouchers, Carbon $startDate, Carbon $endDate)
    {
        $this->vouchers = $vouchers;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Count each guest check by day
     */
    public function countChecks(): self
    {
        $this->vouchers->each(function ($voucher) {
            $voucher->rooms->each(function ($room) use ($voucher) {
                $room->guests->each(function ($guest) use ($voucher, $room) {
                    $checks = $voucher->checks->where('guest_id', $guest->id);
                    $maxDate = $voucher->created_at->addDays($room->pivot->quantity);

                    $checks->each(function ($check) use ($guest, $maxDate) {
                        $checkInAt = $this->getCheckInDate($check);
                        $checkOutAt = $this->getCheckOutDate($check, $maxDate);

                        $diff = $checkInAt->diffInSeconds($checkOutAt);

                        if ($diff > 0) {
                            $checkOutAt->addSeconds($diff + 1);
                        }

                        $period = CarbonPeriod::create($checkInAt, $checkOutAt);

                        foreach ($period as $date) {
                            $this->addCheck($guest, $date);
                        }
                    });
                });
            });
        });

        return $this;
    }

    private function getCheckInDate(Check $check): Carbon
    {
        return $check->in_at->lessThan($this->startDate) ? $this->startDate : $check->in_at;
    }

    private function getCheckOutDate(Check $check, Carbon $maxDate): Carbon
    {
        $checkOutAt = empty($check->out_at) ? $maxDate : $check->out_at;

        if ($checkOutAt->greaterThan($this->endDate)) {
            $checkOutAt = $this->endDate;
        }

        return $checkOutAt->subDay();
    }

    private function addCheck(Guest $guest, Carbon $date): void
    {
        $date = $date->format('Y-m-d');

        if (! array_key_exists($date, $this->data)) {
            $this->data[$date] = [];
        }

        if (! in_array($guest->id, $this->data[$date])) {
            $this->data[$date][] = $guest->id;
        }
    }

    private function fillMonthDates(): void
    {
        $month = CarbonPeriod::create($this->startDate, $this->endDate);

        foreach ($month as $day) {
            if (! array_key_exists($day->format('Y-m-d'), $this->data)) {
                $this->data[$day->format('Y-m-d')] = [];
            }
        }

        ksort($this->data);
    }

    private function buildDatasets(): array
    {
        $dataset = [];
        $set['borderWidth'] = 1;
        $colors = get_colors();
        $indexColor = 0;

        foreach ($this->data as $date => $checks) {

            $dataset['labels'][] = $date;

            if (! isset($set['label'])) {
                $set['label'] = trans('guests.title');
            }

            $set['data'][] = count($checks);
            $set['backgroundColor'][] = $colors[$indexColor]['bar'];
            $set['borderColor'][] = $colors[$indexColor]['border'];

            $indexColor++;

            if ($indexColor == 6) {
                $indexColor = 0;
            }
        }

        $dataset['datasets'][] = $set;

        return $dataset;
    }

    public function get(): array
    {
        $this->fillMonthDates();

        return $this->buildDatasets();
    }
}
