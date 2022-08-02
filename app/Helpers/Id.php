<?php

declare(strict_types=1);

namespace App\Helpers;

use Throwable;
use App\Helpers\Parameter;
use Vinkla\Hashids\Facades\Hashids;

class Id
{
	public static function decode(string $id): int
	{
		try {
			$id = Parameter::clean($id);

			return Hashids::decode($id)[0];
		} catch (Throwable $e) {
			report($e);

			return 0;
		}
	}

	public static function encode(int $id): string
	{
		return (string) Hashids::encode($id);
	}

	/**
     * Decode an ID's array.
     *
     * @param  array $ids
     * @return array
     */
	public static function pool(array $ids): array
	{
		$collection = [];

		array_walk($ids, function ($id) use (&$collection) {
			array_push($collection, self::decode($id));
		});

		return $collection;
	}

	/**
     * Return de User parent ID.
     *
     * @return integer
     */
	public static function parent(): int
	{
		if (empty(auth()->user()->parent)) {
			return auth()->user()->id;
		}

		return auth()->user()->parent;
	}
}
