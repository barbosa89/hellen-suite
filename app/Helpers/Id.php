<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class Id
{
	public static function get($id)
	{
		if (empty($id)) {
			return null;
		}

		$id = htmlentities($id, ENT_QUOTES);

		return Hashids::decode($id)[0];
	}
}