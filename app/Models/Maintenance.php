<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Maintenance extends Model
{
    use HasFactory;
    use Queryable;

    /**
     * @var array
     */
    protected $appends = ['hash'];

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    public function maintainable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWhereMaintainable(Builder $query, string $maintainableId, string $maintainableType): Builder
    {
        return $query->whereHasMorph(
            'maintainable',
            $maintainableType,
            function (Builder $query) use ($maintainableId) {
                $query->where('id', id_decode($maintainableId));
            }
        );
    }
}
