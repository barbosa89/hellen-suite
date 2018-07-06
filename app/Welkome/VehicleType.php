<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleType extends Model
{
    use LogsActivity;

    public function vehicle()
    {
        return $this->hasMany(\App\Welkome\Vehicle::class, 'vehicle_type_id');
    }
}
