<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\RestoreDirectorySync;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RestoreDirectorySyncTest extends TestCase
{
    #[Test]
    public function it_synchronizes_an_existing_directory_when_supported(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('Directory synchronization requires validation on the packaged Windows runtime.');
        }

        (new RestoreDirectorySync())->sync(sys_get_temp_dir());
        $this->assertDirectoryExists(sys_get_temp_dir());
    }

    #[Test]
    public function it_fails_closed_when_the_directory_is_unavailable(): void
    {
        $this->expectException(RuntimeException::class);
        (new RestoreDirectorySync())->sync(sys_get_temp_dir() . '/missing-' . bin2hex(random_bytes(8)));
    }
}
