<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use DateTimeImmutable;
use PDO;
use Throwable;

/** Internal workflow; all application writers must first join the maintenance protocol. */
class ExportBackup
{
    public function __construct(
        private readonly PrepareBackup $preparer,
        private readonly BackupArchivePublisher $publisher,
    ) {}

    public function export(PDO $database, BackupMaintenanceLock $lock, string $workspaceParent, string $destination, string $version, DateTimeImmutable $createdAt): void
    {
        if (! $lock->isExclusive()) {
            throw new RuntimeException('Exclusive maintenance access is required.');
        }

        $workspace = BackupWorkspace::create($workspaceParent);
        try {
            $archive = $this->preparer->prepare($database, $lock, $workspace->path(), $version, $createdAt);
            $this->publisher->publish($archive, $destination);
        } catch (Throwable $exception) {
            try {
                $workspace->remove();
            } catch (Throwable $cleanupException) {
                throw new BackupFailure('Backup failed and its private workspace could not be removed.', $exception, $cleanupException);
            }

            throw $exception;
        }

        try {
            $workspace->remove();
        } catch (Throwable $exception) {
            throw new RuntimeException('The backup was published, but its private workspace could not be removed.', previous: $exception);
        }
    }
}
