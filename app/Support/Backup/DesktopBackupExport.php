<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;

use function is_string;

/** Internal only: do not expose until every desktop writer joins maintenance. */
class DesktopBackupExport
{
    public function __construct(
        private readonly DesktopMaintenanceLock $maintenance,
        private readonly ActiveSqliteDatabase $database,
        private readonly ExportBackup $exporter,
        private readonly DesktopProcessLease $processLease,
    ) {}

    public function export(string $destination): void
    {
        $request = $this->maintenance->request();
        $hadProcessLease = $this->processLease->isHeld();
        $failure = null;

        try {
            if ($hadProcessLease) {
                $this->processLease->release();
            }

            (new DesktopMaintenanceWaiter())->acquire($request, 'The backup maintenance timeout is invalid.', 'The application is busy; backup could not obtain exclusive access.');
            $parent = getenv('NATIVEPHP_USER_DATA_PATH');
            $version = config('nativephp.version');

            if (! is_string($parent) || $parent === '' || ! is_string($version) || $version === '') {
                throw new RuntimeException('The desktop backup configuration is unavailable.');
            }

            $this->database->path();
            $this->exporter->export(
                $this->database->connection(),
                $request->lease(),
                $parent,
                $destination,
                $version,
                new DateTimeImmutable('now', new DateTimeZone('UTC')),
            );
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
                throw new BackupFailure('Unable to resume the desktop process after backup.', $failure, $cleanupException);
            }

            throw new RuntimeException('Unable to resume the desktop process after backup.', previous: $cleanupException);
        }

        if ($failure !== null) {
            throw $failure;
        }
    }
}
