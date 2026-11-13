<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

/** Internal recovery stage; caller must close connections and prevent all desktop writes. */
class RestoreRecoveryCoordinator
{
    public function __construct(
        private readonly RestoreJournal $journal,
        private readonly RestoreJournalTrust $trust,
        private readonly RestoreTargetResolver $targets,
        private readonly RestoreRecovery $recovery,
    ) {}

    public function rollback(string $path, string $database, array $roots, BackupMaintenanceLock $lock): void
    {
        if (! $lock->isExclusive()) {
            throw new RuntimeException('Exclusive maintenance access is required.');
        }

        $journal = $this->journal->read($path);
        $targets = $this->targets->resolve($database, $roots);
        $resources = $this->trust->validate($journal, $targets);
        $this->assertNoSqliteSidecars($resources['database']);
        $this->recovery->rollback($path, $resources);
    }

    private function assertNoSqliteSidecars(array $database): void
    {
        foreach (['target', 'previous', 'replacement'] as $key) {
            foreach (['-wal', '-shm', '-journal'] as $suffix) {
                $path = $database[$key] . $suffix;

                if (file_exists($path) || is_link($path)) {
                    throw new RuntimeException('SQLite journal files must be closed before recovery.');
                }
            }
        }
    }
}
