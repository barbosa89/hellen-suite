<?php

namespace App\Models;

use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleType extends Model
{
    use HasFactory;
    use InteractWithLogs;
    use LogsActivity;

    public function vehicle()
    {
        return $this->hasMany(\App\Models\Vehicle::class, 'vehicle_type_id');
    }
}
