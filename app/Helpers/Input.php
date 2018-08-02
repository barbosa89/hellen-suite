<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class Input
{
	public static function clean($value)
	{
		if (empty($value)) {
			return null;
		}
		
		return htmlentities($value, ENT_QUOTES);
	}

	public static function bool($field = null)
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