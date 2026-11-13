<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\RestoreInstaller;
use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreJournalTrust;
use App\Support\Backup\RestoreRecovery;
use App\Support\Backup\RestoreRecoveryCoordinator;
use App\Support\Backup\RestoreTargetResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreRecoveryCoordinatorTest extends TestCase
{
    private string $directory;

    private array $roots;

    private array $resources;

    protected function setUp(): void
    {
        parent::setUp();
        $directory = sys_get_temp_dir() . '/hellen-coordinator-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $this->directory = realpath($directory);
        mkdir($this->directory . '/public');
        $this->roots = ['hotels' => [$this->directory . '/public'], 'payments' => [$this->directory . '/public']];
        $targets = (new RestoreTargetResolver())->resolve($this->directory . '/live.sqlite', $this->roots);
        $this->resources = [];

        foreach ($targets as $name => $target) {
            $prefix = dirname($target) . '/.' . basename($target) . '.restore-test-transaction';
            $this->resources[$name] = ['target' => $target, 'previous' => $prefix . '-previous', 'replacement' => $prefix . '-replacement', 'existed' => $name === 'database'];
        }

        file_put_contents($this->resources['database']['target'], 'old');
        file_put_contents($this->resources['database']['replacement'], 'new');
        mkdir($this->resources['hotels-public']['replacement']);
        mkdir($this->resources['payments-public']['replacement']);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_reconstructs_trusted_targets_and_reverts_without_removing_the_journal(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();
        $path = $this->directory . '/restore.journal';
        (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install($path, 'test-transaction', $this->resources, $lock, fn (): bool => true);

        (new RestoreRecoveryCoordinator($journal, new RestoreJournalTrust(), new RestoreTargetResolver(), new RestoreRecovery($journal)))
            ->rollback($path, $this->directory . '/live.sqlite', $this->roots, $lock);

        $this->assertSame('old', file_get_contents($this->resources['database']['target']));
        $this->assertFileExists($path);
        $lock->release();
    }

    #[Test]
    public function it_refuses_a_different_configured_database_without_moving_resources(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $journal = new RestoreJournal();
        $path = $this->directory . '/restore.journal';
        (new RestoreInstaller($journal, new RestoreRecovery($journal)))->install($path, 'test-transaction', $this->resources, $lock, fn (): bool => true);

        try {
            (new RestoreRecoveryCoordinator($journal, new RestoreJournalTrust(), new RestoreTargetResolver(), new RestoreRecovery($journal)))
                ->rollback($path, $this->directory . '/other.sqlite', $this->roots, $lock);
            $this->fail('A changed configuration must not be trusted.');
        } catch (RuntimeException) {
            $this->assertSame('new', file_get_contents($this->resources['database']['target']));
            $this->assertSame('old', file_get_contents($this->resources['database']['previous']));
        } finally {
            $lock->release();
        }
    }
}
