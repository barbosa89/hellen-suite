<?php

declare(strict_types=1);

namespace App\Support\Backup;

class BackupPathPolicy
{
    /** @param list<string> $parts */
    public function validComponents(array $parts): bool
    {
        foreach ($parts as $part) {
            if ($part === '' || $part === '.' || $part === '..' || str_contains($part, '\\') || str_contains($part, ':') || str_contains($part, "\0")) {
                return false;
            }
        }

        return true;
    }
}
