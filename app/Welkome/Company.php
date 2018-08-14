<?php

namespace App\Welkome;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;
    use Searchable;

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
        return $this->hasMany(\App\Welkome\Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
