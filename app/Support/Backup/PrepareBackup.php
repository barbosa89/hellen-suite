<?php

declare(strict_types=1);

namespace App\Support\Backup;

use App\Exceptions\RuntimeException;
use DateTimeImmutable;
use PDO;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

/** Internal preparation only. All writers must participate in the same maintenance protocol. */
class PrepareBackup
{
    public function __construct(
        private readonly SqliteSnapshot $snapshotter,
        private readonly BackupAttachmentReferences $references,
        private readonly BackupDiskRoots $roots,
        private readonly BackupAttachmentLocator $locator,
        private readonly BackupAttachmentDisks $attachmentDisks,
        private readonly BackupAttachmentStager $stager,
        private readonly BackupManifest $manifest,
        private readonly BackupArchiveWriter $writer,
        private readonly Filesystem $filesystem,
    ) {}

    /** The caller owns the private staging directory and must clean it after use. */
    public function prepare(PDO $database, BackupMaintenanceLock $lock, string $directory, string $version, DateTimeImmutable $createdAt): string
    {
        if (! $lock->isExclusive() || ! is_dir($directory) || is_link($directory)) {
            throw new RuntimeException('Exclusive maintenance access and private staging are required.');
        }

        $dataset = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . 'dataset';

        if (! @mkdir($dataset, 0700)) {
            throw new RuntimeException('Unable to prepare the backup dataset.');
        }

        try {
            $snapshotPath = $dataset . DIRECTORY_SEPARATOR . 'database.sqlite';
            $this->snapshotter->create($database, $snapshotPath);
            $snapshot = new PDO('sqlite:' . $snapshotPath, options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $references = $this->references->read($snapshot);
            $roots = $this->roots->resolve();
            $attachments = $this->locator->locate($references, $roots);
            $files = $this->stager->stage($attachments, $dataset);
            $files['database.sqlite'] = $this->snapshotMetadata($snapshotPath);
            $manifest = $this->manifest->create($snapshot, $version, $createdAt, $files);
            $manifest['attachment_disks'] = $this->attachmentDisks->fromResolved($attachments, $roots);
            $snapshot = null;
            $archive = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . 'backup.zip';
            $this->writer->create($dataset, $archive, $manifest);

            return $archive;
        } catch (Throwable $exception) {
            $snapshot = null;

            try {
                $this->filesystem->remove($dataset);
            } catch (Throwable $cleanupException) {
                throw new BackupFailure('Backup failed and the temporary dataset could not be removed.', $exception, $cleanupException);
            }

            throw $exception;
        }
    }

    /** @return array{size: int, sha256: string} */
    private function snapshotMetadata(string $path): array
    {
        $size = filesize($path);
        $hash = hash_file('sha256', $path);

        if ($size === false || $hash === false) {
            throw new RuntimeException('Unable to verify the backup database.');
        }

        return ['size' => $size, 'sha256' => $hash];
    }
}
