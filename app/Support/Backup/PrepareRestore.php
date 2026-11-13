<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use PDO;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class PrepareRestore
{
    public function __construct(
        private readonly SelectedBackupStager $selectedBackup,
        private readonly BackupArchiveExtractor $extractor,
        private readonly BackupDatabaseCompatibility $compatibility,
        private readonly BackupAttachmentReferences $references,
        private readonly BackupAttachmentDisks $attachmentDisks,
        private readonly Filesystem $filesystem,
    ) {}

    /** The caller owns the private staging directory and must remove it after use. */
    public function prepare(string $archive, string $directory, PDO $current, string $version, int $maxFiles, int $maxBytes): array
    {
        if (! is_dir($directory) || is_link($directory)) {
            throw new RuntimeException('The restore staging directory is unavailable.');
        }

        $dataset = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . 'dataset';

        if (! @mkdir($dataset, 0700)) {
            throw new RuntimeException('Unable to prepare the restore dataset.');
        }

        $stagedArchive = null;

        try {
            $stagedArchive = $this->selectedBackup->stage($archive, $directory, $maxBytes);
            $manifest = $this->extractor->extract($stagedArchive, $dataset, $maxFiles, $maxBytes);
            $this->attachmentDisks->validateManifest($manifest);
            $snapshot = new PDO('sqlite:' . $dataset . DIRECTORY_SEPARATOR . 'database.sqlite', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $this->compatibility->verify($snapshot, $current, $manifest, $version);

            $this->validateReferences($snapshot, $manifest['files']);

            $snapshot = null;

            return $manifest;
        } catch (Throwable $exception) {
            $snapshot = null;

            try {
                $this->filesystem->remove(array_filter([$dataset, $stagedArchive]));
            } catch (Throwable $cleanupException) {
                throw new RestoreFailure('Restore preparation failed and temporary files could not be removed.', $exception, $cleanupException);
            }

            throw $exception;
        }
    }

    private function validateReferences(PDO $snapshot, array $files): void
    {
        foreach ($this->references->read($snapshot) as $paths) {
            foreach ($paths as $path) {
                if (! isset($files[$path])) {
                    throw new RuntimeException('The backup is missing a referenced attachment.');
                }
            }
        }
    }
}
