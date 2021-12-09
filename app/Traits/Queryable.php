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
     * @param  string $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHash(Builder $query, string $id): Builder
    {
        return $query->where('id', id_decode($id));
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
    public function scopeFilter($query, array $filters): Builder
    {
        foreach ($filters as $filter => $param) {
            $filter = clean_param(Str::camel($filter));
            $param = $this->parseParam($param);

            if($query->hasNamedScope($filter)) {
                $query->{$filter}($param);
            }
        }

        return $query;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function parseParam($value)
    {
        if (is_array($value)) {
            foreach ($value as $param) {
                $parsed[] = $this->parseParam($param);
            }
        } else {
            if (Str::contains($value, '_')) {
                $parsed = clean_param(Str::camel($value));
            } else {
                $parsed = clean_param($value);
            }
        }

        return $parsed;
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
