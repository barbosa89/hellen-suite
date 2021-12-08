<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public function scopeWhereHotel(Builder $query, string $hotel): Builder
    {
        return $query->where('configurable_id', id_decode($hotel))
            ->where('configurable_type', Hotel::class);
    }
}
