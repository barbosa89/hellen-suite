<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Voucher extends Model
{
    use Queryable;
    use LogsActivity;

    public const PREFIX = 'V';
    public const SALE = 'sale';
    public const ENTRY = 'entry';
    public const LOSS = 'loss';
    public const DISCARD = 'discard';
    public const LODGING = 'lodging';
    public const DINING = 'dining';
    public const OPEN = 'open';
    public const CLOSED = 'closed';
    public const PAID = 'paid';
    public const PENDING = 'pending';
    public const RESERVATION = 'reservation';

    public const TYPES = [
        self::SALE,
        self::ENTRY,
        self::LOSS,
        self::DISCARD,
        self::LODGING,
        self::DINING,
    ];

    public const STATUS = [
        self::OPEN,
        self::CLOSED,
        self::PAID,
        self::PENDING,
        self::RESERVATION,
    ];

    public const SCOPE_FILTERS = [
        'from_date',
        'type',
        'status',
    ];

    protected $appends = ['hash'];

    protected $hidden = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Room::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function props(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Prop::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function guests(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Guest::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Hotel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function additionals(): HasMany
    {
        return $this->hasMany(\App\Models\Additional::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Shift::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checks(): HasMany
    {
        return $this->hasMany(\App\Models\Check::class);
    }

    /**
     * @return string
     */
    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLodging(Builder $query): Builder
    {
        return $query->where('type', 'lodging')
            ->when(auth()->user()->hasRole('receptionist'), function ($query)
            {
                $query->where('hotel_id', auth()->user()->headquarters()->first()->id);
            });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('open', true)
            ->where('status', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('open', false)
            ->where('status', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', true)
            ->where('status', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', false)
            ->where('status', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReservation(Builder $query): Builder
    {
        return $query->where('reservation', true)
            ->where('status', true);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, string $filter): Builder
    {
        if ($query->hasNamedScope($filter)) {
            $query->{$filter}();
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType(Builder $query, string $type): Builder
    {
    /**
     * Scope a query to guests by text search.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string $text
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $text)
    {
        return $query->whereLike(['number', 'origin', 'destination', 'made_by'], $text);
    }

    /**
     * @return bool
     */
    public function canClosePayments(): bool
    {
        if ($this->payments->isNotEmpty()) {
            $payments = (float) $this->payments->sum('value');

            return (float) $this->value == $payments  and $this->payment_status == false;
        }

        return false;
    }
}
