<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait Queryable
{
    public static function getColumnNames(array $columns = [], array $default = ['id', 'created_at', 'updated_at']) : array
    {
        return array_merge($default, $columns, (new static)->fillable);
    }

    public function scopeSelectAll(Builder $query, array $columns=['id', 'created_at', 'updated_at']): Builder
    {
        return $query->select(array_merge($columns, $this->fillable));
    }

    public function scopeId(Builder $query, int $id): Builder
    {
        return $query->where('id', $id);
    }

    public function scopeHash(Builder $query, string $id): Builder
    {
        return $query->where('id', id_decode($id));
    }

    public function scopeOwner(Builder $query): Builder
    {
        return $query->where('user_id', id_parent());
    }

    public function scopeFilter(Builder $query, array $filters): Builder
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

    public function scopeFromDate(Builder $query, string $date): Builder
    {
        $date = Carbon::parse($date);

        return $query->where('created_at', '>=', $date->startOfDay());
    }
}
