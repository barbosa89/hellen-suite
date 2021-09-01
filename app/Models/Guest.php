<?php

namespace App\Models;

use App\Traits\Hashable;
use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guest extends Model
{
    use Hashable;
    use Queryable;
    use LogsActivity;

    public const IS_STAYING = 'is_staying';
    public const IS_NOT_STAYING = 'is_not_staying';

    public const SCOPE_STATUS = [
        self::IS_STAYING,
        self::IS_NOT_STAYING,
    ];

    protected $fillable = [
        'dni',
        'name',
        'last_name',
        'gender',
        'birthdate',
        'responsible_adult',
        'identification_type_id',
        'user_id',
        'status',
        'created_at',
    ];

    protected $appends = [
        'hash',
        'user_hash',
        'full_name',
        'country_hash',
        'identification_type_hash',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'country_id',
        'identification_type_id',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }

    public function getUserHashAttribute(): string
    {
        return $this->attributes['user_hash'] = id_encode($this->attributes['user_id']);
    }

    public function getCountryHashAttribute(): string
    {
        return $this->attributes['country_hash'] = id_encode($this->attributes['country_id']);
    }

    public function getIdentificationTypeHashAttribute(): string
    {
        return $this->attributes['identification_type_hash'] = id_encode($this->attributes['identification_type_id']);
    }

    public function children(): HasMany
    {
        return $this->hasMany(\App\Models\Guest::class, 'responsible_adult');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Guest::class, 'responsible_adult');
    }

    public function vouchers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Voucher::class);
    }

    public function identificationType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\IdentificationType::class, 'identification_type_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Room::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(\App\Models\Check::class);
    }

    public function scopeIsStaying(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeIsNotStaying(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        if ($query->hasNamedScope($status)) {
            $query->{$status}();
        }

        return $query;
    }

    public function scopeSearch(Builder $query, string $text): Builder
    {
        return $query->whereLike(['name', 'last_name', 'dni'], $text);
    }
}
