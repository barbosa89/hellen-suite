<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\PendingRestoreGuard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class PendingRestoreGuardTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-restore-guard-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_allows_startup_when_no_restore_journal_exists(): void
    {
        (new PendingRestoreGuard())->assertRecovered($this->directory);
        $this->assertDirectoryExists($this->directory);
    }

    #[Test]
    public function it_blocks_startup_when_a_restore_journal_exists_even_if_it_is_invalid(): void
    {
        file_put_contents($this->directory . '/restore.journal', 'incomplete');

        $this->expectException(RuntimeException::class);
        (new PendingRestoreGuard())->assertRecovered($this->directory);
    }

    #[Test]
    public function it_blocks_startup_when_the_user_data_directory_is_unavailable(): void
    {
        $this->expectException(RuntimeException::class);
        (new PendingRestoreGuard())->assertRecovered($this->directory . '/missing');
    }
}
