<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait Queryable
{
    /**
     * @return array
     */
    public static function getColumnNames(array $columns = [], array $default = ['id', 'created_at', 'updated_at']) : array
    {
        return array_merge($default, $columns, (new static)->fillable);
    }

    /**
     * @return array
     */
    public function getTableColumns(): array
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllColumns(Builder $query, bool $dotted = false): Builder
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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeId(Builder $query, int $id): Builder
    {
        return $query->where('id', $id);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwner(Builder $query): Builder
    {
        return $query->where('user_id', id_parent());
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            if (is_array($value)) {
                foreach ($value as $parameter) {
                    $query = $this->applyFilter($query, $filter, $parameter);
                }
            } else {
                $query = $this->applyFilter($query, $filter, $value);

            }
        }

        return $query;
    }

    /**
     * @param string $filter
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyFilter(Builder $query, string $filter, $value): Builder
    {
        $filter = clean_param(Str::camel($filter));

        if (Str::contains($value, '_')) {
            $value = clean_param(Str::camel($value));
        } else {
            $value = clean_param($value);
        }

        if($query->hasNamedScope($filter)) {
            $query->{$filter}($value);
        }

        return $query;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromDate(Builder $query, string $date): Builder
    {
        $date = Carbon::parse($date);

        return $query->where('created_at', '>=', $date->startOfDay());
    }
}
