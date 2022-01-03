<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $hash
 * @property mixed $value
 * @property int $configurable_id
 * @property string $configurable_type
 * @property int $configuration_id
 * @property Carbon $created_at
 */
class Setting extends Model
{
    protected $fillable = ['value'];

    protected $appends = ['hash'];

    protected $hidden = ['id'];

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = id_encode($this->attributes['id']);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(Configuration::class);
    }
}
