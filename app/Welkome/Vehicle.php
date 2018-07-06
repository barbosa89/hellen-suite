<?php

namespace App\Welkome;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Vehicle extends Model
{
    use Searchable;
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

    public function type()
    {
        return $this->belongsTo(\App\Welkome\VehicleType::class, 'vehicle_type_id');
    }

    public function guest()
    {
        return $this->belongsTo(\App\Welkome\Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
