<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopMaintenanceLock;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopMaintenanceLockTest extends TestCase
{
    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-desktop-lock-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');
        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory);
    }

    protected function tearDown(): void
    {
        if ($this->previousUserDataPath === false) {
            putenv('NATIVEPHP_USER_DATA_PATH');
        } else {
            putenv('NATIVEPHP_USER_DATA_PATH=' . $this->previousUserDataPath);
        }

        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function shared_and_exclusive_operations_use_the_same_lock_file(): void
    {
        config()->set('nativephp-internal.running', true);
        $locks = app(DesktopMaintenanceLock::class);
        $shared = $locks->shared();

        try {
            $locks->exclusive();
            $this->fail('An active request must block exclusive maintenance.');
        } catch (RuntimeException) {
            $this->assertFileExists($this->directory . '/backup-maintenance.lock');
        }

        $shared->release();
        $exclusive = $locks->exclusive();
        $this->assertTrue($exclusive->isExclusive());
        $exclusive->release();
    }

    #[Test]
    public function it_rejects_maintenance_locks_outside_the_desktop_environment(): void
    {
        config()->set('nativephp-internal.running', false);

        $this->expectException(RuntimeException::class);
        app(DesktopMaintenanceLock::class)->exclusive();
    }

    #[Test]
    public function it_rejects_an_unavailable_native_user_data_directory(): void
    {
        config()->set('nativephp-internal.running', true);
        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory . '/missing');

        $this->expectException(RuntimeException::class);
        app(DesktopMaintenanceLock::class)->exclusive();
    }

    #[Test]
    public function shared_requests_cannot_enter_while_maintenance_is_draining(): void
    {
        config()->set('nativephp-internal.running', true);
        $request = (new DesktopMaintenanceGate($this->directory))->request();

        try {
            app(DesktopMaintenanceLock::class)->shared();
            $this->fail('New requests must not bypass maintenance intent.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        } finally {
            $request->release();
        }
    }
}
