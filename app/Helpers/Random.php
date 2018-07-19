<?php

namespace App\Helpers;

use App\User;
use App\Welkome\Invoice;
use Illuminate\Http\Request;

class Random
{
	public static function token($lenght = 32)
	{
		$token = '';
		
		while (empty($token)) {
			$temp = str_random($lenght);
			$user = User::where('token', $temp)->first(['id', 'token']);

			if (empty($user)) {
				$token = $temp;
			}
		}

		return $token;
	}

	public static function consecutive()
	{
		$consecutive = '';
			
		while (empty($consecutive)) {
			$temp = date('y') . date('m') . date('d') . random_int(0, 99999);
			$invoice = Invoice::where('number', $temp)->first(['id', 'number']);
	
			if (empty($invoice)) {
				$consecutive = $temp;
			}
		}

		return $consecutive;
	}
}