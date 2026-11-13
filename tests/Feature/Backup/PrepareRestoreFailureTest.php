<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\PrepareRestore;
use App\Support\Backup\RestoreFailure;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class PrepareRestoreFailureTest extends TestCase
{
    #[Test]
    public function it_preserves_the_preparation_and_cleanup_errors(): void
    {
        $directory = sys_get_temp_dir() . '/hellen-prepare-restore-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $cleanupFailure = new RuntimeException('Cleanup failed.');
        $filesystem = new class($cleanupFailure) extends Filesystem
        {
            public function __construct(private readonly RuntimeException $failure) {}

            public function remove($files, bool $recursive = false): void
            {
                throw $this->failure;
            }
        };
        $preparer = app()->makeWith(PrepareRestore::class, ['filesystem' => $filesystem]);

        try {
            $preparer->prepare($directory . '/missing.zip', $directory, new PDO('sqlite::memory:'), '1', 10, 100);
            $this->fail('Missing backup and failed cleanup must be reported.');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(RestoreFailure::class, $exception);
            $this->assertSame('The selected backup is unavailable.', $exception->getPrevious()?->getMessage());
            $this->assertSame($cleanupFailure, $exception->secondary);
        } finally {
            (new Filesystem())->remove($directory);
        }
    }
}
