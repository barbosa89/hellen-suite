<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Vehicle extends Model
{
    use LogsActivity;

    public function type()
    {
        return $this->belongsTo(\App\Welkome\VehicleType::class, 'vehicle_type_id');
    }

    public function guests()
    {
        return $this->belongsToMany(\App\Welkome\Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
