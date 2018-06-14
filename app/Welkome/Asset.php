<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use LogsActivity;
    
    public function rooms()
    {
        return $this->belongsToMany(Welkome\Room::class);
    }
}
