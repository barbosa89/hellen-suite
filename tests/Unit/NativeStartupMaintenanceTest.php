<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\DesktopMaintenanceGate;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class NativeStartupMaintenanceTest extends TestCase
{
    private string $directory;

    private string $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-startup-lock-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/storage');
        $this->database = $this->directory . '/active.sqlite';
        $database = new PDO('sqlite:' . $this->database);
        $database->exec('CREATE TABLE records (value TEXT)');
        $database->exec('PRAGMA journal_mode=DELETE');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function native_bootstrap_cannot_modify_sqlite_during_exclusive_maintenance(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $process = $this->bootstrapProcess();
        $process->run();

        $this->assertFalse($process->isSuccessful());
        $this->assertStringNotContainsString('BOOT_COMPLETED', $process->getOutput());
        $database = new PDO('sqlite:' . $this->database);
        $this->assertSame('delete', strtolower($database->query('PRAGMA journal_mode')->fetchColumn()));
        $lock->release();
    }

    #[Test]
    public function native_bootstrap_proceeds_and_releases_the_lock_without_maintenance(): void
    {
        $process = $this->bootstrapProcess();
        $process->run();

        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
        $this->assertStringContainsString('BOOT_COMPLETED', $process->getOutput());
        $database = new PDO('sqlite:' . $this->database);
        $this->assertSame('wal', strtolower($database->query('PRAGMA journal_mode')->fetchColumn()));
        $database = null;
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $this->assertTrue($lock->isExclusive());
        $lock->release();
    }

    #[Test]
    public function native_bootstrap_cannot_enter_while_maintenance_is_draining(): void
    {
        $request = (new DesktopMaintenanceGate($this->directory))->request();
        $process = $this->bootstrapProcess();
        $process->run();

        $this->assertFalse($process->isSuccessful());
        $this->assertStringNotContainsString('BOOT_COMPLETED', $process->getOutput());
        $request->release();
    }

    #[Test]
    public function native_console_process_keeps_its_dataset_lock_until_termination(): void
    {
        $script = <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$path = getenv('NATIVEPHP_USER_DATA_PATH') . '/backup-maintenance.lock';
try {
    App\Support\Backup\BackupMaintenanceLock::exclusive($path);
    echo 'UNPROTECTED';
} catch (App\Exceptions\RuntimeException) {
    echo 'PROTECTED';
}
$app->terminate();
$lock = App\Support\Backup\BackupMaintenanceLock::exclusive($path);
$lock->release();
echo ':RELEASED';
PHP;
        $process = $this->bootstrapProcess($script);
        $process->run();

        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
        $this->assertSame('PROTECTED:RELEASED', $process->getOutput());
    }

    private function bootstrapProcess(null|string $script = null): Process
    {
        $script ??= <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'BOOT_COMPLETED';
PHP;

        return new Process([PHP_BINARY, '-r', $script], dirname(__DIR__, 2), [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'NATIVEPHP_RUNNING' => 'true',
            'NATIVEPHP_USER_DATA_PATH' => $this->directory,
            'NATIVEPHP_STORAGE_PATH' => $this->directory . '/storage',
            'NATIVEPHP_DATABASE_PATH' => $this->database,
        ]);
    }
}
