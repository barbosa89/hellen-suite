<?php

namespace App\Helpers;

class Fields
{
	/**
	 * Retrieve column names to execute a query by model
	 *
	 * @param string $model
	 * @return array
	 */
	public static function get(string $model): array
	{
		return config('welkome.fields.' . $model);
	}

	/**
	 * Retrieve column names in dot notacion to execute a query by model
	 * Example: model.column_name
	 *
	 * @param string $model
	 * @return array
	 */
	public static function parsed(string $model): array
	{
		$parsed = [];

		foreach (self::get($model) as $field) {
			$parsed[] = $model . '.' . $field;
		}

		return $parsed;
	}
}