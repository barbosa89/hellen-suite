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

    /**
     * Get the columns name to query
     *
     * @return array
     */
    public function getTableColumns(): array
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    /**
     * Scope a query to select columns.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllColumns($query, bool $dotted = false)
    {
        if ($dotted) {
            $columns = [];

            foreach ($this->getTableColumns() as $column) {
                $columns[] = $this->getTable() . '.' . $column;
            }

            return $query->select($columns);
        }

        return $query->select($this->getTableColumns());
    }
}
