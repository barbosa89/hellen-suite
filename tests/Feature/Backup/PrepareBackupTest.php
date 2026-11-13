<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupFailure;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\PrepareBackup;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;
use ZipArchive;

class PrepareBackupTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-prepare-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/public/hotels');
        mkdir($this->directory . '/public/payments');
        mkdir($this->directory . '/stage');
        config()->set('filesystems.default', 'public');
        config()->set('filesystems.disks.public', ['driver' => 'local', 'root' => $this->directory . '/public']);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_preserves_both_the_preparation_and_cleanup_errors(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $cleanupFailure = new RuntimeException('Cleanup failed.');
        $filesystem = new class($cleanupFailure) extends Filesystem
        {
            public function __construct(private readonly RuntimeException $failure) {}

            public function remove($files, bool $recursive = false): void
            {
                throw $this->failure;
            }
        };
        $preparer = app()->makeWith(PrepareBackup::class, ['filesystem' => $filesystem]);

        try {
            $preparer->prepare($this->database(), $lock, $this->directory . '/stage', '1.2.3', new DateTimeImmutable());
            $this->fail('Missing attachments and failed cleanup must be reported.');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(BackupFailure::class, $exception);
            $this->assertSame('A backup attachment is missing or ambiguous.', $exception->getPrevious()?->getMessage());
            $this->assertSame($cleanupFailure, $exception->secondary);
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_prepares_an_archive_with_a_sqlite_snapshot_and_referenced_attachments(): void
    {
        $database = $this->database();
        file_put_contents($this->directory . '/public/hotels/photo.jpg', 'image');
        file_put_contents($this->directory . '/public/payments/receipt.pdf', 'receipt');
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');

        $archive = app(PrepareBackup::class)->prepare($database, $lock, $this->directory . '/stage', '1.2.3', new DateTimeImmutable('2026-11-12T15:00:00Z'));

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($archive));
        $this->assertSame('image', $zip->getFromName('hotels/photo.jpg'));
        $this->assertSame('receipt', $zip->getFromName('payments/receipt.pdf'));
        $manifest = json_decode($zip->getFromName('manifest.json'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame(['database.sqlite', 'hotels/photo.jpg', 'payments/receipt.pdf'], array_keys($manifest['files']));
        $this->assertSame(['hotels/photo.jpg' => 'public', 'payments/receipt.pdf' => 'public'], $manifest['attachment_disks']);
        $this->assertNotFalse($zip->getFromName('database.sqlite'));
        $zip->close();
        $lock->release();
    }

    #[Test]
    public function it_rejects_a_shared_lock(): void
    {
        $lock = BackupMaintenanceLock::shared($this->directory . '/maintenance.lock');

        $this->expectException(RuntimeException::class);
        app(PrepareBackup::class)->prepare($this->database(), $lock, $this->directory . '/stage', '1.2.3', new DateTimeImmutable());
    }

    #[Test]
    public function it_records_the_actual_disk_role_for_images_across_different_disks(): void
    {
        mkdir($this->directory . '/images');
        mkdir($this->directory . '/images/hotels');
        config()->set('filesystems.default', 'images');
        config()->set('filesystems.disks.images', ['driver' => 'local', 'root' => $this->directory . '/images']);
        $database = $this->database();
        $database->exec("INSERT INTO hotels VALUES ('hotels/public.jpg')");
        file_put_contents($this->directory . '/images/hotels/photo.jpg', 'default image');
        file_put_contents($this->directory . '/public/hotels/public.jpg', 'public image');
        file_put_contents($this->directory . '/public/payments/receipt.pdf', 'receipt');
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');

        $archive = app(PrepareBackup::class)->prepare($database, $lock, $this->directory . '/stage', '1.2.3', new DateTimeImmutable());
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($archive));
        $manifest = json_decode($zip->getFromName('manifest.json'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame([
            'hotels/photo.jpg' => 'default',
            'hotels/public.jpg' => 'public',
            'payments/receipt.pdf' => 'public',
        ], $manifest['attachment_disks']);
        $zip->close();
        $lock->release();
    }

    #[Test]
    public function it_rejects_a_released_exclusive_lock(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $lock->release();

        $this->expectException(RuntimeException::class);
        app(PrepareBackup::class)->prepare($this->database(), $lock, $this->directory . '/stage', '1.2.3', new DateTimeImmutable());
    }

    #[Test]
    public function it_does_not_produce_an_archive_when_a_required_attachment_is_missing(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');

        try {
            app(PrepareBackup::class)->prepare($this->database(), $lock, $this->directory . '/stage', '1.2.3', new DateTimeImmutable());
            $this->fail('Missing attachments must fail preparation.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($this->directory . '/stage/backup.zip');
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
        $database->exec("INSERT INTO hotels VALUES ('hotels/photo.jpg')");
        $database->exec('CREATE TABLE payments (support_path TEXT)');
        $database->exec("INSERT INTO payments VALUES ('payments/receipt.pdf')");

        return $database;
    }
}
