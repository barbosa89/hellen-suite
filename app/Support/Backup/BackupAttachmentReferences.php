<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use PDO;

use function count;
use function is_string;

class BackupAttachmentReferences
{
    /** @return array{hotels: list<string>, payments: list<string>} */
    public function read(PDO $snapshot): array
    {
        return [
            'hotels' => $this->readPaths($snapshot, 'SELECT DISTINCT image FROM hotels WHERE image IS NOT NULL', 'hotels'),
            'payments' => $this->readPaths($snapshot, 'SELECT DISTINCT support_path FROM payments WHERE support_path IS NOT NULL', 'payments'),
        ];
    }

    /** @return list<string> */
    private function readPaths(PDO $snapshot, string $sql, string $tree): array
    {
        $paths = [];

        foreach ($snapshot->query($sql)->fetchAll(PDO::FETCH_COLUMN) as $value) {
            $path = $this->normalizePath($value, $tree);
            $paths[$path] = $path;
        }

        $paths = array_values($paths);
        sort($paths, SORT_STRING);

        return $paths;
    }

    private function normalizePath(mixed $value, string $tree): string
    {
        if (! is_string($value)) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }

        $path = str_replace('\\', '/', $value);
        $parts = explode('/', $path);

        if (count($parts) < 2 || $parts[0] !== $tree || ! (new BackupPathPolicy())->validComponents($parts)) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }

        return $path;
    }
}
