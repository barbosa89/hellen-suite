<?php

declare(strict_types=1);

namespace App\Helpers;

class Parameter
{
	/**
     * Remove malicious chars from input param.
     *
     * @param  int|string $value
     * @return int|string
     */
	public static function clean($value = null, $encoding = 'UTF-8')
	{
		return htmlentities($value, ENT_QUOTES | ENT_HTML5, $encoding);
	}

	/**
     * Convert value to boolean value.
     *
     * @param  int|string $value
     * @return boolean
     */
	public static function bool($value = null): bool
	{
		if (is_null($value)) {
			return false;
		}

		if ((int) $value) {
			return true;
		}

		if ($value == 'on') {
			return true;
		}

		return false;
	}
}