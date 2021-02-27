<?php

namespace App\Helpers;

use Closure;
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
     * Create the chart array data
     *
     * @param  \Closure $calc
     * @return void
     */
    public function process(Closure $calc)
    {
        $types = $this->data->toArray();
        $data = [];

        foreach ($types as $type => $months) {
            foreach ($months as $month => $vouchers) {
                foreach ($vouchers as $voucher) {
                    // Check if the month exists according to the voucher type
                    if (isset($types[$type][$month])) {
                        // check if the month exists in data
                        if (isset($data[$type][$month])) {
                            $data[$type][$month] += $calc($vouchers, $voucher);
                        } else {
                            $data[$type][$month] = $calc($vouchers, $voucher);
                        }
                    } else {
                        // If the month isn't exists, the value is zero by default
                        $data[$type][$month] = 0;
                    }
                }
            }
        }

        // Set new data
        $this->data = $this->fillData($data);
    }

    /**
     * Count the quantities of the products or services associated with the voucher.
     *
     * @return \App\Helpers\Chart
     */
    public function countItems()
    {
        $this->process(function ($vouchers, $voucher)
        {
            // Add quantity in the pivot table
            return $voucher['pivot']['quantity'];
        });

        return $this;
    }

    /**
     * Count the amount of vouchers per month.
     *
     * @return \App\Helpers\Chart
     */
    public function countVouchers()
    {
        $this->process(function ($vouchers, $voucher)
        {
            return 1;
        });

        return $this;
    }

    /**
     * Add the voucher values.
     *
     * @return \App\Helpers\Chart
     */
    public function addValues()
    {
        $this->process(function ($vouchers, $voucher)
        {
            // Add voucher value
            return (float) $voucher['value'];
        });

        return $this;
    }

    /**
     * Add the voucher item value.
     *
     * @return \App\Helpers\Chart
     */
    public function addItemValues()
    {
        $this->process(function ($vouchers, $voucher)
        {
            // Add voucher item value
            return (float) $voucher['pivot']['value'];
        });

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
                // Check if the month exists in the array
                if (!isset($data[$type][$i])) {
                    $data[$type][$i] = 0;
                }

                // Order by keys
                ksort($data[$type]);
            }
        }

        return $data;
    }

    /**
     * Build the array to generate the chart
     *
     * @return array $datasets
     */
    public function get()
    {
        $datasets = collect();

        foreach ($this->data as $type => $value) {
            // Set label by voucher type
            $set['label'] = trans('transactions.' . $type);

            // Get type data
            $set['data'] = array_values($value);

            // Fill background color and border color of chart bars by voucher type
            for ($i=0; $i < count($set['data']); $i++) {
                $set['backgroundColor'][] = config('settings.colors')[$type]['bar'];
                $set['borderColor'][] = config('settings.colors')[$type]['border'];
            }

            // Set border size
            $set['borderWidth'] = 1;

            // Add new set
            $datasets->push($set);

            // Destroy set
            unset($set);
        }

        return $datasets;
    }
}
