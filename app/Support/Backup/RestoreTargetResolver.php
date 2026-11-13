<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function is_string;

/** Resolve from application configuration, not from a restore journal. */
class RestoreTargetResolver
{
    public function resolve(string $database, array $roots): array
    {
        $databaseParent = $this->root(dirname($database));
        $public = $this->root($roots['payments'][0] ?? null);
        $default = $this->root($roots['hotels'][0] ?? null);
        $targets = [
            'database' => $databaseParent . DIRECTORY_SEPARATOR . basename($database),
            'hotels-public' => $public . DIRECTORY_SEPARATOR . 'hotels',
        ];

        if ($default !== $public) {
            $targets['hotels-default'] = $default . DIRECTORY_SEPARATOR . 'hotels';
        }

        $targets['payments-public'] = $public . DIRECTORY_SEPARATOR . 'payments';

        foreach ($targets as $target) {
            if (is_link($target)) {
                throw new RuntimeException('A restore destination cannot be a symbolic link.');
            }
        }

        return $targets;
    }

    private function root(mixed $root): string
    {
        if (! is_string($root) || is_link($root)) {
            throw new RuntimeException('A restore directory is unavailable.');
        }

        $resolved = realpath($root);

        if ($resolved === false || ! is_dir($resolved)) {
            throw new RuntimeException('A restore directory is unavailable.');
        }

        return $resolved;
    }
}
