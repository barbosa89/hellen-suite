<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use LogsActivity;

    public function invoice()
    {
        return $this->belongsTo(Welkome\Invoice::class);
    }
}
