<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopMaintenanceRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class DesktopMaintenanceGateTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-gate-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function an_unavailable_dataset_lock_is_reported_instead_of_treated_as_contention(): void
    {
        $intent = BackupMaintenanceLock::exclusive($this->directory . '/intent.lock');
        $request = new DesktopMaintenanceRequest($intent, $this->directory . '/missing/dataset.lock');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to open the maintenance lock.');

        $request->tryAcquire();
    }

    #[Test]
    public function a_maintenance_request_blocks_new_entries_while_existing_work_drains(): void
    {
        $gate = new DesktopMaintenanceGate($this->directory);
        $active = $gate->enter();
        $request = $gate->request();
        $this->assertTrue($gate->isRequested());
        $this->assertFalse($request->tryAcquire());

        try {
            $gate->enter();
            $this->fail('New work must not enter while maintenance is requested.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        $active->release();
        $this->assertTrue($request->tryAcquire());
        $this->assertTrue($request->lease()->isExclusive());
        $request->release();
        $this->assertFalse($gate->isRequested());
        $next = $gate->enter();
        $next->release();
    }

    #[Test]
    public function releasing_a_request_without_exclusive_access_allows_new_work(): void
    {
        $gate = new DesktopMaintenanceGate($this->directory);
        $active = $gate->enter();
        $request = $gate->request();
        $this->assertFalse($request->tryAcquire());
        $request->release();
        $another = $gate->enter();
        $another->release();
        $active->release();
    }

    #[Test]
    public function a_maintenance_owner_can_resume_a_shared_process_lease_before_reopening_the_gate(): void
    {
        $gate = new DesktopMaintenanceGate($this->directory);
        $request = $gate->request();
        $this->assertTrue($request->tryAcquire());
        $shared = $request->resumeShared();
        $this->assertFalse($gate->isRequested());
        $other = $gate->request();
        $this->assertFalse($other->tryAcquire());
        $shared->release();
        $this->assertTrue($other->tryAcquire());
        $other->release();
    }
}
