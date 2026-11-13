<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use JsonException;
use PDO;

use function is_array;

class BackupDatabaseCompatibility
{
    public function __construct(private readonly Encrypter $encrypter) {}

    public function verify(PDO $snapshot, PDO $current, array $manifest, string $version): void
    {
        if (($manifest['format_version'] ?? null) !== 1 || ($manifest['application_version'] ?? null) !== $version || $version === '') {
            throw new RuntimeException('The backup application version is incompatible.');
        }

        if ($snapshot->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite' || $current->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            throw new RuntimeException('SQLite databases are required.');
        }

        if ($snapshot->query('PRAGMA integrity_check')->fetchAll(PDO::FETCH_COLUMN) !== ['ok']) {
            throw new RuntimeException('The backup database failed its integrity check.');
        }

        $reader = new BackupMigrationReader();
        $snapshotMigrations = $reader->read($snapshot);

        if (($manifest['migrations'] ?? null) !== $snapshotMigrations || $reader->read($current) !== $snapshotMigrations) {
            throw new RuntimeException('The backup database schema is incompatible.');
        }

        $this->verifyCredentials($snapshot);
    }

    private function verifyCredentials(PDO $snapshot): void
    {
        $credentials = $snapshot->query('SELECT credentials FROM hotel_compliance_profiles WHERE credentials IS NOT NULL');

        while (($value = $credentials->fetchColumn()) !== false) {
            try {
                $decoded = json_decode($this->encrypter->decryptString($value), true, 512, JSON_THROW_ON_ERROR);
            } catch (DecryptException|JsonException $exception) {
                throw new RuntimeException('The backup contains credentials that cannot be read on this installation.', previous: $exception);
            }

            if (! is_array($decoded)) {
                throw new RuntimeException('The backup contains invalid encrypted credentials.');
            }
        }
    }
}
