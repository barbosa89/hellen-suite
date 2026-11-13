<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use DateTimeImmutable;
use DateTimeZone;
use PDO;

use function array_key_exists;
use function count;
use function in_array;
use function is_int;
use function is_string;

class BackupManifest
{
    /**
     * @param  array<string, array{size: int, sha256: string}>  $files
     * @return array{format_version: int, created_at: string, application_version: string, migrations: list<array{migration: string, batch: int}>, files: array<string, array{size: int, sha256: string}>}
     */
    public function create(PDO $snapshot, string $version, DateTimeImmutable $createdAt, array $files): array
    {
        if ($version === '') {
            throw new RuntimeException('The backup application version is unavailable.');
        }

        if (! array_key_exists('database.sqlite', $files)) {
            throw new RuntimeException('The backup database snapshot is missing.');
        }

        $this->validateFiles($files);
        ksort($files, SORT_STRING);

        return [
            'format_version' => 1,
            'created_at' => $createdAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
            'application_version' => $version,
            'migrations' => (new BackupMigrationReader())->read($snapshot),
            'files' => $files,
        ];
    }

    private function validateFiles(array $files): void
    {
        foreach ($files as $name => $file) {
            $parts = explode('/', $name);

            if ($name !== 'database.sqlite' && (count($parts) < 2 || ! in_array($parts[0], ['hotels', 'payments'], true))) {
                throw new RuntimeException('Invalid backup manifest file.');
            }

            if (! (new BackupPathPolicy())->validComponents($parts)) {
                throw new RuntimeException('Invalid backup manifest file.');
            }

            if (! is_int($file['size'] ?? null) || $file['size'] < 0 || ! is_string($file['sha256'] ?? null) || ! preg_match('/\A[a-f0-9]{64}\z/', $file['sha256'])) {
                throw new RuntimeException('Invalid backup manifest file.');
            }
        }

    }
}
