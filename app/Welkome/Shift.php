<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }
}
