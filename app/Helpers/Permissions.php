<?php

namespace App\Helpers;

class Permissions
{
	/**
	 * Basic permissions
	 *
	 * @var array
	 */
	const BASIC = ['index', 'create', 'show', 'edit', 'destroy'];

	/**
     * Returns an array with the default permissions for the given role
     *
	 * @param  string $role
     * @return array  $list
     */
	public static function list(string $role) : array
	{
		$list = [];

		foreach (self::get($role) as $module => $permissions) {
			if ($permissions == '*') {
				foreach (self::BASIC as $value) {
					$list[] = $module . '.' . $value;
				}
			} else {
				foreach ($permissions as $permission) {
					if ($permission == '*') {
						foreach (self::BASIC as $value) {
							$list[] = $module . '.' . $value;
						}
					} else {
						$list[] = $module . '.' . $permission;
					}
				}
			}

		}

		return $list;
	}

	/**
     * Returns an array with assigned permissions in configurations
     *
	 * @param  string $role
     * @return array
     */
	public static function get(string $role)
	{
		return config('welkome.permissions.' . $role);
	}
}