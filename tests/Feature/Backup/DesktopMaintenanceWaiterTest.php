<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopMaintenanceWaiter;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopMaintenanceWaiterTest extends TestCase
{
    #[Test]
    public function it_rejects_an_invalid_timeout_before_acquiring_a_lease(): void
    {
        $directory = sys_get_temp_dir() . '/hellen-waiter-' . bin2hex(random_bytes(8));
        mkdir($directory);
        config()->set('backup.maintenance_timeout_ms', -1);
        $request = (new DesktopMaintenanceGate($directory))->request();

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Invalid timeout.');
            (new DesktopMaintenanceWaiter())->acquire($request, 'Invalid timeout.', 'Busy.');
        } finally {
            $request->release();
            (new Filesystem())->remove($directory);
        }
    }

    #[Test]
    public function it_reports_busy_when_the_timeout_expires_and_acquires_after_work_drains(): void
    {
        $directory = sys_get_temp_dir() . '/hellen-waiter-' . bin2hex(random_bytes(8));
        mkdir($directory);
        config()->set('backup.maintenance_timeout_ms', 0);
        $gate = new DesktopMaintenanceGate($directory);
        $active = $gate->enter();
        $request = $gate->request();

        try {
            $waiter = new DesktopMaintenanceWaiter();

            try {
                $waiter->acquire($request, 'Invalid timeout.', 'Busy.');
                $this->fail('A held shared lease must block exclusive access.');
            } catch (RuntimeException $exception) {
                $this->assertSame('Busy.', $exception->getMessage());
            }

            $active->release();
            $waiter->acquire($request, 'Invalid timeout.', 'Busy.');
            $this->assertTrue($request->lease()->isExclusive());
        } finally {
            $active->release();
            $request->release();
            (new Filesystem())->remove($directory);
        }
    }
}
