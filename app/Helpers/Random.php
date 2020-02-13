<?php

namespace App\Helpers;

use App\User;
use App\Welkome\Voucher;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class Random
{
    /**
     * Create an unique token from users table.
     *
     * @param  int		$lenght
     * @return string	$token
     */
	public static function token($lenght = 32)
	{
		$token = '';

		while (empty($token)) {
			$temp = Str::random($lenght);
			$user = User::where('token', $temp)->first(['id', 'token']);

			if (empty($user)) {
				$token = $temp;
			}
		}

		return $token;
	}

	/**
     * Create an unique consecutive from vouchers table.
     *
     * @param  int		$lenght
     * @return string	$token
     */
	public static function consecutive()
	{
		$consecutive = '';

		while (empty($consecutive)) {
			$temp = date('y') . date('m') . date('d') . random_int(0, 99999);
			$voucher = Voucher::where('number', $temp)->first(['id', 'number']);

			if (empty($voucher)) {
				$consecutive = $temp;
			}
		}

		return $consecutive;
	}
}