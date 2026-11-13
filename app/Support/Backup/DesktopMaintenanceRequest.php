<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

class DesktopMaintenanceRequest
{
    private null|BackupMaintenanceLock $dataset = null;

    private bool $released = false;

    public function __construct(private readonly BackupMaintenanceLock $intent, private readonly string $datasetPath) {}

    public function tryAcquire(): bool
    {
        if ($this->released) {
            throw new RuntimeException('The maintenance request has been released.');
        }

        if ($this->dataset !== null) {
            return true;
        }

        try {
            $this->dataset = BackupMaintenanceLock::exclusive($this->datasetPath);
        } catch (MaintenanceLockBusy) {
            return false;
        }

        return true;
    }

    public function lease(): BackupMaintenanceLock
    {
        if ($this->dataset === null || ! $this->dataset->isExclusive()) {
            throw new RuntimeException('Exclusive maintenance access has not been acquired.');
        }

        return $this->dataset;
    }

    public function resumeShared(): BackupMaintenanceLock
    {
        if ($this->released) {
            throw new RuntimeException('The maintenance request has been released.');
        }

        $this->dataset?->release();
        $this->dataset = null;
        $shared = BackupMaintenanceLock::shared($this->datasetPath);
        $this->intent->release();
        $this->released = true;

        return $shared;
    }

    public function release(): void
    {
        if ($this->released) {
            return;
        }

        $this->dataset?->release();
        $this->dataset = null;
        $this->intent->release();
        $this->released = true;
    }

    public function __destruct()
    {
        $this->release();
    }
}
