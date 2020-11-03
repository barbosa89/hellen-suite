<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class IdentificationType extends Model
{
    use LogsActivity;

    public function guest()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }

    public function invoice()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }
}
