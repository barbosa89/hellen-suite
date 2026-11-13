<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;

use function is_array;
use function is_bool;
use function is_string;

/** Internal only: callers must close all connections and establish desktop-wide exclusion. */
class RestoreInstaller
{
    public function __construct(
        private readonly RestoreJournal $journal,
        private readonly RestoreRecovery $recovery,
        private readonly RestoreSidecars $sidecars = new RestoreSidecars(),
        private readonly RestoreDatabaseCheckpoint $checkpoint = new RestoreDatabaseCheckpoint(),
    ) {}

    /**
     * Installs prepared resources and retains the journal and previous dataset.
     * The verifier must check the complete installed dataset before returning true.
     */
    public function install(string $journalPath, string $transaction, array $resources, BackupMaintenanceLock $lock, callable $verify): void
    {
        if (! $lock->isExclusive()) {
            throw new RuntimeException('Exclusive maintenance access is required.');
        }

        $this->validateResources($resources);
        $this->assertReplacementHasNoSidecars($resources);
        $this->journal->create($journalPath, $transaction, $resources);

        try {
            foreach ($resources as $name => $paths) {
                $this->installResource($journalPath, $name, $paths);
            }

            if ($verify($resources) !== true) {
                throw new RuntimeException('Restore verification failed.');
            }

            foreach ($resources as $name => $paths) {
                $this->journal->append($journalPath, 'verify_completed', $name);
            }
        } catch (Throwable $exception) {
            try {
                $this->recovery->rollback($journalPath, $resources);
            } catch (Throwable $recoveryException) {
                throw new RestoreFailure('Restore failed and requires recovery before the application can start.', $exception, $recoveryException);
            }

            throw $exception;
        }
    }

    /** The replacement must be a self-contained database without journal files. */
    private function assertReplacementHasNoSidecars(array $resources): void
    {
        $path = $resources['database']['replacement'] ?? null;

        if (! is_string($path)) {
            throw new RuntimeException('The restore database is missing.');
        }

        if ($this->sidecars->exists($path)) {
            throw new RuntimeException('The prepared restore database carries SQLite journal files.');
        }
    }

    private function validateResources(array $resources): void
    {
        if ($resources === []) {
            throw new RuntimeException('The restore resource plan is empty.');
        }

        foreach ($resources as $paths) {
            if (! is_array($paths) || ! is_bool($paths['existed'] ?? null)) {
                throw new RuntimeException('Invalid restore resource plan.');
            }

            foreach (['target', 'previous', 'replacement'] as $key) {
                if (! is_string($paths[$key] ?? null) || $paths[$key] === '' || is_link($paths[$key])) {
                    throw new RuntimeException('Invalid restore resource plan.');
                }
            }

            $this->validateResourceState($paths);
        }
    }

    private function validateResourceState(array $paths): void
    {
        clearstatcache();

        if (file_exists($paths['target']) !== $paths['existed'] || file_exists($paths['previous']) || ! file_exists($paths['replacement'])) {
            throw new RuntimeException('A restore resource changed before installation.');
        }

        $file = is_file($paths['replacement']);
        $directory = is_dir($paths['replacement']);

        if ((! $file && ! $directory) || ($paths['existed'] && (($file && ! is_file($paths['target'])) || ($directory && ! is_dir($paths['target']))))) {
            throw new RuntimeException('A restore resource has an invalid type.');
        }
    }

    private function installResource(string $journalPath, string $name, array $paths): void
    {
        $this->validateResourceState($paths);
        $this->journal->append($journalPath, 'preserve_started', $name);

        if ($paths['existed']) {
            $this->settleDatabase($paths['target']);
            $this->move($paths['target'], $paths['previous']);
            $this->sidecars->move($paths['target'], $paths['previous']);
        }

        $this->journal->append($journalPath, 'preserve_completed', $name);
        $this->journal->append($journalPath, 'install_started', $name);
        $this->move($paths['replacement'], $paths['target']);
        $this->journal->append($journalPath, 'install_completed', $name);
    }

    /** Fold committed transactions into the database file before it is moved. */
    private function settleDatabase(string $path): void
    {
        if ($this->isSqliteDatabase($path)) {
            $this->checkpoint->prepare($path);

            return;
        }

        if ($this->sidecars->exists($path)) {
            throw new RuntimeException('SQLite journal files must be settled before restoration.');
        }
    }

    private function isSqliteDatabase(string $path): bool
    {
        if (! is_file($path) || is_link($path)) {
            return false;
        }

        $header = @file_get_contents($path, false, null, 0, 16);

        return is_string($header) && str_starts_with($header, 'SQLite format 3');
    }

    private function move(string $from, string $to): void
    {
        clearstatcache(true, $to);

        if (is_link($from) || is_link($to) || file_exists($to) || ! @rename($from, $to)) {
            throw new RuntimeException('Unable to move a restore resource.');
        }

        (new RestoreDirectorySync())->sync(dirname($from));
    }
}
