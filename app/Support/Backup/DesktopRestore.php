<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function count;
use function is_array;
use function is_int;

/**
 * Desktop restore workflow.
 *
 * The archive is always treated as untrusted input and staged privately before
 * the live dataset is touched.
 */
class DesktopRestore
{
    public function __construct(
        private readonly DesktopMaintenanceLock $maintenance,
        private readonly DesktopProcessLease $processLease,
        private readonly ActiveSqliteDatabase $database,
        private readonly BackupDiskRoots $roots,
        private readonly PrepareRestore $preparer,
        private readonly RestoreResourcePlan $planner,
        private readonly RestoreReplacementStager $stager,
        private readonly RestoreInstaller $installer,
        private readonly RestoreInstalledVerifier $verifier,
        private readonly Filesystem $filesystem = new Filesystem(),
    ) {}

    /**
     * Inspect a selected archive without touching the live dataset.
     *
     * @return array{created_at: string, application_version: string, files: int, size: int}
     */
    public function preview(string $archive, string $version, string $workspaceParent): array
    {
        $workspace = BackupWorkspace::create($workspaceParent);

        try {
            $manifest = $this->preparer->prepare(
                $archive,
                $workspace->path(),
                $this->database->connection(),
                $version,
                $this->maxFiles(),
                $this->maxBytes(),
            );

            return [
                'created_at' => $manifest['created_at'],
                'application_version' => $manifest['application_version'],
                'files' => count($manifest['files']),
                'size' => $this->totalSize($manifest),
            ];
        } finally {
            $workspace->remove();
        }
    }

    /**
     * Replace the live dataset with the contents of the archive.
     */
    public function restore(string $archive, string $version, string $workspaceParent, string $journalPath): void
    {
        $request = $this->maintenance->request();
        $hadProcessLease = $this->processLease->isHeld();
        $workspace = null;
        $failure = null;

        try {
            if ($hadProcessLease) {
                $this->processLease->release();
            }

            (new DesktopMaintenanceWaiter())->acquire($request, 'The restore maintenance timeout is invalid.', 'The application is busy; the restore could not obtain exclusive access.');
            $workspace = BackupWorkspace::create($workspaceParent);
            $manifest = $this->preparer->prepare(
                $archive,
                $workspace->path(),
                $this->database->connection(),
                $version,
                $this->maxFiles(),
                $this->maxBytes(),
            );

            $transaction = $this->transaction();
            $plan = $this->planner->build(
                $this->database->path(),
                $this->roots->resolve(),
                $manifest,
                $transaction,
                $request->lease(),
            );

            $this->stager->stage($workspace->path() . DIRECTORY_SEPARATOR . 'dataset', $plan, $manifest, $request->lease());
            $this->database->disconnect();

            $this->installer->install(
                $journalPath,
                $transaction,
                $plan['resources'],
                $request->lease(),
                fn (array $resources): bool => $this->verifier->verify($resources, $plan['files'], $manifest),
            );

            $this->database->reconnect();
        } catch (Throwable $exception) {
            $failure = $exception;

            try {
                $this->database->reconnect();
            } catch (Throwable $reconnectException) {
                $failure = new RestoreFailure('Restore failed and the database could not be reconnected.', $exception, $reconnectException);
            }
        }

        $this->cleanup($workspace, $request, $hadProcessLease, $failure);
    }

    private function cleanup(null|BackupWorkspace $workspace, DesktopMaintenanceRequest $request, bool $hadProcessLease, null|Throwable $failure): void
    {
        $workspaceFailure = null;

        try {
            $workspace?->remove();
        } catch (Throwable $cleanupException) {
            $workspaceFailure = $cleanupException;
        }

        try {
            if ($hadProcessLease) {
                $this->processLease->hold($request->resumeShared());
            } else {
                $request->release();
            }
        } catch (Throwable $cleanupException) {
            $request->release();

            if ($workspaceFailure !== null) {
                $failure = $failure === null
                    ? $workspaceFailure
                    : new RestoreFailure('The restore workspace could not be removed.', $failure, $workspaceFailure);
            }

            if ($failure !== null) {
                throw new RestoreFailure('Unable to resume the desktop process after the restore.', $failure, $cleanupException);
            }

            throw new RuntimeException('Unable to resume the desktop process after the restore.', previous: $cleanupException);
        }

        if ($workspaceFailure !== null) {
            if ($failure !== null) {
                throw new RestoreFailure('The restore workspace could not be removed.', $failure, $workspaceFailure);
            }

            throw new RuntimeException('The restore workspace could not be removed.', previous: $workspaceFailure);
        }

        if ($failure !== null) {
            throw $failure;
        }
    }

    private function transaction(): string
    {
        return 'restore' . bin2hex(random_bytes(6));
    }

    private function maxFiles(): int
    {
        $value = config('backup.restore_max_files', 20000);

        if (! is_int($value) || $value < 1) {
            throw new RuntimeException('The backup restore file limit is invalid.');
        }

        return $value;
    }

    private function maxBytes(): int
    {
        $value = config('backup.restore_max_bytes', 2147483648);

        if (! is_int($value) || $value < 1) {
            throw new RuntimeException('The backup restore size limit is invalid.');
        }

        return $value;
    }

    private function totalSize(array $manifest): int
    {
        $size = 0;

        foreach ($manifest['files'] as $metadata) {
            if (! is_array($metadata) || ! is_int($metadata['size'] ?? null)) {
                throw new RuntimeException('The backup manifest is invalid.');
            }

            $size += $metadata['size'];
        }

        return $size;
    }
}
