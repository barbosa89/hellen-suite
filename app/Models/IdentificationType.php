<?php

namespace App\Models;

use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IdentificationType extends Model
{
    use HasFactory;
    use LogsActivity;
    use InteractWithLogs;

    public function guest()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }

    public function invoice()
    {
        return $this->hasMany(\App\Models\Guest::class, 'identification_type_id');
    }
}
