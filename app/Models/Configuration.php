<?php

namespace App\Models;

use App\User;
use App\Traits\Queryable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Presenters\ConfigurationPresenter;

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
    use PresentableTrait;

    protected $fillable = [
        'name', 'enabled_at'
    ];

    protected $appends = ['hash'];

    protected $casts = [
        'enabled_at' => 'date',
    ];

    protected string $presenter = ConfigurationPresenter::class;

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = id_encode($this->attributes['id']);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function isEnabled(): bool
    {
        return !empty($this->enabled_at);
    }
}
