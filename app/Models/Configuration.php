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
 * @property string $module
 * @property Carbon $enabled_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method string|null getEnabledDate()
 * @method bool isEnabled()
 * @method void toggle()
 */
class Configuration extends Model
{
    use Queryable;
    use PresentableTrait;

    protected $fillable = [
        'name', 'module', 'enabled_at'
    ];

    protected $appends = ['hash'];

    protected $casts = [
        'enabled_at' => 'datetime:Y-m-d',
    ];

    protected $dates = ['enabled_at'];

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

    public function toggle(): void
    {
        if ($this->isEnabled()) {
            $this->enabled_at = null;
        } else {
            $this->enabled_at = now();
        }
    }
}
