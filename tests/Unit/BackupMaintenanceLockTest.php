<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupMaintenanceLock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BackupMaintenanceLockTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-lock-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function shared_holders_block_exclusive_access_until_all_release(): void
    {
        $path = $this->directory . '/maintenance.lock';
        $first = BackupMaintenanceLock::shared($path);
        $second = BackupMaintenanceLock::shared($path);

        try {
            BackupMaintenanceLock::exclusive($path);
            $this->fail('An exclusive lock must not bypass active shared locks.');
        } catch (RuntimeException) {
            $this->assertFileExists($path);
        }

        $first->release();

        try {
            BackupMaintenanceLock::exclusive($path);
            $this->fail('The second shared lock must still block exclusive access.');
        } catch (RuntimeException) {
            $this->assertFileExists($path);
        }

        $second->release();
        $exclusive = BackupMaintenanceLock::exclusive($path);
        $exclusive->release();
        $this->assertFileExists($path);
    }

    #[Test]
    public function an_exclusive_holder_blocks_other_holders(): void
    {
        $path = $this->directory . '/maintenance.lock';
        $lock = BackupMaintenanceLock::exclusive($path);

        try {
            BackupMaintenanceLock::shared($path);
            $this->fail('A shared lock must not bypass an exclusive lock.');
        } catch (RuntimeException) {
            $this->assertFileExists($path);
        } finally {
            $lock->release();
        }

        $shared = BackupMaintenanceLock::shared($path);
        $shared->release();
    }

    #[Test]
    public function an_exclusive_holder_blocks_a_separate_process(): void
    {
        $path = $this->directory . '/maintenance.lock';
        $lock = BackupMaintenanceLock::exclusive($path);
        $process = proc_open(
            [PHP_BINARY, '-r', '$f = fopen($argv[1], "c+b"); exit(flock($f, LOCK_SH | LOCK_NB) ? 0 : 7);', $path],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
        );

        $this->assertIsResource($process);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);
        $lock->release();

        $this->assertSame(7, $status);
    }
}
