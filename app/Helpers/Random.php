<?php

namespace App\Helpers;

use App\User;
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

	}

}