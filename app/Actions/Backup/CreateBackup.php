<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use App\Support\Backup\DesktopBackupExport;
use App\Support\Backup\DesktopFileDialog;
use Illuminate\Support\Facades\File;

class CreateBackup
{
    public function __construct(
        private readonly DesktopBackupExport $export,
        private readonly DesktopFileDialog $dialog,
        private readonly BackupEnvironment $environment,
    ) {}

    public function selectDestination(): null|string
    {
        return $this->dialog->save(
            trans('backup.dialogs.export.title'),
            trans('backup.dialogs.export.button'),
            $this->environment->defaultDirectory(),
        );
    }

    public function requiresConfirmation(string $destination, bool $overwrite): bool
    {
        return File::exists($destination) && ! $overwrite;
    }

    public function execute(string $destination): void
    {
        $this->export->export($destination);
    }
}
