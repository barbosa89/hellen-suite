<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;

use function count;
use function is_resource;

class BackupAttachmentStager
{
    /**
     * The caller must hold exclusive maintenance access and provide a private staging directory.
     *
     * @param  array{hotels: array<string, string>, payments: array<string, string>}  $attachments
     * @return array<string, array{size: int, sha256: string}>
     */
    public function stage(array $attachments, string $directory): array
    {
        if (! is_dir($directory) || is_link($directory)) {
            throw new RuntimeException('The backup staging directory is unavailable.');
        }

        $created = [];
        $manifest = [];

        try {
            foreach (['hotels', 'payments'] as $tree) {
                foreach ($attachments[$tree] as $name => $source) {
                    $target = $this->targetPath($directory, $tree, $name);
                    $manifest[$name] = $this->stageFile($source, $target, $created);
                }
            }
        } catch (Throwable $exception) {
            $cleanupFailed = false;

            foreach ($created as $path) {
                if (! @unlink($path)) {
                    $cleanupFailed = true;
                }
            }

            if ($cleanupFailed) {
                throw new RuntimeException('Backup failed and temporary attachments could not be removed.', previous: $exception);
            }

            throw $exception;
        }

        return $manifest;
    }

    private function targetPath(string $directory, string $tree, string $name): string
    {
        $parts = explode('/', $name);

        if (count($parts) < 2 || $parts[0] !== $tree || ! (new BackupPathPolicy())->validComponents($parts)) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }

        $parent = rtrim($directory, '/\\');
        $filename = array_pop($parts);

        foreach ($parts as $part) {
            $parent .= DIRECTORY_SEPARATOR . $part;

            if (is_link($parent) || (! is_dir($parent) && ! @mkdir($parent, 0700))) {
                throw new RuntimeException('Unable to prepare backup attachments.');
            }
        }

        return $parent . DIRECTORY_SEPARATOR . $filename;
    }

    /** @return array{size: int, sha256: string} */
    private function stageFile(string $source, string $target, array &$created): array
    {
        $before = @hash_file('sha256', $source);
        $input = @fopen($source, 'rb');

        if ($before === false || $input === false) {
            if (is_resource($input)) {
                fclose($input);
            }

            throw new RuntimeException('A backup attachment is unreadable.');
        }

        $output = @fopen($target, 'x+b');

        if ($output === false) {
            fclose($input);
            throw new RuntimeException('Unable to stage a backup attachment.');
        }

        $created[] = $target;
        $copied = stream_copy_to_stream($input, $output);
        $flushed = fflush($output);

        fclose($input);
        fclose($output);

        $after = @hash_file('sha256', $source);
        $staged = @hash_file('sha256', $target);

        if ($copied === false || ! $flushed || $after === false || $staged === false || ! hash_equals($before, $after) || ! hash_equals($before, $staged)) {
            throw new RuntimeException('Backup attachment verification failed.');
        }

        return ['size' => $copied, 'sha256' => $staged];
    }
}
