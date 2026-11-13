<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use PDO;
use Throwable;

/** Creates a database snapshot inside a caller-owned private staging directory. */
class SqliteSnapshot
{
    public function create(PDO $connection, string $destination): void
    {
        if ($connection->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            throw new RuntimeException('A SQLite connection is required.');
        }

        $file = @fopen($destination, 'x');

        if ($file === false) {
            throw new RuntimeException('Unable to create the SQLite snapshot.');
        }

        fclose($file);

        try {
            $connection->exec('VACUUM main INTO ' . $connection->quote($destination));

            $snapshot = new PDO('sqlite:' . $destination, options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $results = $snapshot->query('PRAGMA integrity_check')->fetchAll(PDO::FETCH_COLUMN);
            $snapshot = null;

            if ($results !== ['ok']) {
                throw new RuntimeException('SQLite snapshot integrity verification failed.');
            }
        } catch (Throwable $exception) {
            clearstatcache(true, $destination);

            if ((file_exists($destination) || is_link($destination)) && ! @unlink($destination)) {
                throw new RuntimeException('Unable to remove the failed SQLite snapshot.', previous: $exception);
            }

            throw $exception;
        }
    }
}
