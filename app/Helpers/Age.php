<?php

namespace App\Helpers;

use Carbon\Carbon;

class Age
{
	public static function get($birthdate = '')
	{
		if (empty($birthdate)) {
			return null;
		}

		$birthdate = new Carbon($birthdate);
		$now = Carbon::now();

		return $now->diffInYears($birthdate);
	}
}