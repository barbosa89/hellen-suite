<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    public function vehicle()
    {
        return $this->hasMany(Welkome\Vehicle::class, 'vehicle_type_id');
    }
}
