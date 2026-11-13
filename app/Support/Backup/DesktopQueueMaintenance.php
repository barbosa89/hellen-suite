<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

/** Partial queue guard; other worker activity still needs coordination. */
class DesktopQueueMaintenance
{
    private null|BackupMaintenanceLock $lock = null;

    public function __construct(
        private readonly DesktopMaintenanceLock $maintenance,
        private readonly DesktopProcessLease $processLease,
    ) {}

    public function begin(): null|bool
    {
        if (! config('nativephp-internal.running', false) || $this->lock !== null) {
            return null;
        }

        try {
            $this->stopIfDraining();
            $this->lock = $this->maintenance->shared();
        } catch (RuntimeException) {
            $this->stopIfDraining();

            return false;
        }

        return null;
    }

    private function stopIfDraining(): void
    {
        if ($this->processLease->isHeld() && $this->maintenance->isRequested()) {
            throw new RuntimeException('The desktop worker is stopping for maintenance.');
        }
    }

    public function finish(): void
    {
        $this->lock?->release();
        $this->lock = null;
    }

    public function beforePop(): void
    {
        if ($this->begin() === false) {
            throw new RuntimeException('The desktop application is undergoing maintenance.');
        }
    }
}
