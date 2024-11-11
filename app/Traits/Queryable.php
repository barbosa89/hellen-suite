<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait Queryable
{
    public static function getColumnNames(array $columns = [], array $default = ['id', 'created_at', 'updated_at']): array
    {
        return array_merge($default, $columns, (new static)->fillable);
    }

    public function getTableColumns(): array
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function scopeAllColumns(Builder $query, bool $dotted = false): Builder
    {
        if ($dotted) {
            $columns = [];

            foreach ($this->getTableColumns() as $column) {
                $columns[] = $this->getTable().'.'.$column;
            }

            return $query->select($columns);
        }

        return $query->select($this->getTableColumns());
    }

    public function scopeId(Builder $query, int $id): Builder
    {
        return $query->where('id', $id);
    }

    public function scopeWhereOwner(Builder $query): Builder
    {
        return $query->where('user_id', id_parent());
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $filter => $param) {
            $filter = clean_param(Str::camel($filter));
            $param = $this->parseParam($param);

            if ($query->hasNamedScope($filter)) {
                $query->{$filter}($param);
            }
        }

        return $query;
    }

    /**
     * @return mixed
     */
    private function parseParam(mixed $value)
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

    public function scopeFromDate(Builder $query, string $date): Builder
    {
        $date = Carbon::parse($date);

        return $query->where('created_at', '>=', $date->startOfDay());
    }
}
