<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

class RestoreDirectorySync
{
    public function sync(string $directory): void
    {
        if (! is_dir($directory) || is_link($directory) || ! function_exists('fsync')) {
            throw new RuntimeException('Unable to synchronize a restore directory.');
        }

        $handle = @fopen($directory, 'rb');

        if ($handle === false) {
            throw new RuntimeException('Unable to synchronize a restore directory.');
        }

        try {
            if (! @fsync($handle)) {
                throw new RuntimeException('Unable to synchronize a restore directory.');
            }
        } finally {
            fclose($handle);
        }
    }
}
