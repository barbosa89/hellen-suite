<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use DateTimeImmutable;
use DateTimeZone;
use JsonException;
use ZipArchive;

use function count;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use function strlen;

class BackupArchiveInspector
{
    public function inspect(string $path, int $maxFiles, int $maxBytes): array
    {
        $zip = new ZipArchive();

        if ($maxFiles < 1 || $maxBytes < 1 || $zip->open($path, ZipArchive::RDONLY) !== true) {
            throw new RuntimeException('Unable to inspect the backup archive.');
        }

        try {
            $entries = $this->readEntries($zip, $maxFiles, $maxBytes);
            $manifest = $this->readManifest($zip, $entries);
            $this->verifyFiles($zip, $entries, $manifest['files']);

            return $manifest;
        } finally {
            $zip->close();
        }
    }

    /** @return array<string, int> */
    private function readEntries(ZipArchive $zip, int $maxFiles, int $maxBytes): array
    {
        if ($zip->numFiles < 2 || $zip->numFiles > $maxFiles) {
            throw new RuntimeException('Backup archive limits exceeded.');
        }

        $entries = [];
        $total = 0;

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $stat = $zip->statIndex($index);

            if ($stat === false || ! is_string($stat['name']) || isset($entries[$stat['name']]) || ! is_int($stat['size']) || $stat['size'] < 0 || $stat['size'] > $maxBytes - $total) {
                throw new RuntimeException('Invalid backup archive entry.');
            }

            $this->validateEntry($zip, $index, $stat['name']);
            $total += $stat['size'];
            $entries[$stat['name']] = $stat['size'];
        }

        return $entries;
    }

    private function validateEntry(ZipArchive $zip, int $index, string $name): void
    {
        $parts = explode('/', $name);

        if ($name !== 'manifest.json' && $name !== 'database.sqlite' && (count($parts) < 2 || ! in_array($parts[0], ['hotels', 'payments'], true))) {
            throw new RuntimeException('Invalid backup archive entry.');
        }

        if (! (new BackupPathPolicy())->validComponents($parts) || ! $zip->getExternalAttributesIndex($index, $system, $attributes)) {
            throw new RuntimeException('Invalid backup archive entry.');
        }

        $type = ($attributes >> 16) & 0170000;

        if (($system === ZipArchive::OPSYS_UNIX && $type !== 0 && $type !== 0100000) || ($attributes & 0x10) !== 0) {
            throw new RuntimeException('Invalid backup archive entry.');
        }
    }

    private function readManifest(ZipArchive $zip, array $entries): array
    {
        if (! isset($entries['manifest.json'], $entries['database.sqlite']) || $entries['manifest.json'] > 1048576) {
            throw new RuntimeException('Invalid backup manifest.');
        }

        $raw = $zip->getFromName('manifest.json');

        if ($raw === false || strlen($raw) !== $entries['manifest.json']) {
            throw new RuntimeException('Invalid backup manifest.');
        }

        try {
            $manifest = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Invalid backup manifest.', previous: $exception);
        }

        if (! is_array($manifest) || ($manifest['format_version'] ?? null) !== 1 || ! is_array($manifest['files'] ?? null) || ! isset($manifest['files']['database.sqlite']) || count($manifest['files']) !== count($entries) - 1) {
            throw new RuntimeException('Invalid backup manifest.');
        }

        $this->validateManifestMetadata($manifest);

        return $manifest;
    }

    private function validateManifestMetadata(array $manifest): void
    {
        $createdAt = $manifest['created_at'] ?? null;
        $version = $manifest['application_version'] ?? null;
        $migrations = $manifest['migrations'] ?? null;
        $date = is_string($createdAt)
            ? DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z', $createdAt, new DateTimeZone('UTC'))
            : false;

        if ($date === false || $date->format('Y-m-d\TH:i:s\Z') !== $createdAt || ! is_string($version) || $version === '' || ! is_array($migrations) || ! array_is_list($migrations)) {
            throw new RuntimeException('Invalid backup manifest.');
        }

        $names = [];

        foreach ($migrations as $migration) {
            if (! is_array($migration) || ! is_string($migration['migration'] ?? null) || $migration['migration'] === '' || ! is_int($migration['batch'] ?? null) || $migration['batch'] < 1 || isset($names[$migration['migration']])) {
                throw new RuntimeException('Invalid backup manifest.');
            }

            $names[$migration['migration']] = true;
        }
    }

    private function verifyFiles(ZipArchive $zip, array $entries, array $files): void
    {
        foreach ($files as $name => $metadata) {
            if (! is_string($name) || $name === 'manifest.json' || ! isset($entries[$name]) || ! is_array($metadata) || ! is_int($metadata['size'] ?? null) || $metadata['size'] !== $entries[$name] || ! is_string($metadata['sha256'] ?? null) || ! preg_match('/\A[a-f0-9]{64}\z/', $metadata['sha256'])) {
                throw new RuntimeException('Invalid backup manifest.');
            }

            $stream = $zip->getStream($name);

            if ($stream === false) {
                throw new RuntimeException('Unable to read a backup entry.');
            }

            $hash = hash_init('sha256');
            $size = hash_update_stream($hash, $stream, $entries[$name] + 1);
            fclose($stream);

            if ($size !== $metadata['size'] || ! hash_equals($metadata['sha256'], hash_final($hash))) {
                throw new RuntimeException('Backup entry verification failed.');
            }
        }
    }
}
