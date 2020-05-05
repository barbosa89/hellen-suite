<?php

declare(strict_types=1);

namespace App\Traits;

trait Queryable {

    /**
     * Get the columns name to query
     *
     * @return array
     */
    public static function getColumnNames(array $columns = [], array $default = ['id', 'created_at', 'updated_at']) : array
    {
        return array_merge($default, $columns, (new static)->fillable);
    }
}