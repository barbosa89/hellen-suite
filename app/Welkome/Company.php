<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;
    
    public function guest()
    {
        return $this->belongsToMany(Welkome\Guest::class);
    }
}
