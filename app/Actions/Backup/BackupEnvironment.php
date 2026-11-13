<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use App\Exceptions\RuntimeException;

use function is_string;

class BackupEnvironment
{
    public function version(): string
    {
        $version = config('nativephp.version');

        if (! is_string($version) || $version === '') {
            throw new RuntimeException('The desktop application version is unavailable.');
        }

        return $version;
    }

    public function userDataPath(): string
    {
        $path = getenv('NATIVEPHP_USER_DATA_PATH');

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('The desktop user data path is unavailable.');
        }

        return $path;
    }

    public function defaultDirectory(): string
    {
        $path = getenv('NATIVEPHP_DOCUMENTS_PATH');

        return is_string($path) && $path !== '' ? $path : $this->userDataPath();
    }

    public function journalPath(): string
    {
        return rtrim($this->userDataPath(), '/\\') . DIRECTORY_SEPARATOR . 'restore.journal';
    }
}
