<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
