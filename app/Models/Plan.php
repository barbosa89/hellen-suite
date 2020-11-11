<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Support\Str;
use App\Services\ExchangeRate;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

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
    protected $fillable = ['price', 'months', 'type', 'status'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
    ];

    public function users()
    {
        return $this->belongsToMany(\App\User::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(\App\Models\Invoice::class);
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
     * Scope a query to plans not related to the authenticated user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotRelatedToUser($query)
    {
        return $query->whereDoesntHave('users', function ($query) {
            return $query->where('users.id', auth()->id());
        });
    }

    /**
     * Scope a query to non free plans.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonFree($query)
    {
        return $query->where('type', '!=', self::FREE);
    }

    /**
     * Scope a query to the free plan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFree($query)
    {
        return $query->where('type', self::FREE);
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

    /**
     * Return the plan type in lower case string
     *
     * @return string
     */
    public function getType(): string
    {
        return Str::lower($this->type);
    }


    /**
     * Get the plan's price in USD currency.
     *
     * @return float
     */
    public function getDollarPrice(): float
    {
        $dollarPrice = (float) ExchangeRate::convert(1, 'USD', 'COP');

        return $this->price / $dollarPrice;
    }
}
