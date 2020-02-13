<?php

namespace App\Helpers;

use App\Helpers\Age;
use App\Welkome\Voucher;
use Vinkla\Hashids\Facades\Hashids;

class Customer
{
    /**
     * Return the voucher customer.
     *
     * @param  \App\Welkome\Voucher
     * @return array
     */
    public static function get(Voucher $voucher): array
    {
        $customer = [];

        if (empty($voucher->company)) {
            if ($voucher->guests->isNotEmpty()) {
                $main = $voucher->guests->first(function ($guest, $index) {
                    return $guest->pivot->main == true;
                });

                $customer['name'] = $main->full_name;
                $customer['tin'] = $main->dni;
                $customer['route'] = route('guests.show', ['id' => Hashids::encode($main->id)]);
                $customer['email'] = $main->email ? $main->email : '';
                $customer['address'] = $main->address ? $main->address : '';
                $customer['phone'] = $main->phone ? $main->phone : '';
            }
        } else {
            $customer['name'] = $voucher->company->business_name;
            $customer['tin'] = $voucher->company->tin;
            $customer['route'] = route('companies.show', ['id' => Hashids::encode($voucher->company->id)]);
            $customer['email'] = $voucher->company->email ? $voucher->company->email : '';
            $customer['address'] = $voucher->company->address ? $voucher->company->address : '';
            $customer['phone'] = $voucher->company->phone ? $voucher->company->phone : '';
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