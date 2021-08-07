<?php

namespace App\Models;

use App\User;
use App\Constants\Config;
use App\Traits\Queryable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $enabled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method string|null getEnabledDate()
 * @method bool isEnabled()
 */
class Configuration extends Model
{
    use Queryable;

    protected $fillable = [
        'name', 'enabled_at'
    ];

    protected $appends = ['hash'];

    protected $casts = [
        'enabled_at' => 'date',
    ];

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = id_encode($this->attributes['id']);
    }

    public function getFullNameAttribute(): string
    {
        return Config::trans($this->attributes['name']);
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function getEnabledDate(): ?string
    {
        return $this->enabled_at
            ? $this->enabled_at->format('Y-m-d')
            : '';
    }

    public function isEnabled(): bool
    {
        return !empty($this->enabled_at);
    }
}
