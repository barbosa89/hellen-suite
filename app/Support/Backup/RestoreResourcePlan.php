<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function array_slice;
use function is_string;

/** Builds a plan only; the caller must hold the desktop-wide exclusive lock. */
class RestoreResourcePlan
{
    public function __construct(
        private readonly BackupAttachmentDisks $attachmentDisks,
        private readonly RestoreTargetResolver $targets = new RestoreTargetResolver(),
    ) {}

    public function build(string $database, array $roots, array $manifest, string $transaction, BackupMaintenanceLock $lock): array
    {
        if (! $lock->isExclusive() || ! preg_match('/\A[a-zA-Z0-9_-]{1,80}\z/', $transaction)) {
            throw new RuntimeException('Exclusive access and a valid restore transaction are required.');
        }

        $this->attachmentDisks->validateManifest($manifest);

        if (! isset($manifest['files']['database.sqlite']) || is_link($database) || ! is_file($database)) {
            throw new RuntimeException('The restore database is unavailable.');
        }

        $resources = [];
        $files = [];

        foreach ($this->targets->resolve($database, $roots) as $name => $target) {
            $resources[$name] = $this->resource($target, $transaction, $name !== 'database');

            if ($name !== 'database') {
                $files[$name] = [];
            }
        }

        $this->rejectOverlappingTargets($resources);

        foreach ($manifest['attachment_disks'] as $name => $disk) {
            $this->validateName($name);
            $key = str_starts_with($name, 'payments/')
                ? 'payments-public'
                : ($disk === 'default' && isset($resources['hotels-default']) ? 'hotels-default' : 'hotels-public');
            $files[$key][] = $name;
        }

        foreach ($files as &$paths) {
            sort($paths, SORT_STRING);
        }

        return ['resources' => $resources, 'files' => $files];
    }

    private function resource(string $target, string $transaction, bool $directory): array
    {
        clearstatcache(true, $target);

        if (is_link($target) || (file_exists($target) && ($directory ? ! is_dir($target) : ! is_file($target)))) {
            throw new RuntimeException('A restore destination is invalid.');
        }

        $prefix = dirname($target) . DIRECTORY_SEPARATOR . '.' . basename($target) . '.restore-' . $transaction;
        $previous = $prefix . '-previous';
        $replacement = $prefix . '-replacement';

        if (file_exists($previous) || is_link($previous) || file_exists($replacement) || is_link($replacement)) {
            throw new RuntimeException('A restore recovery path already exists.');
        }

        return ['target' => $target, 'previous' => $previous, 'replacement' => $replacement, 'existed' => file_exists($target)];
    }

    private function validateName(mixed $name): void
    {
        if (! is_string($name) || (! str_starts_with($name, 'hotels/') && ! str_starts_with($name, 'payments/'))) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }

        if (! (new BackupPathPolicy())->validComponents(explode('/', $name))) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }
    }

    private function rejectOverlappingTargets(array $resources): void
    {
        $targets = array_column($resources, 'target');

        foreach ($targets as $index => $target) {
            foreach (array_slice($targets, $index + 1) as $other) {
                if ($target === $other || str_starts_with($target, $other . DIRECTORY_SEPARATOR) || str_starts_with($other, $target . DIRECTORY_SEPARATOR)) {
                    throw new RuntimeException('Restore destinations must not overlap.');
                }
            }
        }
    }
}
