<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Service extends Model
{
    use LogsActivity;

    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
