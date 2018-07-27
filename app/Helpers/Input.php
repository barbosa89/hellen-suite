<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class Input
{
	public static function get(Request $request, $parameter)
	{
		return htmlentities($request->get($parameter), ENT_QUOTES);
	}
}