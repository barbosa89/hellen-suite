<?php

namespace App\Helpers;

use App\Helpers\Input;
use Vinkla\Hashids\Facades\Hashids;

class Id
{
	public static function get($id)
	{
		if (empty($id)) {
			return null;
		}

		$id = Input::clean($id);

		return Hashids::decode($id)[0];
	}
}