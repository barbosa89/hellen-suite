<?php

namespace App\Helpers;

use App\Helpers\Age;
use App\Welkome\Invoice;
use Vinkla\Hashids\Facades\Hashids;

class Customer
{
    /**
     * Return the invoice customer.
     *
     * @param  \App\Welkome\Invoice
     * @return array
     */
    public static function get(Invoice $invoice): array
    {
        $customer = [];

        if (empty($invoice->company)) {
            if ($invoice->guests->isNotEmpty()) {
                $main = $invoice->guests->first(function ($guest, $index) {
                    return $guest->pivot->main == true;
                });
                // dd($invoice->guests);

                $customer['name'] = $main->full_name;
                $customer['tin'] = $main->dni;
                $customer['route'] = route('guests.show', ['id' => Hashids::encode($main->id)]);
            }
        } else {
            $customer['name'] = $invoice->company->business_name;
            $customer['tin'] = $invoice->company->tin;
            $customer['route'] = route('guests.show', ['id' => Hashids::encode($invoice->company->id)]);
        }

        return $customer;
	}

    /**
     * Check if the guest customer is a minor.
     *
     * @param string $birthdate
     * @return boolean
     */
    public static function isMinor($birthdate = '')
    {
        if (empty($birthdate)) {
            return false;
        }

        $age = Age::get($birthdate);

        if ($age < 18) {
            return true;
        }

        return false;
    }
}