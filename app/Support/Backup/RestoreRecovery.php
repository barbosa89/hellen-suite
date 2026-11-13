<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function is_bool;

/** Internal rollback only. The caller must hold exclusive access and validate trusted paths. */
class RestoreRecovery
{
    public function __construct(
        private readonly RestoreJournal $journal,
        private readonly RestoreSidecars $sidecars = new RestoreSidecars(),
    ) {}

    /**
     * Restore the previous resources without deleting the journal or replacement copies.
     *
     * @param  array<string, array{target: string, previous: string, replacement: string, existed: bool}>  $trustedResources
     */
    public function rollback(string $journalPath, array $trustedResources): void
    {
        $journal = $this->journal->read($journalPath);

        if ($journal['resources'] !== $trustedResources) {
            throw new RuntimeException('The restore journal does not match the trusted resource plan.');
        }

        $states = [];
        $order = [];

        foreach ($journal['events'] as $event) {
            $states[$event['resource']] = $event['event'];
            $order[] = $event['resource'];
        }

        $plans = [];

        foreach ($trustedResources as $resource => $paths) {
            if (! is_bool($paths['existed'] ?? null)) {
                throw new RuntimeException('The restore journal is missing resource existence information.');
            }

            $status = $this->status($paths);
            $plans[$resource] = [$status, $this->moves($paths, $states[$resource] ?? null, $status)];
        }

        foreach (array_unique(array_reverse($order)) as $resource) {
            $state = $states[$resource];

            if ($state === 'rollback_completed') {
                continue;
            }

            [$initial, $moves] = $plans[$resource];

            if ($this->status($trustedResources[$resource]) !== $initial) {
                throw new RuntimeException('A restore resource changed during recovery.');
            }

            if ($state !== 'rollback_started') {
                $this->journal->append($journalPath, 'rollback_started', $resource);
            }

            foreach ($moves as [$from, $to]) {
                if (is_link($from) || is_link($to) || file_exists($to) || ! @rename($from, $to)) {
                    throw new RuntimeException('Unable to recover a restore resource.');
                }

                $this->sidecars->move($from, $to);
                (new RestoreDirectorySync())->sync(dirname($from));
            }

            $this->journal->append($journalPath, 'rollback_completed', $resource);
        }
    }

    /** @return array{target: bool, previous: bool, replacement: bool} */
    private function status(array $paths): array
    {
        $status = [];

        foreach (['target', 'previous', 'replacement'] as $key) {
            $path = $paths[$key];
            clearstatcache(true, $path);

            if (is_link($path) || (file_exists($path) && ! is_file($path) && ! is_dir($path))) {
                throw new RuntimeException('Invalid restore resource.');
            }

            $status[$key] = file_exists($path);
        }

        return $status;
    }

    /** @return list<array{string, string}> */
    private function moves(array $paths, null|string $state, array $status): array
    {
        $old = $paths['existed'];
        $untouched = ['target' => $old, 'previous' => false, 'replacement' => true];
        $preserved = ['target' => false, 'previous' => $old, 'replacement' => true];
        $installed = ['target' => true, 'previous' => $old, 'replacement' => false];

        $valid = match ($state) {
            null, 'rollback_completed' => $status === $untouched,
            'preserve_started' => $status === $untouched || $status === $preserved,
            'preserve_completed' => $status === $preserved,
            'install_started' => $status === $preserved || $status === $installed,
            'install_completed', 'verify_completed' => $status === $installed,
            'rollback_started' => $status === $untouched || $status === $preserved || $status === $installed,
            default => false,
        };

        if (! $valid) {
            throw new RuntimeException('The restore resource state is ambiguous.');
        }

        if ($status === $untouched) {
            return [];
        }

        $moves = [];

        if ($status === $installed) {
            $moves[] = [$paths['target'], $paths['replacement']];
        }

        if ($old) {
            $moves[] = [$paths['previous'], $paths['target']];
        }

        return $moves;
    }
}
