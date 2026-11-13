<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupMaintenanceLock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopQueueWorkerTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-queue-worker-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');
        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory);
        config()->set('nativephp-internal.running', true);
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
    public function a_real_one_shot_worker_does_not_reserve_a_job_during_maintenance(): void
    {
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => time() - 1,
            'created_at' => time() - 1,
        ]);
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');

        try {
            $this->artisan('queue:work', ['connection' => 'database', '--once' => true, '--sleep' => 0])->run();
            $this->fail('The worker must stop before reserving a job.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        $job = DB::table('jobs')->first();
        $this->assertSame(0, $job->attempts);
        $this->assertNull($job->reserved_at);
        $lock->release();
    }

    #[Test]
    public function a_one_shot_worker_releases_its_lock_at_the_end_of_the_application_lifecycle(): void
    {
        $this->artisan('queue:work', ['connection' => 'database', '--once' => true, '--sleep' => 0])->assertSuccessful();
        app()->terminate();

        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $this->assertTrue($lock->isExclusive());
        $lock->release();
    }
}
