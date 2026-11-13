<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupAttachmentDisks;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\RestoreResourcePlan;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreResourcePlanTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-resource-plan-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/default');
        mkdir($this->directory . '/public/hotels');
        file_put_contents($this->directory . '/active.sqlite', 'database');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_plans_separate_disk_trees_and_assigns_attachments_to_their_original_roles(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $plan = (new RestoreResourcePlan(new BackupAttachmentDisks()))->build(
            $this->directory . '/active.sqlite',
            $this->roots(),
            $this->manifest(),
            'abc123',
            $lock,
        );

        $this->assertSame(['database', 'hotels-public', 'hotels-default', 'payments-public'], array_keys($plan['resources']));
        $this->assertTrue($plan['resources']['hotels-public']['existed']);
        $this->assertFalse($plan['resources']['hotels-default']['existed']);
        $this->assertSame(['hotels/public.jpg'], $plan['files']['hotels-public']);
        $this->assertSame(['hotels/default.jpg'], $plan['files']['hotels-default']);
        $this->assertSame(['payments/receipt.pdf'], $plan['files']['payments-public']);
        $this->assertSame(dirname($plan['resources']['database']['target']), dirname($plan['resources']['database']['replacement']));
        $this->assertFileDoesNotExist($plan['resources']['database']['previous']);
        $lock->release();
    }

    #[Test]
    public function it_merges_roles_when_both_disks_have_the_same_root(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $root = $this->directory . '/public';
        $plan = (new RestoreResourcePlan(new BackupAttachmentDisks()))->build(
            $this->directory . '/active.sqlite',
            ['hotels' => [$root], 'payments' => [$root]],
            $this->manifest(),
            'abc123',
            $lock,
        );

        $this->assertArrayNotHasKey('hotels-default', $plan['resources']);
        $this->assertSame(['hotels/default.jpg', 'hotels/public.jpg'], $plan['files']['hotels-public']);
        $lock->release();
    }

    #[Test]
    public function it_refuses_an_existing_recovery_path_without_modifying_it(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $path = $this->directory . '/.active.sqlite.restore-abc123-previous';
        file_put_contents($path, 'existing');

        try {
            (new RestoreResourcePlan(new BackupAttachmentDisks()))->build($this->directory . '/active.sqlite', $this->roots(), $this->manifest(), 'abc123', $lock);
            $this->fail('Existing recovery data must not be reused.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($path));
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_rejects_a_database_inside_an_attachment_tree(): void
    {
        $path = $this->directory . '/public/hotels/live.sqlite';
        file_put_contents($path, 'database');
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');

        try {
            (new RestoreResourcePlan(new BackupAttachmentDisks()))->build($path, $this->roots(), $this->manifest(), 'abc123', $lock);
            $this->fail('Overlapping destinations must be rejected.');
        } catch (RuntimeException) {
            $this->assertSame('database', file_get_contents($path));
        } finally {
            $lock->release();
        }
    }

    private function roots(): array
    {
        return ['hotels' => [$this->directory . '/default', $this->directory . '/public'], 'payments' => [$this->directory . '/public']];
    }

    private function manifest(): array
    {
        return [
            'files' => ['database.sqlite' => [], 'hotels/default.jpg' => [], 'hotels/public.jpg' => [], 'payments/receipt.pdf' => []],
            'attachment_disks' => ['hotels/default.jpg' => 'default', 'hotels/public.jpg' => 'public', 'payments/receipt.pdf' => 'public'],
        ];
    }
}
