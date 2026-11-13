<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;
use ZipArchive;

class BackupArchiveExtractor
{
    public function __construct(private readonly BackupArchiveInspector $inspector) {}

    /** The archive and staging directory must be private and owned by the caller. */
    public function extract(string $archive, string $directory, int $maxFiles, int $maxBytes): array
    {
        if (! is_dir($directory) || is_link($directory)) {
            throw new RuntimeException('The restore staging directory is unavailable.');
        }

        $manifest = $this->inspector->inspect($archive, $maxFiles, $maxBytes);
        $zip = new ZipArchive();

        if ($zip->open($archive, ZipArchive::RDONLY) !== true) {
            throw new RuntimeException('Unable to open the backup archive.');
        }

        $created = [];

        try {
            foreach ($manifest['files'] as $name => $metadata) {
                $target = $this->targetPath($directory, $name);
                $this->extractFile($zip, $name, $target, $metadata, $created);
            }

            return $manifest;
        } catch (Throwable $exception) {
            if (! $this->removeCreatedFiles($created)) {
                throw new RuntimeException('Restore staging failed and temporary files could not be removed.', previous: $exception);
            }

            throw $exception;
        } finally {
            $zip->close();
        }
    }

    private function targetPath(string $directory, string $name): string
    {
        $parts = explode('/', $name);
        $filename = array_pop($parts);
        $parent = rtrim($directory, '/\\');

        foreach ($parts as $part) {
            $parent .= DIRECTORY_SEPARATOR . $part;

            if (is_link($parent) || (! is_dir($parent) && ! @mkdir($parent, 0700))) {
                throw new RuntimeException('Unable to prepare restore staging.');
            }
        }

        return $parent . DIRECTORY_SEPARATOR . $filename;
    }

    private function extractFile(ZipArchive $zip, string $name, string $target, array $metadata, array &$created): void
    {
        $input = $zip->getStream($name);

        if ($input === false) {
            throw new RuntimeException('Unable to read a backup entry.');
        }

        $output = @fopen($target, 'x+b');

        if ($output === false) {
            fclose($input);
            throw new RuntimeException('Unable to stage a backup entry.');
        }

        $created[] = $target;
        $size = stream_copy_to_stream($input, $output, $metadata['size'] + 1);
        $flushed = fflush($output);
        fclose($input);
        fclose($output);
        $hash = @hash_file('sha256', $target);

        if ($size !== $metadata['size'] || ! $flushed || $hash === false || ! hash_equals($metadata['sha256'], $hash)) {
            throw new RuntimeException('Backup entry verification failed.');
        }
    }

    private function removeCreatedFiles(array $created): bool
    {
        $success = true;

        foreach ($created as $path) {
            if (! @unlink($path)) {
                $success = false;
            }
        }

        return $success;
    }
}
