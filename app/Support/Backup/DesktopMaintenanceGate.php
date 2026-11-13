<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

/** Two-lock protocol: an exclusive request stops new entrants while existing work drains. */
class DesktopMaintenanceGate
{
    private readonly string $datasetPath;

    private readonly string $intentPath;

    public function __construct(string $directory)
    {
        if (! is_dir($directory)) {
            throw new RuntimeException('The desktop maintenance directory is unavailable.');
        }

        $parent = rtrim($directory, '/\\');
        $this->datasetPath = $parent . DIRECTORY_SEPARATOR . 'backup-maintenance.lock';
        $this->intentPath = $parent . DIRECTORY_SEPARATOR . 'backup-maintenance-intent.lock';
    }

    public function enter(): BackupMaintenanceLock
    {
        $intent = BackupMaintenanceLock::shared($this->intentPath);

        try {
            return BackupMaintenanceLock::shared($this->datasetPath);
        } finally {
            $intent->release();
        }
    }

    public function request(): DesktopMaintenanceRequest
    {
        return new DesktopMaintenanceRequest(BackupMaintenanceLock::exclusive($this->intentPath), $this->datasetPath);
    }

    public function isRequested(): bool
    {
        try {
            $intent = BackupMaintenanceLock::shared($this->intentPath);
        } catch (RuntimeException) {
            return true;
        }

        $intent->release();

        return false;
    }
}
