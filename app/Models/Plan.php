<?php

namespace App\Models;

use App\Traits\Queryable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use Queryable, Cachable;

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

    public function users()
    {
        return $this->belongsToMany(\App\User::class);
    }

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

    /**
     * Scope a query to owner user plans.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwner($query)
    {
        return $query->whereHas('users', function ($query) {
            return $query->where('users.id', id_parent());
        });
    }

    /**
     * Check plan is expired
     *
     * @return boolean
     */
    public function isExpired(): bool
    {
        $ends = Carbon::parse($this->pivot->ends_at);

        return now()->greaterThan($ends);
    }

    /**
     * Check plan is active
     *
     * @return boolean
     */
    public function isActive(): bool
    {
        $ends = Carbon::parse($this->pivot->ends_at);

        return now()->lessThan($ends);
    }
}
