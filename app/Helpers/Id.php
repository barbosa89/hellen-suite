<?php

namespace App\Helpers;

use App\Helpers\Input;
use Vinkla\Hashids\Facades\Hashids;

class Id
{
	/**
     * Unhash an ID.
     *
     * @param  string  		$ids
     * @return int|array
     */
	public static function get($ids)
	{
		if (empty($ids)) {
			return null;
		}

		if (is_array($ids)) {
			return self::pool($ids);
		}

		$id = Input::clean($ids);

		return Hashids::decode($ids)[0];
	}

	/**
     * Unhash an ID collection.
     *
     * @param  array	$ids
     * @return array
     */
	public static function pool($ids)
	{
		$collection = [];

		array_walk($ids, function ($id) use (&$collection)
		{
			array_push($collection, Hashids::decode($id)[0]);
		});

		return $collection;
	}
}