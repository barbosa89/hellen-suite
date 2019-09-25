<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use LogsActivity;

    public function rooms()
    {
        return $this->belongsToMany(\App\Welkome\Room::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
