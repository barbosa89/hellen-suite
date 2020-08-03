<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;

class Columns
{
	public static function get(string $model, string $connection = 'mysql')
	{
		return Schema::connection($connection)->getColumnListing($model);
	}
}