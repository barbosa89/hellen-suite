<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class IdentificationType extends Model
{
    use LogsActivity;
    
    public function guest()
    {
        return $this->hasMany(Welkome\Guest::class, 'identification_type_id');
    }
}
