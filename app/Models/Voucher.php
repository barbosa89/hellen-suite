<?php

namespace App\Models;

use App\Traits\Queryable;
use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    use Queryable;
    use HasFactory;
    use LogsActivity;
    use InteractWithLogs;

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
        'search',
    ];

    protected $appends = ['hash'];

    protected $hidden = ['id'];

    protected $casts = [
        self::RESERVATION => 'boolean',
    ];

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
        return $this->belongsTo(\App\Models\User::class);
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

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    public function scopeLodging(Builder $query): Builder
    {
        return $query->where('type', 'lodging')
            ->when(auth()->user()->hasRole('receptionist'), function ($query)
            {
                $query->where('hotel_id', auth()->user()->headquarters()->first()->id);
            });
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('open', true)
            ->where('status', true);
    }

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
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter) {
            if ($query->hasNamedScope($filter)) {
                $query->{$filter}();
            }
        }

        return $query;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $types
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType(Builder $query, array $types): Builder
    {
        return $query->whereIn('type', $types);
    }

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
