<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Throwable;

use function is_resource;

class SelectedBackupStager
{
    /** The caller must provide an application-owned private directory. */
    public function stage(string $source, string $directory, int $maxBytes): string
    {
        $this->validateInput($source, $directory, $maxBytes);

        $input = @fopen($source, 'rb');

        if ($input === false) {
            throw new RuntimeException('The selected backup is unreadable.');
        }

        $target = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . 'selected-backup.zip';
        $output = @fopen($target, 'x+b');

        if ($output === false) {
            fclose($input);
            throw new RuntimeException('Unable to stage the selected backup.');
        }

        try {
            $this->copyAndVerify($input, $output, $target, $maxBytes);

            return $target;
        } catch (Throwable $exception) {
            if (is_resource($output)) {
                fclose($output);
                $output = null;
            }

            if (! @unlink($target)) {
                throw new RuntimeException('Backup staging failed and its temporary file could not be removed.', previous: $exception);
            }

            throw $exception;
        } finally {
            fclose($input);
        }
    }

    private function validateInput(string $source, string $directory, int $maxBytes): void
    {
        if ($maxBytes < 1 || $maxBytes === PHP_INT_MAX || ! is_file($source) || ! is_dir($directory) || is_link($directory)) {
            throw new RuntimeException('The selected backup is unavailable.');
        }
    }

    /** @param resource $input @param resource|null $output */
    private function copyAndVerify($input, &$output, string $target, int $maxBytes): void
    {
        $initial = fstat($input);

        if ($initial === false || $initial['size'] > $maxBytes) {
            throw new RuntimeException('The selected backup exceeds the size limit.');
        }

        $size = stream_copy_to_stream($input, $output, $maxBytes + 1);
        $flushed = fflush($output);
        fclose($output);
        $output = null;

        if ($size === false || $size > $maxBytes || ! $flushed || fseek($input, 0) !== 0) {
            throw new RuntimeException('Unable to verify the selected backup.');
        }

        $this->verifyCopy($input, $target, $size, $initial['size'], $maxBytes);
    }

    /** @param resource $input */
    private function verifyCopy($input, string $target, int $size, int $initialSize, int $maxBytes): void
    {
        $hash = hash_init('sha256');
        $read = hash_update_stream($hash, $input, $maxBytes + 1);
        $sourceHash = hash_final($hash);
        $stagedHash = @hash_file('sha256', $target);
        $final = fstat($input);

        if ($read !== $size || $initialSize !== $size || $final === false || $final['size'] !== $size || $stagedHash === false || ! hash_equals($sourceHash, $stagedHash)) {
            throw new RuntimeException('The selected backup changed during staging.');
        }
    }
}
