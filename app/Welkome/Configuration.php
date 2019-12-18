<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    public function user()
    {
        return $this->belongsToMany(\App\User::class);
    }
}