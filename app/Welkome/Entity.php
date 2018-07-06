<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Entity extends Model
{
    use LogsActivity;
    
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
