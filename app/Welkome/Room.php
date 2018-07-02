<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Room extends Model
{
    use LogsActivity;

    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Welkome\Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
