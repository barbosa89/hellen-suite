<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Maintenance Timeout
    |--------------------------------------------------------------------------
    |
    | How long a desktop backup or restore waits for other application work to
    | drain before it gives up. The value is expressed in milliseconds and
    | must stay within a range that keeps the desktop application responsive.
    |
    */

    'maintenance_timeout_ms' => (int) env('BACKUP_MAINTENANCE_TIMEOUT_MS', 10000),

    /*
    |--------------------------------------------------------------------------
    | Restore Limits
    |--------------------------------------------------------------------------
    |
    | Limits applied to a backup archive before it is inspected. They protect
    | the application from oversized or excessive archives selected by the user.
    |
    */

    'restore_max_files' => (int) env('BACKUP_RESTORE_MAX_FILES', 20000),

    'restore_max_bytes' => (int) env('BACKUP_RESTORE_MAX_BYTES', 2147483648),

];
