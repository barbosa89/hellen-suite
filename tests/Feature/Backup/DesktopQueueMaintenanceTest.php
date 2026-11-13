<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopProcessLease;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Queue\Events\JobAttempted;
use Illuminate\Queue\Events\JobPopping;
use Illuminate\Queue\Events\Looping;
use Illuminate\Queue\Events\WorkerIdle;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopQueueMaintenanceTest extends TestCase
{
    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-queue-lock-' . bin2hex(random_bytes(8));
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
    public function the_worker_pauses_before_reservation_during_exclusive_maintenance(): void
    {
        config()->set('nativephp-internal.running', true);
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');

        $this->assertFalse(Event::until(new Looping('database', 'default')));
        $lock->release();
        $this->assertNull(Event::until(new Looping('database', 'default')));
        Event::dispatch(new WorkerIdle('database', 'default', new WorkerOptions()));
    }

    #[Test]
    public function the_worker_holds_the_shared_lock_until_the_attempt_finishes(): void
    {
        config()->set('nativephp-internal.running', true);
        $this->assertNull(Event::until(new Looping('database', 'default')));

        try {
            BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
            $this->fail('The worker must hold a shared lock while processing.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        Event::dispatch(new JobAttempted('database', null));
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $lock->release();
    }

    #[Test]
    public function an_idle_worker_releases_the_lock_before_sleeping(): void
    {
        config()->set('nativephp-internal.running', true);
        $this->assertNull(Event::until(new Looping('database', 'default')));
        Event::dispatch(new WorkerIdle('database', 'default', new WorkerOptions()));

        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $lock->release();
    }

    #[Test]
    public function a_one_shot_worker_cannot_reserve_during_maintenance(): void
    {
        config()->set('nativephp-internal.running', true);
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');

        try {
            Event::dispatch(new JobPopping('database', 'default'));
            $this->fail('A worker must not reserve a job during maintenance.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function a_one_shot_worker_releases_the_lock_when_its_command_finishes(): void
    {
        config()->set('nativephp-internal.running', true);
        Event::dispatch(new JobPopping('database', 'default'));

        try {
            BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
            $this->fail('A worker must hold a lock while attempting reservation.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        Event::dispatch(new CommandFinished('queue:work', new ArrayInput([]), new NullOutput(), 0));

        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $this->assertTrue($lock->isExclusive());
        $lock->release();
    }

    #[Test]
    public function a_long_lived_worker_exits_when_maintenance_is_requested(): void
    {
        config()->set('nativephp-internal.running', true);
        $gate = new DesktopMaintenanceGate($this->directory);
        $lease = app(DesktopProcessLease::class);
        $lease->hold($gate->enter());
        $request = $gate->request();

        try {
            Event::until(new Looping('database', 'default'));
            $this->fail('A long-lived worker must stop to let maintenance drain.');
        } catch (RuntimeException) {
            $this->assertFalse($request->tryAcquire());
        } finally {
            $lease->release();
            $request->release();
        }
    }
}
