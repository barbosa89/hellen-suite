<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\ActiveSqliteDatabase;
use App\Support\Backup\BackupFailure;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\DesktopBackupExport;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopMaintenanceLock;
use App\Support\Backup\DesktopProcessLease;
use App\Support\Backup\DesktopRestoreRecovery;
use App\Support\Backup\DesktopRestoreRecoveryRunner;
use App\Support\Backup\ExportBackup;
use App\Support\Backup\RestoreFailure;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;
use ZipArchive;

class DesktopBackupExportTest extends TestCase
{
    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-desktop-export-' . bin2hex(random_bytes(8));

        mkdir($this->directory);
        mkdir($this->directory . '/public');

        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');

        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory);
        config()->set('filesystems.default', 'public');
        config()->set('filesystems.disks.public', ['driver' => 'local', 'root' => $this->directory . '/public']);
        config()->set('nativephp.version', '1.2.3');
        config()->set('backup.maintenance_timeout_ms', 0);
        config()->set('database.connections.desktop_backup_test', ['driver' => 'sqlite', 'database' => $this->directory . '/active.sqlite', 'prefix' => '']);
        touch($this->directory . '/active.sqlite');

        DB::setDefaultConnection('desktop_backup_test');
        DB::statement('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        DB::statement("INSERT INTO migrations VALUES ('first', 1)");
        DB::statement('CREATE TABLE hotels (image TEXT)');
        DB::statement('CREATE TABLE payments (support_path TEXT)');
    }

    protected function tearDown(): void
    {
        DB::disconnect('desktop_backup_test');

        if ($this->previousUserDataPath === false) {
            putenv('NATIVEPHP_USER_DATA_PATH');
        } else {
            putenv('NATIVEPHP_USER_DATA_PATH=' . $this->previousUserDataPath);
        }

        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function recovery_preserves_acquisition_and_lease_resumption_failures(): void
    {
        config()->set('nativephp-internal.running', true);
        $lease = new DesktopProcessLease();
        $lease->hold((new DesktopMaintenanceGate($this->directory))->enter());
        unlink($this->directory . '/backup-maintenance.lock');
        mkdir($this->directory . '/backup-maintenance.lock');
        file_put_contents($this->directory . '/restore.journal', 'pending');
        $runner = new DesktopRestoreRecoveryRunner(new DesktopMaintenanceLock(), $lease, app(DesktopRestoreRecovery::class));

        try {
            $runner->run($this->directory, $this->directory . '/active.sqlite', []);
            $this->fail('The lock errors must be reported.');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(RestoreFailure::class, $exception);
            $this->assertSame('Unable to open the maintenance lock.', $exception->getPrevious()?->getMessage());
            $this->assertSame('Unable to open the maintenance lock.', $exception->secondary->getMessage());
        } finally {
            $lease->release();
        }
    }

    #[Test]
    public function it_preserves_the_export_and_lease_resumption_failures(): void
    {
        config()->set('nativephp-internal.running', true);
        $lease = new DesktopProcessLease();
        $lease->hold((new DesktopMaintenanceGate($this->directory))->enter());
        $failure = new RuntimeException('Database unavailable.');
        $database = Mockery::mock(DatabaseManager::class);
        $database->shouldReceive('connection')->once()->andReturnUsing(function () use ($failure): never {
            unlink($this->directory . '/backup-maintenance.lock');
            mkdir($this->directory . '/backup-maintenance.lock');
            throw $failure;
        });
        $export = new DesktopBackupExport(new DesktopMaintenanceLock(), new ActiveSqliteDatabase($database), app(ExportBackup::class), $lease);

        try {
            $export->export($this->directory . '/backup.zip');
            $this->fail('A failed export and lease resumption must be reported.');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(BackupFailure::class, $exception);
            $this->assertSame($failure, $exception->getPrevious());
            $this->assertSame('Unable to open the maintenance lock.', $exception->secondary->getMessage());
        } finally {
            $lease->release();
        }
    }

    #[Test]
    public function it_exports_the_active_desktop_database_with_the_native_version(): void
    {
        config()->set('nativephp-internal.running', true);
        $target = $this->directory . '/backup.zip';

        app(DesktopBackupExport::class)->export($target);

        $zip = new ZipArchive();

        $this->assertTrue($zip->open($target));

        $manifest = json_decode($zip->getFromName('manifest.json'), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('1.2.3', $manifest['application_version']);
        $this->assertNotFalse($zip->getFromName('database.sqlite'));

        $zip->close();

        $this->assertSame([], glob($this->directory . '/backup-*', GLOB_ONLYDIR));
    }

    #[Test]
    public function it_is_unavailable_in_web_deployments(): void
    {
        config()->set('nativephp-internal.running', false);

        $this->expectException(RuntimeException::class);
        app(DesktopBackupExport::class)->export($this->directory . '/backup.zip');
    }

    #[Test]
    public function it_does_not_export_while_another_process_holds_a_shared_lock(): void
    {
        config()->set('nativephp-internal.running', true);
        $shared = BackupMaintenanceLock::shared($this->directory . '/backup-maintenance.lock');

        try {
            app(DesktopBackupExport::class)->export($this->directory . '/backup.zip');
            $this->fail('Active work must block export.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($this->directory . '/backup.zip');
        } finally {
            $shared->release();
        }
    }

    #[Test]
    public function it_temporarily_releases_and_then_restores_its_own_console_process_lease(): void
    {
        config()->set('nativephp-internal.running', true);
        $lease = app(DesktopProcessLease::class);
        $lease->hold((new DesktopMaintenanceGate($this->directory))->enter());

        try {
            app(DesktopBackupExport::class)->export($this->directory . '/backup.zip');
            $this->assertFileExists($this->directory . '/backup.zip');
            $this->assertTrue($lease->isHeld());
        } finally {
            $lease->release();
        }
    }
}
