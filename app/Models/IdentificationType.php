<?php

namespace App\Models;

use App\Traits\Hashable;
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

    protected $appends = ['hash'];

    protected $hidden = ['id'];

    public function guest()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }

    public function invoice()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }
}
