<?php

namespace App\Models;

use App\Traits\Hashable;
use App\Constants\IdentificationTypes;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $type
 * @property string $hash
 */
class IdentificationType extends Model
{
    use Hashable;
    use LogsActivity;

    protected $appends = ['hash', 'description'];

    protected $hidden = ['id'];

    public function getDescriptionAttribute(): string
    {
        return $this->attributes['description'] = IdentificationTypes::trans($this->attributes['type']);
    }

    public function guest()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }

    public function invoice()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }
}
