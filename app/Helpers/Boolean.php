<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class Boolean
{
	public static function get($field = null)
	{
		if (is_null($field)) {
			return false;
		}

		if ((int) $field) {
			return true;
		}

		if ($field == 'on') {
			return true;
		}

		return false;
	}
}