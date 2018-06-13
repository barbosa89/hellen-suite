<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    public function type()
    {
        return $this->belongsTo(Welkome\VehicleType::class, 'vehicle_type_id');
    }

    public function guest()
    {
        return $this->belongsTo(Welkome\Guest::class);
    }
}
