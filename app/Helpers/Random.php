<?php

namespace App\Helpers;

use App\User;
use App\Welkome\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * @return string
     */
	public static function consecutive(): string
	{
		$consecutive = '';

		while (empty($consecutive)) {
			// Temporary consecutive
			$temp = date('ymd') . (string) Str::of(Str::random(6))->upper();

			// Query if consecutive exists
			$voucher = Voucher::where('number', $temp)->first(['id', 'number']);

			// If the voucher is empty, the consecutive does not exist in the database
			if (empty($voucher)) {
				$consecutive = $temp;
			}
		}

		return $consecutive;
	}
}