<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use PDO;
use PDOException;

use function is_string;

/**
 * Settles a SQLite database before it is moved.
 *
 * Checkpointing folds committed transactions into the database file, so the
 * database can be moved on its own. A restored database is never paired with a
 * journal file that belongs to a different database.
 */
class RestoreDatabaseCheckpoint
{
    public function prepare(string $path): void
    {
        if (! is_string($path) || $path === '' || ! file_exists($path) || is_link($path) || ! is_file($path)) {
            throw new RuntimeException('The restore database is unavailable.');
        }

        try {
            $database = new PDO('sqlite:' . $path, options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $database->exec('PRAGMA busy_timeout = 5000');
            $database->exec('PRAGMA wal_checkpoint(TRUNCATE)');
            $database->exec('PRAGMA optimize');
            $database = null;
        } catch (PDOException $exception) {
            throw new RuntimeException('Unable to settle the restore database.', previous: $exception);
        }

        $this->removeSettledSidecars($path);
    }

    private function removeSettledSidecars(string $path): void
    {
        foreach (['-wal', '-shm'] as $suffix) {
            $sidecar = $path . $suffix;

            if (! file_exists($sidecar) && ! is_link($sidecar)) {
                continue;
            }

            if (is_link($sidecar) || ! @unlink($sidecar)) {
                throw new RuntimeException('Unable to remove a settled SQLite journal file.');
            }
        }
    }
}
