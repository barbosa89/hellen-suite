<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Illuminate\Database\DatabaseManager;
use PDO;

use function is_string;

class ActiveSqliteDatabase
{
    public function __construct(private readonly DatabaseManager $database) {}

    public function connection(): PDO
    {
        $connection = $this->database->connection();

        if ($connection->getDriverName() !== 'sqlite') {
            throw new RuntimeException('Backups require an active SQLite database.');
        }

        return $connection->getPdo();
    }

    public function path(): string
    {
        $databases = $this->connection()->query('PRAGMA database_list')->fetchAll(PDO::FETCH_ASSOC);

        foreach ($databases as $database) {
            if ($database['name'] === 'main' && is_string($database['file']) && $database['file'] !== '' && is_file($database['file'])) {
                return $database['file'];
            }
        }

        throw new RuntimeException('The active SQLite database must be stored in a file.');
    }

    public function disconnect(): void
    {
        $this->database->disconnect();
    }

    public function reconnect(): void
    {
        $this->database->purge();
        $this->database->reconnect();
    }

    /**
     * The configured desktop database file, without opening a connection.
     *
     * Used before the database is touched so an interrupted restore can be recovered.
     */
    public function configuredPath(): string
    {
        $path = config('app.debug')
            ? database_path('nativephp.sqlite')
            : config('nativephp-internal.database_path');

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The desktop database path is unavailable.');
        }

        return $path;
    }
}
