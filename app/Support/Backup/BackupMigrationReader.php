<?php

declare(strict_types=1);

namespace App\Support\Backup;

use PDO;

class BackupMigrationReader
{
    /** @return list<array{migration: string, batch: int}> */
    public function read(PDO $database): array
    {
        $rows = $database->query('SELECT migration, batch FROM migrations ORDER BY migration')->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn (array $row): array => ['migration' => $row['migration'], 'batch' => (int) $row['batch']], $rows);
    }
}
