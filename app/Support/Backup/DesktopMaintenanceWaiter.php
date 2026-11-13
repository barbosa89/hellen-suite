<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function is_int;

class DesktopMaintenanceWaiter
{
    public function acquire(DesktopMaintenanceRequest $request, string $invalidTimeoutMessage, string $busyMessage): void
    {
        $timeout = config('backup.maintenance_timeout_ms', 10000);

        if (! is_int($timeout) || $timeout < 0 || $timeout > 60000) {
            throw new RuntimeException($invalidTimeoutMessage);
        }

        $deadline = hrtime(true) + $timeout * 1000000;

        do {
            if ($request->tryAcquire()) {
                return;
            }

            if (hrtime(true) >= $deadline) {
                break;
            }

            usleep(50000);
        } while (true);

        throw new RuntimeException($busyMessage);
    }
}
