<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\RestoreInstaller;
use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreRecovery;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class RestoreSidecarsTest extends TestCase
{
    private string $directory;

    private array $resources;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-sidecars-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
        $this->resources = [
            'database' => [
                'target' => $this->directory . '/live.sqlite',
                'previous' => $this->directory . '/previous.sqlite',
                'replacement' => $this->directory . '/replacement.sqlite',
                'existed' => true,
            ],
        ];
        $this->crashWithPendingWrites($this->resources['database']['target'], 'previous');
        $this->createDatabase($this->resources['database']['replacement'], 'restored');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_settles_the_wal_file_before_moving_the_previous_database(): void
    {
        $this->assertFileExists($this->resources['database']['target'] . '-wal');
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();

        (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
            $this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock, fn (): bool => true,
        );

        $this->assertFileDoesNotExist($this->resources['database']['previous'] . '-wal');
        $this->assertFileDoesNotExist($this->resources['database']['previous'] . '-shm');
        $this->assertFileDoesNotExist($this->resources['database']['target'] . '-wal');
        $this->assertFileDoesNotExist($this->resources['database']['target'] . '-shm');
        $this->assertSame('previous', $this->readValue($this->resources['database']['previous']));
        $this->assertSame('restored', $this->readValue($this->resources['database']['target']));
        $lock->release();
    }

    #[Test]
    public function it_keeps_committed_transactions_in_the_previous_database(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();
        (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
            $this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock, fn (): bool => true,
        );
        (new RestoreRecovery($journal))->rollback($this->directory . '/restore.journal', $this->resources);

        $this->assertSame('previous', $this->readValue($this->resources['database']['target']));
        $this->assertFileDoesNotExist($this->resources['database']['target'] . '-wal');
        $this->assertFileDoesNotExist($this->resources['database']['previous']);
        $lock->release();
    }

    /** Simulate a crashed process that left committed transactions in the WAL. */
    private function crashWithPendingWrites(string $path, string $value): void
    {
        $script = <<<'PHP'
$path = $argv[1];
$value = $argv[2];
$database = new PDO('sqlite:' . $path);
$database->exec('PRAGMA journal_mode = WAL');
$database->exec('CREATE TABLE records (value TEXT)');
$database->exec("INSERT INTO records VALUES ('" . $value . "')");
posix_kill(getmypid(), 9);
PHP;
        $process = new Process([PHP_BINARY, '-r', $script, $path, $value]);
        $process->setTimeout(10);

        try {
            $process->run();
        } catch (\RuntimeException) {
            // A killed process reports a signal instead of an exit code.
        }

        $this->assertTrue($process->getTermSignal() === 9 || $process->getExitCode() === 137);
    }

    private function createDatabase(string $path, string $value): void
    {
        $database = new PDO('sqlite:' . $path);
        $database->exec('CREATE TABLE records (value TEXT)');
        $database->exec("INSERT INTO records VALUES ('" . $value . "')");
        $database = null;
    }

    private function readValue(string $path): null|string
    {
        $database = new PDO('sqlite:' . $path);
        $value = $database->query('SELECT value FROM records')->fetchColumn();

        return $value === false ? null : $value;
    }
}
