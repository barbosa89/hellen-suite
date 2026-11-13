<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

use function is_array;
use function is_string;

/** Removes the journal and the retained copies once a dataset is known to be consistent. */
class RestoreCleanup
{
    public function __construct(private readonly Filesystem $filesystem = new Filesystem()) {}

    public function discard(string $journalPath, array $resources): void
    {
        foreach ($resources as $paths) {
            if (! is_array($paths)) {
                throw new RuntimeException('Invalid restore resource plan.');
            }

            foreach (['previous', 'replacement'] as $key) {
                $path = $paths[$key] ?? null;

                if (! is_string($path) || $path === '') {
                    throw new RuntimeException('Invalid restore resource plan.');
                }

                if (is_link($path)) {
                    throw new RuntimeException('Unable to discard a restore recovery copy.');
                }

                if (file_exists($path)) {
                    $this->filesystem->remove($path);
                }
            }
        }

        if (file_exists($journalPath) && ! @unlink($journalPath)) {
            throw new RuntimeException('Unable to discard the restore journal.');
        }
    }
}
