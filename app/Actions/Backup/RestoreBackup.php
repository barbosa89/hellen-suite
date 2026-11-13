<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use App\Support\Backup\DesktopRestore;

class RestoreBackup
{
    public function __construct(
        private readonly DesktopRestore $restore,
        private readonly BackupEnvironment $environment,
    ) {}

    public function execute(string $path): void
    {
        $this->restore->restore($path, $this->environment->version(), $this->environment->userDataPath(), $this->environment->journalPath());
    }
}
