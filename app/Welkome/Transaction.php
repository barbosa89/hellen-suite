<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * Get the owning transactionable model.
     */
    public function transactionable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
