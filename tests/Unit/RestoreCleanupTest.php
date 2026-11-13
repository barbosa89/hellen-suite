<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\RestoreCleanup;
use App\Support\Backup\RestoreInstaller;
use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreRecovery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class RestoreCleanupTest extends TestCase
{
    private string $directory;

    private array $resources;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-cleanup-' . bin2hex(random_bytes(8));
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
        file_put_contents($this->resources['database']['target'], 'old');
        file_put_contents($this->resources['database']['replacement'], 'new');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_discards_the_journal_and_the_retained_copies_after_a_verified_restore(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();
        $path = $this->directory . '/restore.journal';
        (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
            $path, 'transaction-1', $this->resources, $lock, fn (): bool => true,
        );

        (new RestoreCleanup())->discard($path, $this->resources);

        $this->assertFileDoesNotExist($path);
        $this->assertFileDoesNotExist($this->resources['database']['previous']);
        $this->assertFileDoesNotExist($this->resources['database']['replacement']);
        $this->assertSame('new', file_get_contents($this->resources['database']['target']));
        $lock->release();
    }

    #[Test]
    public function it_keeps_the_journal_when_the_discard_fails(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();
        $path = $this->directory . '/restore.journal';
        (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install(
            $path, 'transaction-1', $this->resources, $lock, fn (): bool => true,
        );
        file_put_contents($this->resources['database']['previous'], 'old');

        try {
            (new RestoreCleanup(new class() extends Filesystem
            {
                public function remove($files, bool $recursive = false): void
                {
                    throw new IOException('Cannot remove.');
                }
            }))->discard($path, $this->resources);
            $this->fail('A failed discard must stop the operation.');
        } catch (IOException) {
            $this->assertFileExists($path);
            $this->assertSame('old', file_get_contents($this->resources['database']['previous']));
        } finally {
            $lock->release();
        }
    }
}
