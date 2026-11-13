<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

/** The parent directory must be application-owned and access-controlled. */
class BackupWorkspace
{
    private bool $removed = false;

    private function __construct(private readonly string $directory) {}

    public static function create(string $parent): self
    {
        $resolved = realpath($parent);

        if ($resolved === false || ! is_dir($resolved) || is_link($parent)) {
            throw new RuntimeException('The backup workspace parent is unavailable.');
        }

        $directory = $resolved . DIRECTORY_SEPARATOR . 'backup-' . bin2hex(random_bytes(16));

        if (! @mkdir($directory, 0700)) {
            throw new RuntimeException('Unable to create the backup workspace.');
        }

        return new self($directory);
    }

    public function path(): string
    {
        if ($this->removed) {
            throw new RuntimeException('The backup workspace has already been removed.');
        }

        return $this->directory;
    }

    public function remove(): void
    {
        if ($this->removed) {
            return;
        }

        if (is_link($this->directory)) {
            throw new RuntimeException('Unable to remove the backup workspace.');
        }

        try {
            (new Filesystem())->remove($this->directory);
        } catch (Throwable $exception) {
            throw new RuntimeException('Unable to remove the backup workspace.', previous: $exception);
        }

        $this->removed = true;
    }
}
