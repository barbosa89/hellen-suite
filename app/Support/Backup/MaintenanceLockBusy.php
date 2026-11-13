<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

class MaintenanceLockBusy extends RuntimeException {}
