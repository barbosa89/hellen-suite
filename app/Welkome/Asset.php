<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public function rooms()
    {
        return $this->belongsToMany(Welkome\Room::class);
    }
}
