<?php

namespace App\Models;

use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleType extends Model
{
    use LogsActivity;
    use InteractWithLogs;

    public function vehicle()
    {
        return $this->hasMany(\App\Models\Vehicle::class, 'vehicle_type_id');
    }
}
