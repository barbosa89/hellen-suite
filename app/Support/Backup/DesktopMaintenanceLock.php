<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function is_string;

class DesktopMaintenanceLock
{
    public function shared(): BackupMaintenanceLock
    {
        return (new DesktopMaintenanceGate($this->directory()))->enter();
    }

    public function exclusive(): BackupMaintenanceLock
    {
        return BackupMaintenanceLock::exclusive($this->path());
    }

    private function path(): string
    {
        return rtrim($this->directory(), '/\\') . DIRECTORY_SEPARATOR . 'backup-maintenance.lock';
    }

    public function request(): DesktopMaintenanceRequest
    {
        return (new DesktopMaintenanceGate($this->directory()))->request();
    }

    public function isRequested(): bool
    {
        return (new DesktopMaintenanceGate($this->directory()))->isRequested();
    }

    private function directory(): string
    {
        $parent = getenv('NATIVEPHP_USER_DATA_PATH');

        if (! config('nativephp-internal.running', false) || ! is_string($parent) || $parent === '' || ! is_dir($parent)) {
            throw new RuntimeException('The desktop maintenance lock is unavailable.');
        }

        return $parent;
    }
}
