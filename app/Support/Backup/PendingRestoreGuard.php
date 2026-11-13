<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

class PendingRestoreGuard
{
    /** The application may only continue once no restore requires recovery. */
    public function assertRecovered(string $directory): void
    {
        if ($directory === '' || ! is_dir($directory) || ! is_readable($directory)) {
            throw new RuntimeException('Unable to check for pending desktop restore recovery.');
        }

        $journal = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . 'restore.journal';
        clearstatcache(true, $journal);

        if (file_exists($journal) || is_link($journal)) {
            throw new RuntimeException('Desktop restore recovery is required before the application can start.');
        }
    }
}
