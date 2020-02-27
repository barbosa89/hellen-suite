<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class Chart
{
    /**
     * Return datasets to build a chart
     *
     * @param  \Illuminate\Support\Collection $vouchers
     * @return array $data
     */
	public static function data(Collection $vouchers)
	{
		$chart = new Chart;
		$grouped = $chart->groupVoucherTypesByMonth($vouchers);
		$data = $chart->prepareChartData($grouped->toArray());

		return $chart->buildDatasets($data);
	}

    /**
     * Grouping by type and month
     *
     * @param  \Illuminate\Support\Collection $vouchers
     * @return array $types
     */
    public function groupVoucherTypesByMonth(Collection $vouchers)
    {
        $types = $vouchers->groupBy([
            function($voucher) {
                return $voucher->type;
            }, function ($voucher)
            {
                return $voucher->created_at->month;
            }
        ]);

        return $types;
    }

    /**
     * Prepare chart data by voucher type in a yearly period.
     *
     * @param  array $types
     * @return array $data
     */
    public function prepareChartData(array $types)
    {
        $data = [];

        foreach ($types as $type => $months) {
            foreach ($months as $month => $vouchers) {
                foreach ($vouchers as $voucher) {
					// Check if the month exists according to the voucher type
                    if (isset($types[$type][$month])) {
						// check if the month exists in data
                        if (isset($data[$type][$month])) {
							$data[$type][$month] += $voucher['pivot']['quantity'];
						} else {
							$data[$type][$month] = $voucher['pivot']['quantity'];
						}
                    } else {
						// If the month isn't exists, the value is zero by default
                        $data[$type][$month] = 0;
					}
                }
            }
		}

        return $this->fillData($data);
    }

    /**
     * Build the object to generate the chart
     *
     * @param  array $data
     * @return array $datasets
     */
    public function buildDatasets(array $data)
    {
        $filled = $this->fillData($data);
        $datasets = collect();

        foreach ($filled as $type => $value) {
            $set['label'] = trans('transactions.' . $type);
            $set['data'] = array_values($value);

            foreach (array_keys($value) as $month) {
                $set['backgroundColor'][] = config('welkome.colors')[$type]['bar'];
                $set['borderColor'][] = config('welkome.colors')[$type]['border'];
            }

            $set['borderWidth'] = 1;

            $datasets->push($set);
            unset($set);
        }

        return $datasets;
    }

	/**
     * Assign the default zero value for months that have no vouchers
     *
     * @param  array $data
     * @return array $data
     */
    public function fillData(array $data)
    {
        for ($i=1; $i <= 12; $i++) {
            foreach (array_keys($data) as $type) {
                if (!isset($data[$type][$i])) {
                    $data[$type][$i] = 0;
                }

                ksort($data[$type]);
            }
        }

        return $data;
    }
}