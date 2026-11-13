<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\RestoreCleanup;
use App\Support\Backup\RestoreFailure;
use App\Support\Backup\RestoreInstaller;
use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreRecovery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class RestoreInstallerTest extends TestCase
{
    private string $directory;

    private array $resources;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-installer-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
        $this->resources = [
            'database' => [
                'target' => $this->directory . '/live.sqlite',
                'previous' => $this->directory . '/previous.sqlite',
                'replacement' => $this->directory . '/replacement.sqlite',
                'existed' => true,
            ],
            'hotels' => [
                'target' => $this->directory . '/hotels',
                'previous' => $this->directory . '/previous-hotels',
                'replacement' => $this->directory . '/replacement-hotels',
                'existed' => false,
            ],
        ];
        file_put_contents($this->resources['database']['target'], 'old');
        file_put_contents($this->resources['database']['replacement'], 'new');
        mkdir($this->resources['hotels']['replacement']);
        file_put_contents($this->resources['hotels']['replacement'] . '/photo', 'photo');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_installs_prepared_resources_and_keeps_the_recovery_copies_until_startup(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();
        $installer = new RestoreInstaller($journal, new RestoreRecovery($journal));
        $verified = false;

        $installer->install($this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock, function () use (&$verified): bool {
            $this->assertSame('new', file_get_contents($this->resources['database']['target']));
            $this->assertSame('photo', file_get_contents($this->resources['hotels']['target'] . '/photo'));
            $verified = true;

            return true;
        });

        $this->assertTrue($verified);
        $this->assertSame('new', file_get_contents($this->resources['database']['target']));
        $this->assertSame('old', file_get_contents($this->resources['database']['previous']));
        $this->assertFileExists($this->directory . '/restore.journal');
        $lock->release();
    }

    #[Test]
    public function it_reverts_installed_resources_if_verification_fails(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();

        try {
            (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
                $this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock,
                fn () => throw new RuntimeException('Verification failed.'),
            );
            $this->fail('Failed verification must fail the installation.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Verification failed.', $exception->getMessage());
            $this->assertSame('old', file_get_contents($this->resources['database']['target']));
            $this->assertDirectoryDoesNotExist($this->resources['hotels']['target']);
            $this->assertFileExists($this->directory . '/restore.journal');
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_preserves_verification_and_rollback_errors_when_recovery_fails(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();
        $failure = new RuntimeException('Verification failed.');

        try {
            (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
                $this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock,
                function () use ($failure): void {
                    unlink($this->resources['database']['previous']);
                    throw $failure;
                },
            );
            $this->fail('Failed verification and recovery must fail the installation.');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(RestoreFailure::class, $exception);
            $this->assertSame($failure, $exception->getPrevious());
            $this->assertSame('The restore resource state is ambiguous.', $exception->secondary->getMessage());
            $this->assertFileExists($this->directory . '/restore.journal');
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_keeps_the_journal_when_the_recovery_copies_cannot_be_discarded(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();

        (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
            $this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock, fn (): bool => true,
        );

        try {
            (new RestoreCleanup(new class() extends Filesystem
            {
                public function remove($files, bool $recursive = false): void
                {
                    throw new IOException('Cannot remove.');
                }
            }))->discard($this->directory . '/restore.journal', $this->resources);
            $this->fail('A failed discard must stop the operation.');
        } catch (IOException) {
            $this->assertFileExists($this->directory . '/restore.journal');
            $this->assertSame('old', file_get_contents($this->resources['database']['previous']));
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_rejects_a_shared_lock_before_writing_the_journal(): void
    {
        $lock = BackupMaintenanceLock::shared($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();

        try {
            (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install($this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock, fn () => null);
            $this->fail('Exclusive access is required.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($this->directory . '/restore.journal');
            $this->assertSame('old', file_get_contents($this->resources['database']['target']));
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_refuses_to_replace_a_database_with_journal_files_beside_a_non_database_file(): void
    {
        file_put_contents($this->resources['database']['target'] . '-wal', 'stale');
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();

        try {
            (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install($this->directory . '/restore.journal', 'transaction-1', $this->resources, $lock, fn () => true);
            $this->fail('A stale WAL must prevent replacement.');
        } catch (RuntimeException) {
            $this->assertSame('old', file_get_contents($this->resources['database']['target']));
        } finally {
            $lock->release();
        }
    }
}
