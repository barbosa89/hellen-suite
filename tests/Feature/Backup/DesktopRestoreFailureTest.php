<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\ActiveSqliteDatabase;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopRestore;
use App\Support\Backup\RestoreFailure;
use Illuminate\Database\DatabaseManager;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopRestoreFailureTest extends TestCase
{
    #[Test]
    public function maintenance_is_released_when_reconnecting_after_a_failure_also_fails(): void
    {
        $directory = sys_get_temp_dir() . '/hellen-restore-failure-' . bin2hex(random_bytes(8));
        mkdir($directory);
        $previous = getenv('NATIVEPHP_USER_DATA_PATH');
        putenv('NATIVEPHP_USER_DATA_PATH=' . $directory);
        config()->set('nativephp-internal.running', true);

        $database = Mockery::mock(DatabaseManager::class);
        $database->shouldReceive('connection')->andThrow(new RuntimeException('Initial failure.'));
        $database->shouldReceive('purge')->andThrow(new RuntimeException('Reconnect failure.'));
        $restore = app()->makeWith(DesktopRestore::class, ['database' => new ActiveSqliteDatabase($database)]);

        try {
            try {
                $restore->restore($directory . '/missing.zip', '1', $directory, $directory . '/restore.journal');
                $this->fail('Restore must report the failure.');
            } catch (RestoreFailure $exception) {
                $this->assertSame('Initial failure.', $exception->getPrevious()?->getMessage());
                $this->assertSame('Reconnect failure.', $exception->secondary->getMessage());
                $this->assertFalse((new DesktopMaintenanceGate($directory))->isRequested());
                $this->assertSame([], glob($directory . '/backup-*', GLOB_ONLYDIR));
            }
        } finally {
            $previous === false ? putenv('NATIVEPHP_USER_DATA_PATH') : putenv('NATIVEPHP_USER_DATA_PATH=' . $previous);
            (new Filesystem())->remove($directory);
        }
    }
}
