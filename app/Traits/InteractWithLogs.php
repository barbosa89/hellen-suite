<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;

trait InteractWithLogs
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
