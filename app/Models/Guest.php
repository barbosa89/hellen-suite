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

class Guest extends Model
{
    use Queryable;
    use HasFactory;
    use LogsActivity;
    use InteractWithLogs;

    public const SCOPE_FILTERS = [
        'from_date',
        'status',
        'query_by',
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

    protected $appends = ['hash', 'full_name'];

    protected $hidden = ['id'];

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = id_encode($this->attributes['id']);
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
        return $this->belongsTo(\App\Models\User::class);
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

    public function scopeQueryBy(Builder $query, string $text): Builder
    {
        return $query->whereLike(['name', 'last_name', 'dni'], $text);
    }
}
