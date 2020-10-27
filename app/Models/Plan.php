<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use Queryable;

    public const FREE = 'FREE';

    public const BASIC = 'BASIC';

    public const PREMIUM = 'PREMIUM';

    public const SPONSOR = 'SPONSOR';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['price', 'months', 'status'];

    /**
     * Scope a query to only active plans.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
