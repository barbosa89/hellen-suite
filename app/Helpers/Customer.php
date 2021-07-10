<?php

namespace App\Helpers;

use App\Helpers\Age;
use App\Models\Voucher;
use App\Data\Views\Customer as CustomerData;

class Customer
{
    /**
     * Return the voucher customer.
     *
     * @param  \App\Models\Voucher
     * @return array
     */
    public static function get(Voucher $voucher): array
    {
        $customer = new CustomerData($voucher);

        return $customer->build()->toArray();
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
