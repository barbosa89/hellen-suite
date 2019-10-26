<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Prop extends Model
{
    public function rooms()
    {
        return $this->belongsToMany(\App\Welkome\Room::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
