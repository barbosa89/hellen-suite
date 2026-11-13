<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

/** Tracks the dataset lease held by the current native PHP process. */
class DesktopProcessLease
{
    private null|BackupMaintenanceLock $lock = null;

    public function hold(BackupMaintenanceLock $lock): void
    {
        if ($this->lock !== null) {
            throw new RuntimeException('A desktop process lease is already held.');
        }

        $this->lock = $lock;
    }

    public function release(): void
    {
        $this->lock?->release();
        $this->lock = null;
    }

    public function isHeld(): bool
    {
        return $this->lock !== null;
    }
}
