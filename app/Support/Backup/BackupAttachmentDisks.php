<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function in_array;
use function is_array;
use function is_string;

class BackupAttachmentDisks
{
    /**
     * @param  array{hotels: array<string, string>, payments: array<string, string>}  $attachments
     * @param  array{hotels: list<string>, payments: list<string>}  $roots
     * @return array<string, 'public'|'default'>
     */
    public function fromResolved(array $attachments, array $roots): array
    {
        $public = $roots['payments'][0];
        $default = $roots['hotels'][0];
        $mapping = [];

        foreach ($attachments['hotels'] as $name => $source) {
            if ($this->matches($public, $name, $source)) {
                $mapping[$name] = 'public';
            } elseif ($this->matches($default, $name, $source)) {
                $mapping[$name] = 'default';
            } else {
                throw new RuntimeException('Unable to identify the backup attachment disk.');
            }
        }

        foreach ($attachments['payments'] as $name => $source) {
            if (! $this->matches($public, $name, $source)) {
                throw new RuntimeException('Unable to identify the backup attachment disk.');
            }

            $mapping[$name] = 'public';
        }

        ksort($mapping, SORT_STRING);

        return $mapping;
    }

    public function validateManifest(array $manifest): void
    {
        $files = $manifest['files'] ?? null;
        $mapping = $manifest['attachment_disks'] ?? null;

        if (! is_array($files) || ! is_array($mapping)) {
            throw new RuntimeException('The backup attachment disk mapping is invalid.');
        }

        unset($files['database.sqlite']);

        if (array_diff_key($files, $mapping) !== [] || array_diff_key($mapping, $files) !== []) {
            throw new RuntimeException('The backup attachment disk mapping is incomplete.');
        }

        foreach ($mapping as $name => $disk) {
            if (! is_string($name) || ! in_array($disk, ['public', 'default'], true) || (str_starts_with($name, 'payments/') && $disk !== 'public')) {
                throw new RuntimeException('The backup attachment disk mapping is invalid.');
            }
        }
    }

    private function matches(string $root, string $name, string $source): bool
    {
        $resolvedSource = realpath($source);
        $expected = realpath(rtrim($root, '/\\') . DIRECTORY_SEPARATOR . $name);

        return $resolvedSource !== false && $expected !== false && $resolvedSource === $expected;
    }
}
