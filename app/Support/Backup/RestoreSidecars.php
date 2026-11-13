<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

/**
 * Keeps SQLite journal files attached to the database they belong to.
 *
 * A restored database must never be paired with a stale WAL or SHM file.
 */
class RestoreSidecars
{
    /** @var list<string> */
    public const SUFFIXES = ['-wal', '-shm', '-journal'];

    public function exists(string $database): bool
    {
        foreach (self::SUFFIXES as $suffix) {
            if (file_exists($database . $suffix) || is_link($database . $suffix)) {
                return true;
            }
        }

        return false;
    }

    public function move(string $from, string $to): void
    {
        foreach (self::SUFFIXES as $suffix) {
            $source = $from . $suffix;
            $target = $to . $suffix;

            if (! file_exists($source) && ! is_link($source)) {
                continue;
            }

            if (file_exists($target) || is_link($target) || is_link($source) || ! @rename($source, $target)) {
                throw new RuntimeException('Unable to move a SQLite journal file.');
            }
        }
    }

    public function remove(string $database): void
    {
        foreach (self::SUFFIXES as $suffix) {
            $path = $database . $suffix;

            if (! file_exists($path) && ! is_link($path)) {
                continue;
            }

            if (is_link($path) || ! @unlink($path)) {
                throw new RuntimeException('Unable to remove a SQLite journal file.');
            }
        }
    }
}
