<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function count;

class BackupAttachmentLocator
{
    /**
     * Resolve references against local disk roots while the caller holds the maintenance lock.
     *
     * @param  array{hotels: list<string>, payments: list<string>}  $references
     * @param  array{hotels: list<string>, payments: list<string>}  $roots
     * @return array{hotels: array<string, string>, payments: array<string, string>}
     */
    public function locate(array $references, array $roots): array
    {
        $result = ['hotels' => [], 'payments' => []];

        foreach (['hotels', 'payments'] as $tree) {
            foreach ($references[$tree] as $reference) {
                $result[$tree][$reference] = $this->resolveReference($reference, $tree, $roots[$tree]);
            }
        }

        return $result;
    }

    private function resolveReference(string $reference, string $tree, array $roots): string
    {
        $parts = explode('/', $reference);

        if (count($parts) < 2 || $parts[0] !== $tree || ! (new BackupPathPolicy())->validComponents($parts)) {
            throw new RuntimeException('Invalid backup attachment reference.');
        }

        $matches = [];

        foreach ($roots as $root) {
            $canonicalRoot = realpath($root);

            if ($canonicalRoot === false || ! is_dir($canonicalRoot)) {
                throw new RuntimeException('A backup attachment disk is unavailable.');
            }

            $path = $this->resolvePath($root, $parts);

            if (! file_exists($path)) {
                continue;
            }

            if (! is_file($path) || ! is_readable($path)) {
                throw new RuntimeException('A backup attachment is unreadable.');
            }

            $matches[$canonicalRoot] = $path;
        }

        if (count($matches) !== 1) {
            throw new RuntimeException('A backup attachment is missing or ambiguous.');
        }

        return reset($matches);
    }

    private function resolvePath(string $root, array $parts): string
    {
        $path = rtrim($root, '/\\');

        foreach ($parts as $part) {
            $path .= DIRECTORY_SEPARATOR . $part;

            if (is_link($path)) {
                throw new RuntimeException('Backup attachments cannot contain links.');
            }
        }

        return $path;
    }
}
