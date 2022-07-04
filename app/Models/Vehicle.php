<?php

namespace App\Models;

use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;
    use LogsActivity;
    use InteractWithLogs;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['hash'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

    public function type()
    {
        return $this->belongsTo(\App\Models\VehicleType::class, 'vehicle_type_id');
    }

    public function guests()
    {
        return $this->belongsToMany(\App\Models\Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Hashing Vehicle ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }
}
