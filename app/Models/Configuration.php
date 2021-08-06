<?php

namespace App\Models;

use App\Constants\Config;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $enabled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method string|null getEnabledDate()
 */
class Configuration extends Model
{
    protected $fillable = [
        'name', 'enabled_at'
    ];

    protected $casts = [
        'enabled_at' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return Config::trans($this->attributes['name']);
    }

    public function user()
    {
        return $this->belongsToMany(\App\User::class);
    }

    public function getEnabledDate(): ?string
    {
        return $this->enabled_at
            ? $this->enabled_at->format('Y-m-d')
            : '';
    }
}
