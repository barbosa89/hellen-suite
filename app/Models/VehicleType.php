<?php

namespace App\Models;

use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleType extends Model
{
    use HasFactory;
    use LogsActivity;
    use InteractWithLogs;

    public function vehicle()
    {
        return $this->hasMany(\App\Models\Vehicle::class, 'vehicle_type_id');
    }
}
