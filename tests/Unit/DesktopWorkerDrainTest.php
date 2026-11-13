<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\DesktopMaintenanceGate;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use ZipArchive;

class DesktopWorkerDrainTest extends TestCase
{
    private string $directory;

    private string $database;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-worker-drain-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/storage');
        mkdir($this->directory . '/storage/app', 0700);
        mkdir($this->directory . '/storage/app/public', 0700);
        $this->database = $this->directory . '/active.sqlite';
        $database = new PDO('sqlite:' . $this->database);
        $database->exec('CREATE TABLE jobs (id INTEGER PRIMARY KEY, queue TEXT, payload TEXT, attempts INTEGER, reserved_at INTEGER, available_at INTEGER, created_at INTEGER)');
        $database->exec('CREATE TABLE cache (key TEXT PRIMARY KEY, value TEXT, expiration INTEGER)');
        $database->exec('CREATE TABLE cache_locks (key TEXT PRIMARY KEY, owner TEXT, expiration INTEGER)');
        $database->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        $database->exec("INSERT INTO migrations VALUES ('first', 1)");
        $database->exec('CREATE TABLE hotels (image TEXT)');
        $database->exec('CREATE TABLE payments (support_path TEXT)');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function a_desktop_export_succeeds_with_the_real_queue_listener_running(): void
    {
        $listener = new Process([PHP_BINARY, 'artisan', 'queue:listen', '--sleep=1', '--timeout=10'], dirname(__DIR__, 2), $this->environment());
        $listener->setTimeout(20);
        $listener->start();

        try {
            $ready = $listener->waitUntil(fn (string $type, string $output): bool => str_contains($output, 'Processing jobs'));
            $this->assertTrue($ready, $listener->getErrorOutput());
            $script = <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make(App\Support\Backup\DesktopBackupExport::class)->export(getenv('TEST_BACKUP_DESTINATION'));
$app->terminate();
echo 'EXPORT_COMPLETED';
PHP;
            $export = new Process([PHP_BINARY, '-r', $script], dirname(__DIR__, 2), [
                ...$this->environment(),
                'TEST_BACKUP_DESTINATION' => $this->directory . '/listener-backup.zip',
            ]);
            $export->setTimeout(20);
            $export->run();

            $this->assertTrue($export->isSuccessful(), $export->getErrorOutput());
            $this->assertStringContainsString('EXPORT_COMPLETED', $export->getOutput());
            $this->assertFileExists($this->directory . '/listener-backup.zip');
        } finally {
            if ($listener->isRunning()) {
                $listener->stop(1);
            }
        }
    }

    #[Test]
    public function a_real_queue_listener_does_not_hold_the_dataset_lock_during_maintenance(): void
    {
        $listener = new Process([PHP_BINARY, 'artisan', 'queue:listen', '--sleep=1', '--timeout=10'], dirname(__DIR__, 2), $this->environment());
        $listener->setTimeout(15);
        $listener->start();

        try {
            $ready = $listener->waitUntil(fn (string $type, string $output): bool => str_contains($output, 'Processing jobs'));
            $this->assertTrue($ready, $listener->getErrorOutput());
            $request = (new DesktopMaintenanceGate($this->directory))->request();

            try {
                $deadline = microtime(true) + 5;
                $acquired = false;

                do {
                    if ($request->tryAcquire()) {
                        $acquired = true;
                        break;
                    }

                    usleep(50000);
                } while (microtime(true) < $deadline);

                $this->assertTrue($acquired, 'The listener must release its process lease when idle.');
            } finally {
                $request->release();
            }
        } finally {
            if ($listener->isRunning()) {
                $listener->stop(1);
            }
        }
    }

    #[Test]
    public function a_real_desktop_worker_exits_and_releases_its_process_lease_when_maintenance_is_requested(): void
    {
        $worker = $this->worker();
        $worker->start();

        try {
            $ready = $worker->waitUntil(fn (string $type, string $output): bool => str_contains($output, 'WORKER_READY'));
            $this->assertTrue($ready, $worker->getErrorOutput());
            $request = (new DesktopMaintenanceGate($this->directory))->request();

            try {
                $worker->wait();
                $this->assertTrue($request->tryAcquire(), 'The worker must release its process lease.');
            } finally {
                $request->release();
            }
        } finally {
            if ($worker->isRunning()) {
                $worker->stop(1);
            }
        }
    }

    #[Test]
    public function a_separate_desktop_export_process_drains_the_worker_and_creates_a_backup(): void
    {
        $worker = $this->worker();
        $worker->start();

        try {
            $ready = $worker->waitUntil(fn (string $type, string $output): bool => str_contains($output, 'WORKER_READY'));
            $this->assertTrue($ready, $worker->getErrorOutput());
            $script = <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make(App\Support\Backup\DesktopBackupExport::class)->export(getenv('TEST_BACKUP_DESTINATION'));
$app->terminate();
echo 'EXPORT_COMPLETED';
PHP;
            $export = new Process([PHP_BINARY, '-r', $script], dirname(__DIR__, 2), [
                ...$this->environment(),
                'TEST_BACKUP_DESTINATION' => $this->directory . '/backup.zip',
            ]);
            $export->setTimeout(20);
            $export->run();
            $this->assertTrue($export->isSuccessful(), $export->getErrorOutput());
            $this->assertStringContainsString('EXPORT_COMPLETED', $export->getOutput());
            $zip = new ZipArchive();
            $this->assertTrue($zip->open($this->directory . '/backup.zip'));
            $this->assertNotFalse($zip->getFromName('database.sqlite'));
            $zip->close();
        } finally {
            if ($worker->isRunning()) {
                $worker->stop(1);
            }
        }
    }

    private function worker(): Process
    {
        $script = <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
fwrite(STDOUT, "WORKER_READY\n");
fflush(STDOUT);
$kernel->call('queue:work', ['--sleep' => 1, '--timeout' => 10]);
$app->terminate();
PHP;

        $worker = new Process([PHP_BINARY, '-r', $script], dirname(__DIR__, 2), $this->environment());
        $worker->setTimeout(15);

        return $worker;
    }

    private function environment(): array
    {
        return [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'CACHE_STORE' => 'database',
            'FILESYSTEM_DISK' => 'public',
            'NATIVEPHP_RUNNING' => 'true',
            'NATIVEPHP_USER_DATA_PATH' => $this->directory,
            'NATIVEPHP_STORAGE_PATH' => $this->directory . '/storage',
            'NATIVEPHP_DATABASE_PATH' => $this->database,
        ];
    }
}
