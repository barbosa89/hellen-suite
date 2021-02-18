<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

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

    /**
     * Scope a query by id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId($query, int $id)
    {
        return $query->where('id', $id);
    }

    /**
     * Scope a query by owner.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwner($query)
    {
        return $query->where('user_id', id_parent());
    }

    /**
     * Scope a query by filters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $filter => $value) {
            $filter = clean_param(Str::camel($filter));

            if (Str::contains($value, '_')) {
                $value = clean_param(Str::camel($value));
            } else {
                $value = clean_param($value);
            }

            if($query->hasNamedScope($filter)) {
                $query->{$filter}($value);
            }
        }

        return $query;
    }

    /**
     * Scope a query by creation date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromDate($query, string $date)
    {
        $date = Carbon::parse($date);

        return $query->where('created_at', '>=', $date->startOfDay());
    }
}
