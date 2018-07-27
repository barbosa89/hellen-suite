<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class Fields
{
	public static function get($model)
	{
		return config('welkome.fields.' . $model);
	}

	public static function parsed($model)
	{
		$fields = config('welkome.fields.' . $model);
		$parsed = [];

		foreach ($fields as $field) {
			$parsed[] = $model . '.' . $field;
		}
		
		return $parsed;
	}
}