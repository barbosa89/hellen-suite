<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupArchivePublisher;
use App\Support\Backup\BackupFailure;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\ExportBackup;
use App\Support\Backup\PrepareBackup;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;
use ZipArchive;

class ExportBackupTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-export-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/private');
        config()->set('filesystems.default', 'public');
        config()->set('filesystems.disks.public', ['driver' => 'local', 'root' => $this->directory . '/public']);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_preserves_export_and_workspace_cleanup_failures(): void
    {
        $probe = $this->directory . '/symlink-probe';

        if (! function_exists('symlink') || ! @symlink($this->directory, $probe)) {
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        unlink($probe);

        $database = $this->database();
        $database->exec("INSERT INTO hotels VALUES ('hotels/missing.jpg')");
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $filesystem = new class() extends Filesystem
        {
            public function remove($files, bool $recursive = false): void
            {
                parent::remove($files, $recursive);
                $workspace = dirname($files);
                rmdir($workspace);
                symlink(dirname($workspace), $workspace);
            }
        };
        $preparer = app()->makeWith(PrepareBackup::class, ['filesystem' => $filesystem]);
        $exporter = new ExportBackup($preparer, app(BackupArchivePublisher::class));

        try {
            $exporter->export($database, $lock, $this->directory . '/private', $this->directory . '/backup.zip', '1.2.3', new DateTimeImmutable());
            $this->fail('Failed export and workspace cleanup must be reported.');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(BackupFailure::class, $exception);
            $this->assertSame('A backup attachment is missing or ambiguous.', $exception->getPrevious()?->getMessage());
            $this->assertSame('Unable to remove the backup workspace.', $exception->secondary->getMessage());
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_exports_a_backup_and_cleans_the_workspace(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $target = $this->directory . '/backup.zip';

        app(ExportBackup::class)->export($this->database(), $lock, $this->directory . '/private', $target, '1.2.3', new DateTimeImmutable('2026-11-12T15:00:00Z'));

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($target));
        $this->assertNotFalse($zip->getFromName('database.sqlite'));
        $this->assertNotFalse($zip->getFromName('manifest.json'));
        $zip->close();
        $this->assertSame([], glob($this->directory . '/private/backup-*'));
        $lock->release();
    }

    #[Test]
    public function it_preserves_existing_backups_and_cleans_the_workspace_on_failure(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $target = $this->directory . '/backup.zip';
        file_put_contents($target, 'existing');

        try {
            app(ExportBackup::class)->export($this->database(), $lock, $this->directory . '/private', $target, '1.2.3', new DateTimeImmutable());
            $this->fail('An existing backup must not be overwritten.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($target));
            $this->assertSame([], glob($this->directory . '/private/backup-*'));
        } finally {
            $lock->release();
        }
    }

    private function database(): PDO
    {
        $database = new PDO('sqlite:' . $this->directory . '/live.sqlite', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $database->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        $database->exec("INSERT INTO migrations VALUES ('first', 1)");
        $database->exec('CREATE TABLE hotels (image TEXT)');
        $database->exec('CREATE TABLE payments (support_path TEXT)');

        return $database;
    }
}
