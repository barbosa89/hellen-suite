<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;
    
    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Welkome\Room::class);
    }
}
