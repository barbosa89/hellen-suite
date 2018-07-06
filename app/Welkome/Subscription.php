<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
