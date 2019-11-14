<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    /**
     * Get the owning maintainable model.
     */
    public function maintainable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
