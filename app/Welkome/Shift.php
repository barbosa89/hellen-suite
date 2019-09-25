<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Shift extends Model
{
    use LogsActivity;

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(\App\Welkome\Invoice::class);
    }
}
