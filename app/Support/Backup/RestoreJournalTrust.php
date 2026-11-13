<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function is_array;
use function is_bool;
use function is_string;

/** Reconstruct paths from trusted runtime configuration; never trust journal paths directly. */
class RestoreJournalTrust
{
    public function validate(array $journal, array $trustedTargets): array
    {
        $transaction = $journal['transaction'] ?? null;
        $resources = $journal['resources'] ?? null;

        if (! is_string($transaction) || ! preg_match('/\A[a-zA-Z0-9_-]{1,80}\z/', $transaction) || ! is_array($resources) || $trustedTargets === []) {
            throw new RuntimeException('The restore journal cannot be trusted.');
        }

        if (array_diff_key($resources, $trustedTargets) !== [] || array_diff_key($trustedTargets, $resources) !== []) {
            throw new RuntimeException('The restore journal does not match the configured resources.');
        }

        $trusted = [];

        foreach ($trustedTargets as $name => $target) {
            $trusted[$name] = $this->resource($target, $transaction, $resources[$name]);
        }

        return $trusted;
    }

    private function resource(mixed $target, string $transaction, mixed $recorded): array
    {
        if (! is_string($target) || $target === '' || ! is_array($recorded) || ! is_bool($recorded['existed'] ?? null)) {
            throw new RuntimeException('The restore journal cannot be trusted.');
        }

        $parent = dirname($target);

        if (is_link($parent) || realpath($parent) !== $parent) {
            throw new RuntimeException('A restore destination is unavailable or changed.');
        }

        $prefix = $parent . DIRECTORY_SEPARATOR . '.' . basename($target) . '.restore-' . $transaction;
        $expected = [
            'target' => $target,
            'previous' => $prefix . '-previous',
            'replacement' => $prefix . '-replacement',
            'existed' => $recorded['existed'],
        ];

        if ($recorded !== $expected) {
            throw new RuntimeException('The restore journal does not match the configured resources.');
        }

        foreach (['target', 'previous', 'replacement'] as $key) {
            if (is_link($expected[$key])) {
                throw new RuntimeException('A restore resource cannot be a symbolic link.');
            }
        }

        return $expected;
    }
}
