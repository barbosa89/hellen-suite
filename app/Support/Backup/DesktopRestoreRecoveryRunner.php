<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;

/**
 * Runs the startup recovery of an interrupted restore.
 *
 * The process must hold no other dataset lease while the replacement is reverted.
 */
class DesktopRestoreRecoveryRunner
{
    public function __construct(
        private readonly DesktopMaintenanceLock $maintenance,
        private readonly DesktopProcessLease $processLease,
        private readonly DesktopRestoreRecovery $recovery,
    ) {}

    public function run(string $directory, string $database, array $roots): bool
    {
        $path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . 'restore.journal';

        if (! file_exists($path)) {
            return false;
        }

        $request = $this->maintenance->request();
        $hadProcessLease = $this->processLease->isHeld();
        $failure = null;

        try {
            if ($hadProcessLease) {
                $this->processLease->release();
            }

            (new DesktopMaintenanceWaiter())->acquire($request, 'The restore maintenance timeout is invalid.', 'The application is busy; restore recovery could not obtain exclusive access.');
            $this->recovery->recover($directory, $database, $roots);
        } catch (Throwable $exception) {
            $failure = $exception;
        }

        try {
            if ($hadProcessLease) {
                $this->processLease->hold($request->resumeShared());
            } else {
                $request->release();
            }
        } catch (Throwable $cleanupException) {
            $request->release();

            if ($failure !== null) {
                throw new RestoreFailure('Unable to resume the desktop process after restore recovery.', $failure, $cleanupException);
            }

            throw new RuntimeException('Unable to resume the desktop process after restore recovery.', previous: $cleanupException);
        }

        if ($failure !== null) {
            throw $failure;
        }

        return true;
    }
}
