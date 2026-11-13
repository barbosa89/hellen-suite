<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use App\Support\Backup\DesktopFileDialog;
use App\Support\Backup\DesktopRestore;

class SelectBackup
{
    public function __construct(
        private readonly DesktopFileDialog $dialog,
        private readonly DesktopRestore $restore,
        private readonly BackupEnvironment $environment,
    ) {}

    public function selectFile(): null|string
    {
        return $this->dialog->open(
            trans('backup.dialogs.restore.title'),
            trans('backup.dialogs.restore.button'),
            $this->environment->defaultDirectory(),
        );
    }

    /** @return array{path: string, created_at: string, application_version: string, files: int, size: int} */
    public function execute(string $path): array
    {
        $preview = $this->restore->preview($path, $this->environment->version(), $this->environment->userDataPath());

        return [
            'path' => $path,
            'created_at' => $preview['created_at'],
            'application_version' => $preview['application_version'],
            'files' => $preview['files'],
            'size' => $preview['size'],
        ];
    }
}
