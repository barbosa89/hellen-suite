<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }
    
    public function invoices()
    {
        return $this->belongsToMany(\App\Welkome\Invoice::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(\App\Welkome\Room::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
