<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;
    
    public function guest()
    {
        return $this->belongsToMany(\App\Welkome\Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
