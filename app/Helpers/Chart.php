<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class Chart
{
    /**
     * Voucher collection.
     *
     * @var \Illuminate\Support\Collection<Voucher>
     */
    protected $vouchers;

    /**
     * Voucher data.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $data;

    /**
     * Initialization
     *
     * @param  \Illuminate\Support\Collection $vouchers
     * @return void
     */
    public function __construct(Collection $vouchers)
    {
        $this->vouchers = $vouchers;
    }

    /**
     * Return Chart object
     *
     * @param  \Illuminate\Support\Collection $vouchers
     * @return \App\Helpers\Chart
     */
	public static function create(Collection $vouchers)
	{
        $chart = new Chart($vouchers);

        return $chart->group();
	}

    /**
     * Grouping by type and month
     *
     * @return \App\Helpers\Chart
     */
    public function group()
    {
        $this->data = $this->vouchers->groupBy([
            function($voucher) {
                return $voucher->type;
            }, function ($voucher)
            {
                return $voucher->created_at->month;
            }
        ]);

        return $this;
    }

    /**
     * Count the quantities of the products or services associated with the voucher.
     *
     * @return \App\Helpers\Chart
     */
    public function countItems()
    {
        $data = [];
        $types = $this->data->toArray();

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

        $this->data = $this->fillData($data);

        return $this;
    }

    /**
     * Count the amount of vouchers per month.
     *
     * @return \App\Helpers\Chart
     */
    public function countVouchers()
    {
        $data = [];
        $types = $this->data->toArray();

        foreach ($types as $type => $months) {
            foreach (array_keys($months) as $month) {
                // Check if the month exists according to the voucher type
                if (isset($types[$type][$month])) {
                    // check if the month exists in data
                    if (isset($data[$type][$month])) {
                        $data[$type][$month] += 1;
                    } else {
                        $data[$type][$month] = 1;
                    }
                } else {
                    // If the month isn't exists, the value is zero by default
                    $data[$type][$month] = 0;
                }
            }
		}

        $this->data = $this->fillData($data);

        return $this;
    }

    /**
     * Add the voucher values.
     *
     * @return \App\Helpers\Chart
     */
    public function addValues()
    {
        $data = [];
        $types = $this->data->toArray();

        foreach ($types as $type => $months) {
            foreach ($months as $month => $vouchers) {
                foreach ($vouchers as $voucher) {
					// Check if the month exists according to the voucher type
                    if (isset($types[$type][$month])) {
						// check if the month exists in data
                        if (isset($data[$type][$month])) {
							$data[$type][$month] += (float) $voucher['value'];
						} else {
							$data[$type][$month] = (float) $voucher['value'];
						}
                    } else {
						// If the month isn't exists, the value is zero by default
                        $data[$type][$month] = 0;
					}
                }
            }
		}

        $this->data = $this->fillData($data);

        return $this;
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

    /**
     * Build the object to generate the chart
     *
     * @return array $datasets
     */
    public function get()
    {
        $datasets = collect();

        foreach ($this->data as $type => $value) {
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
}