<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Support\Backup\BackupAttachmentDisks;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\RestoreInstaller;
use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreRecovery;
use App\Support\Backup\RestoreResourcePlan;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class DesktopRestoreRecoveryTest extends TestCase
{
    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-startup-recovery-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/storage');
        mkdir($this->directory . '/storage/app', 0700);
        mkdir($this->directory . '/storage/app/public', 0700);
        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');
    }

    protected function tearDown(): void
    {
        if ($this->previousUserDataPath === false) {
            putenv('NATIVEPHP_USER_DATA_PATH');
        } else {
            putenv('NATIVEPHP_USER_DATA_PATH=' . $this->previousUserDataPath);
        }

        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function native_bootstrap_refuses_to_start_while_a_restore_journal_requires_recovery(): void
    {
        file_put_contents($this->directory . '/restore.journal', 'pending');
        $process = $this->bootstrapProcess();
        $process->run();

        $this->assertFalse($process->isSuccessful());
        $this->assertStringNotContainsString('BOOT_COMPLETED', $process->getOutput());
        $this->assertStringContainsString('restore', strtolower($process->getErrorOutput()));
    }

    #[Test]
    public function native_bootstrap_keeps_a_verified_restored_dataset(): void
    {
        $database = $this->directory . '/active.sqlite';
        $this->createDatabase($database, 'previous');
        $this->installRestore($database, 'restored');
        $process = $this->bootstrapProcess();
        $process->run();

        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
        $this->assertStringContainsString('BOOT_COMPLETED', $process->getOutput());
        $this->assertSame('restored', $this->readValue($database, 'value'));
        $this->assertFileDoesNotExist($this->directory . '/restore.journal');
    }

    #[Test]
    public function native_bootstrap_reverts_a_restore_that_was_never_verified(): void
    {
        $database = $this->directory . '/active.sqlite';
        $this->createDatabase($database, 'previous');
        $resources = $this->installRestore($database, 'restored', false);
        $process = $this->bootstrapProcess();
        $process->run();

        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
        $this->assertSame('previous', $this->readValue($database, 'value'));
        $this->assertFileDoesNotExist($this->directory . '/restore.journal');
        $this->assertFileDoesNotExist($resources['database']['previous']);
    }

    private function installRestore(string $database, string $value, bool $verify = true): array
    {
        $public = $this->directory . '/storage/app/public';
        mkdir($public . '/hotels', 0700, true);
        mkdir($public . '/payments', 0700, true);
        file_put_contents($public . '/hotels/photo.jpg', 'photo');
        $roots = ['hotels' => [$public], 'payments' => [$public]];
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $plan = (new RestoreResourcePlan(new BackupAttachmentDisks()))->build($database, $roots, $this->manifest(), 'recovery', $lock);
        $this->stageReplacements($plan, $value);
        $journal = new RestoreJournal();

        if ($verify) {
            (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
                $this->directory . '/restore.journal', 'recovery', $plan['resources'], $lock, fn (): bool => true,
            );
            $lock->release();

            return $plan['resources'];
        }

        $journal->create($this->directory . '/restore.journal', 'recovery', $plan['resources']);
        $journal->append($this->directory . '/restore.journal', 'preserve_started', 'database');
        rename($plan['resources']['database']['target'], $plan['resources']['database']['previous']);
        $journal->append($this->directory . '/restore.journal', 'preserve_completed', 'database');
        $journal->append($this->directory . '/restore.journal', 'install_started', 'database');
        rename($plan['resources']['database']['replacement'], $plan['resources']['database']['target']);
        $journal->append($this->directory . '/restore.journal', 'install_completed', 'database');
        $lock->release();

        return $plan['resources'];
    }

    private function manifest(): array
    {
        return [
            'files' => [
                'database.sqlite' => ['size' => 0, 'sha256' => hash('sha256', '')],
                'hotels/photo.jpg' => ['size' => 5, 'sha256' => hash('sha256', 'photo')],
            ],
            'attachment_disks' => ['hotels/photo.jpg' => 'public'],
        ];
    }

    private function stageReplacements(array $plan, string $value): void
    {
        foreach ($plan['resources'] as $name => $paths) {
            if ($name === 'database') {
                $this->createDatabase($paths['replacement'], $value);

                continue;
            }

            mkdir($paths['replacement'], 0700, true);
            file_put_contents($paths['replacement'] . '/photo.jpg', 'photo');
        }
    }

    private function createDatabase(string $path, string $value): void
    {
        $database = new PDO('sqlite:' . $path);
        $database->exec('CREATE TABLE records (value TEXT)');
        $database->exec("INSERT INTO records VALUES ('" . $value . "')");
        $database = null;
    }

    private function readValue(string $path, string $column): null|string
    {
        $database = new PDO('sqlite:' . $path);
        $value = $database->query('SELECT ' . $column . ' FROM records')->fetchColumn();

        return $value === false ? null : $value;
    }

    private function bootstrapProcess(): Process
    {
        $script = <<<'PHP'
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo 'BOOT_COMPLETED';
PHP;

        return new Process([PHP_BINARY, '-r', $script], dirname(__DIR__, 3), [
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'FILESYSTEM_DISK' => 'public',
            'NATIVEPHP_RUNNING' => 'true',
            'NATIVEPHP_USER_DATA_PATH' => $this->directory,
            'NATIVEPHP_STORAGE_PATH' => $this->directory . '/storage',
            'NATIVEPHP_DATABASE_PATH' => $this->directory . '/active.sqlite',
        ]);
    }
}
