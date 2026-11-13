<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;
use ZipArchive;

use function count;
use function in_array;

class BackupArchiveWriter
{
    /**
     * Create an archive in a caller-owned private directory, never at the final destination.
     *
     * @param  array{format_version: int, created_at: string, application_version: string, migrations: array, files: array<string, array{size: int, sha256: string}>}  $manifest
     */
    public function create(string $stagingDirectory, string $target, array $manifest): void
    {
        $zip = new ZipArchive();

        if ($zip->open($target, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
            throw new RuntimeException('Unable to create the backup archive.');
        }

        $open = true;

        try {
            $this->addFiles($zip, $stagingDirectory, $manifest['files']);

            $encoded = json_encode($manifest, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

            if (! $zip->addFromString('manifest.json', $encoded)) {
                throw new RuntimeException('Unable to finish the backup archive.');
            }

            $closed = $zip->close();
            $open = false;

            if (! $closed) {
                throw new RuntimeException('Unable to finish the backup archive.');
            }

            if ($zip->open($target, ZipArchive::RDONLY) !== true) {
                throw new RuntimeException('Unable to verify the backup archive.');
            }

            $open = true;

            $this->verifyArchive($zip, $manifest['files'], $encoded);

            $zip->close();
            $open = false;
        } catch (Throwable $exception) {
            if ($open) {
                $zip->close();
            }

            if (file_exists($target) && ! @unlink($target)) {
                throw new RuntimeException('Backup failed and the temporary archive could not be removed.', previous: $exception);
            }

            throw $exception;
        }
    }

    private function addFiles(ZipArchive $zip, string $directory, array $files): void
    {
        foreach ($files as $name => $metadata) {
            $path = $this->sourcePath($directory, $name);

            if (! is_file($path) || ! $zip->addFile($path, $name)) {
                throw new RuntimeException('Unable to add a backup file.');
            }
        }
    }

    private function sourcePath(string $directory, string $name): string
    {
        $parts = explode('/', $name);

        if ($name !== 'database.sqlite' && (count($parts) < 2 || ! in_array($parts[0], ['hotels', 'payments'], true))) {
            throw new RuntimeException('Invalid backup archive entry.');
        }

        if (! (new BackupPathPolicy())->validComponents($parts)) {
            throw new RuntimeException('Invalid backup archive entry.');
        }

        $path = rtrim($directory, '/\\');

        foreach ($parts as $part) {
            $path .= DIRECTORY_SEPARATOR . $part;

            if (is_link($path)) {
                throw new RuntimeException('Invalid backup archive entry.');
            }
        }

        return $path;
    }

    private function verifyArchive(ZipArchive $zip, array $files, string $encoded): void
    {
        if ($zip->numFiles !== count($files) + 1 || $zip->getFromName('manifest.json') !== $encoded) {
            throw new RuntimeException('Backup archive verification failed.');
        }

        foreach ($files as $name => $metadata) {
            $stream = $zip->getStream($name);

            if ($stream === false) {
                throw new RuntimeException('Backup archive verification failed.');
            }

            $hash = hash_init('sha256');
            $size = hash_update_stream($hash, $stream);

            fclose($stream);

            if ($size !== $metadata['size'] || ! hash_equals($metadata['sha256'], hash_final($hash))) {
                throw new RuntimeException('Backup archive verification failed.');
            }
        }
    }
}
