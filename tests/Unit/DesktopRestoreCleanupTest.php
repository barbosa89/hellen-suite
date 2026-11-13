<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupWorkspace;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopProcessLease;
use App\Support\Backup\DesktopRestore;
use App\Support\Backup\RestoreFailure;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Filesystem\Filesystem;

class DesktopRestoreCleanupTest extends TestCase
{
    #[Test]
    public function it_preserves_original_workspace_and_lease_failures_together(): void
    {
        $directory = sys_get_temp_dir() . '/hellen-restore-cleanup-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $workspace = BackupWorkspace::create($directory);
        $path = $workspace->path();
        rmdir($path);

        if (! @symlink($directory, $path)) {
            (new Filesystem())->remove($directory);
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        $request = (new DesktopMaintenanceGate($directory))->request();
        $request->release();
        $restore = (new ReflectionClass(DesktopRestore::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(DesktopRestore::class, 'processLease'))->setValue($restore, new DesktopProcessLease());
        $failure = new RuntimeException('Original restore failure.');

        try {
            try {
                (new \ReflectionMethod(DesktopRestore::class, 'cleanup'))->invoke($restore, $workspace, $request, true, $failure);
                $this->fail('All failures must be reported.');
            } catch (RestoreFailure $exception) {
                $this->assertSame('The maintenance request has been released.', $exception->secondary->getMessage());
                $this->assertInstanceOf(RestoreFailure::class, $exception->getPrevious());
                $workspaceFailure = $exception->getPrevious();
                $this->assertSame($failure, $workspaceFailure->getPrevious());
                $this->assertSame('Unable to remove the backup workspace.', $workspaceFailure->secondary->getMessage());
            }
        } finally {
            (new Filesystem())->remove($directory);
        }
    }

    #[Test]
    public function it_preserves_the_original_and_lease_resumption_errors(): void
    {
        $directory = sys_get_temp_dir() . '/hellen-restore-cleanup-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $request = (new DesktopMaintenanceGate($directory))->request();
        $request->release();
        $restore = (new ReflectionClass(DesktopRestore::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(DesktopRestore::class, 'processLease'))->setValue($restore, new DesktopProcessLease());
        $failure = new RuntimeException('Original restore failure.');

        try {
            try {
                (new \ReflectionMethod(DesktopRestore::class, 'cleanup'))->invoke($restore, null, $request, true, $failure);
                $this->fail('A released request cannot resume a shared lease.');
            } catch (RuntimeException $exception) {
                $this->assertInstanceOf(RestoreFailure::class, $exception);
                $this->assertSame($failure, $exception->getPrevious());
                $this->assertSame('The maintenance request has been released.', $exception->secondary->getMessage());
            }
        } finally {
            (new Filesystem())->remove($directory);
        }
    }

    #[Test]
    public function maintenance_is_released_even_when_the_workspace_cannot_be_removed(): void
    {
        $directory = sys_get_temp_dir() . '/hellen-restore-cleanup-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $workspace = BackupWorkspace::create($directory);
        $path = $workspace->path();
        rmdir($path);

        if (! @symlink($directory, $path)) {
            (new Filesystem())->remove($directory);
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        $gate = new DesktopMaintenanceGate($directory);
        $request = $gate->request();
        $restore = (new ReflectionClass(DesktopRestore::class))->newInstanceWithoutConstructor();
        $failure = new RuntimeException('Original restore failure.');

        try {
            try {
                (new \ReflectionMethod(DesktopRestore::class, 'cleanup'))->invoke($restore, $workspace, $request, false, $failure);
                $this->fail('Workspace cleanup must reject symbolic links.');
            } catch (RuntimeException $exception) {
                $this->assertInstanceOf(RestoreFailure::class, $exception);
                $this->assertSame('The restore workspace could not be removed.', $exception->getMessage());
                $this->assertSame($failure, $exception->getPrevious());
                $this->assertSame('Unable to remove the backup workspace.', $exception->secondary->getMessage());
            }

            $this->assertFalse($gate->isRequested());
        } finally {
            $request->release();
            (new Filesystem())->remove($directory);
        }
    }
}
