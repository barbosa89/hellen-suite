<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;

use function is_resource;

/** Advisory lock: all processes accessing the dataset must participate. */
class BackupMaintenanceLock
{
    /** @param resource $handle */
    private function __construct(private $handle, private readonly int $mode) {}

    public function isExclusive(): bool
    {
        return is_resource($this->handle) && $this->mode === LOCK_EX;
    }

    public static function shared(string $path): self
    {
        return self::acquire($path, LOCK_SH);
    }

    public static function exclusive(string $path): self
    {
        return self::acquire($path, LOCK_EX);
    }

    public function release(): void
    {
        if (! is_resource($this->handle)) {
            return;
        }

        flock($this->handle, LOCK_UN);
        fclose($this->handle);
        $this->handle = null;
    }

    public function __destruct()
    {
        $this->release();
    }

    private static function acquire(string $path, int $mode): self
    {
        $handle = @fopen($path, 'c+b');

        if ($handle === false) {
            throw new RuntimeException('Unable to open the maintenance lock.');
        }

        if (! flock($handle, $mode | LOCK_NB)) {
            fclose($handle);

            throw new MaintenanceLockBusy('The application is busy.');
        }

        return new self($handle, $mode);
    }
}
