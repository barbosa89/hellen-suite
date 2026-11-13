<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function count;
use function is_array;
use function is_string;

/**
 * Startup recovery for an interrupted restore.
 *
 * The journal is untrusted input: every path is rebuilt from the running
 * configuration before any file is moved.
 */
class DesktopRestoreRecovery
{
    public function __construct(
        private readonly RestoreJournal $journal,
        private readonly RestoreJournalTrust $trust,
        private readonly RestoreTargetResolver $targets,
        private readonly RestoreRecovery $recovery,
        private readonly RestoreCleanup $cleanup = new RestoreCleanup(),
    ) {}

    public function recover(string $directory, string $database, array $roots): void
    {
        $path = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . 'restore.journal';

        if (! file_exists($path)) {
            return;
        }

        $journal = $this->journal->read($path);
        $resources = $this->trust->validate($journal, $this->targets->resolve($database, $roots));

        $this->assertNoSqliteSidecars($resources);

        if ($this->isFullyVerified($journal)) {
            $this->cleanup->discard($path, $resources);
            (new PendingRestoreGuard())->assertRecovered($directory);

            return;
        }

        $this->recovery->rollback($path, $resources);
        $this->cleanup->discard($path, $resources);
        (new PendingRestoreGuard())->assertRecovered($directory);
    }

    /** A restore is complete only when every resource was verified. */
    private function isFullyVerified(array $journal): bool
    {
        $verified = [];

        foreach ($journal['events'] as $event) {
            $verified[$event['resource']] = $event['event'];
        }

        if (count($verified) !== count($journal['resources'])) {
            return false;
        }

        foreach ($verified as $event) {
            if ($event !== 'verify_completed') {
                return false;
            }
        }

        return true;
    }

    private function assertNoSqliteSidecars(array $resources): void
    {
        $database = $resources['database'] ?? null;

        if (! is_array($database) || ! is_string($database['target'] ?? null)) {
            throw new RuntimeException('The restore journal does not describe a database resource.');
        }

        foreach (['target', 'previous', 'replacement'] as $key) {
            foreach (['-wal', '-shm', '-journal'] as $suffix) {
                $sidecar = $database[$key] . $suffix;

                if (file_exists($sidecar) || is_link($sidecar)) {
                    throw new RuntimeException('SQLite journal files must be closed before restore recovery.');
                }
            }
        }
    }
}
