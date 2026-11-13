<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupAttachmentDisks;
use App\Support\Backup\BackupMaintenanceLock;
use App\Support\Backup\RestoreFailure;
use App\Support\Backup\RestoreReplacementStager;
use App\Support\Backup\RestoreResourcePlan;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class RestoreReplacementStagerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-restore-stage-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/public/hotels');
        mkdir($this->directory . '/dataset');
        mkdir($this->directory . '/dataset/hotels');
        mkdir($this->directory . '/dataset/payments');
        file_put_contents($this->directory . '/active.sqlite', 'current database');
        file_put_contents($this->directory . '/public/hotels/old.jpg', 'old');
        file_put_contents($this->directory . '/dataset/database.sqlite', 'backup database');
        file_put_contents($this->directory . '/dataset/hotels/photo.jpg', 'photo');
        file_put_contents($this->directory . '/dataset/payments/receipt.pdf', 'receipt');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_preserves_both_staging_and_cleanup_errors(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $manifest = $this->manifest();
        $plan = $this->plan($manifest, $lock);
        file_put_contents($this->directory . '/dataset/hotels/photo.jpg', 'changed');
        $cleanupFailure = new RuntimeException('Cleanup failed.');
        $filesystem = new class($cleanupFailure) extends Filesystem
        {
            public function __construct(private readonly RuntimeException $failure) {}

            public function remove($files, bool $recursive = false): void
            {
                throw $this->failure;
            }
        };

        try {
            (new RestoreReplacementStager($filesystem))->stage($this->directory . '/dataset', $plan, $manifest, $lock);
            $this->fail('Failed staging and cleanup must be reported.');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(RestoreFailure::class, $exception);
            $this->assertSame('Restore replacement verification failed.', $exception->getPrevious()?->getMessage());
            $this->assertSame($cleanupFailure, $exception->secondary);
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_stages_verified_replacements_without_changing_live_data(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $manifest = $this->manifest();
        $plan = $this->plan($manifest, $lock);

        (new RestoreReplacementStager(new Filesystem()))->stage($this->directory . '/dataset', $plan, $manifest, $lock);

        $this->assertSame('current database', file_get_contents($this->directory . '/active.sqlite'));
        $this->assertSame('old', file_get_contents($this->directory . '/public/hotels/old.jpg'));
        $this->assertSame('backup database', file_get_contents($plan['resources']['database']['replacement']));
        $this->assertSame('photo', file_get_contents($plan['resources']['hotels-public']['replacement'] . '/photo.jpg'));
        $this->assertSame('receipt', file_get_contents($plan['resources']['payments-public']['replacement'] . '/receipt.pdf'));
        $this->assertFileDoesNotExist($plan['resources']['hotels-public']['replacement'] . '/old.jpg');
        $lock->release();
    }

    #[Test]
    public function it_removes_new_replacements_when_an_attachment_fails_verification(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $manifest = $this->manifest();
        $plan = $this->plan($manifest, $lock);
        file_put_contents($this->directory . '/dataset/hotels/photo.jpg', 'changed');

        try {
            (new RestoreReplacementStager(new Filesystem()))->stage($this->directory . '/dataset', $plan, $manifest, $lock);
            $this->fail('A changed file must fail staging.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($plan['resources']['database']['replacement']);
            $this->assertDirectoryDoesNotExist($plan['resources']['hotels-public']['replacement']);
            $this->assertSame('current database', file_get_contents($this->directory . '/active.sqlite'));
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_rejects_a_plan_that_routes_an_attachment_to_the_wrong_disk(): void
    {
        mkdir($this->directory . '/default');
        $manifest = $this->manifest();
        $manifest['attachment_disks']['hotels/photo.jpg'] = 'default';
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $plan = (new RestoreResourcePlan(new BackupAttachmentDisks()))->build(
            $this->directory . '/active.sqlite',
            ['hotels' => [$this->directory . '/default', $this->directory . '/public'], 'payments' => [$this->directory . '/public']],
            $manifest,
            'transaction1',
            $lock,
        );
        $plan['files']['hotels-default'] = [];
        $plan['files']['hotels-public'] = ['hotels/photo.jpg'];

        try {
            (new RestoreReplacementStager(new Filesystem()))->stage($this->directory . '/dataset', $plan, $manifest, $lock);
            $this->fail('Attachments must not be staged on the wrong disk.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($plan['resources']['database']['replacement']);
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function it_rejects_a_plan_that_omits_an_empty_attachment_tree(): void
    {
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/maintenance.lock');
        $manifest = $this->manifest();
        unset($manifest['files']['payments/receipt.pdf'], $manifest['attachment_disks']['payments/receipt.pdf']);
        $plan = $this->plan($manifest, $lock);
        unset($plan['resources']['payments-public'], $plan['files']['payments-public']);

        try {
            (new RestoreReplacementStager(new Filesystem()))->stage($this->directory . '/dataset', $plan, $manifest, $lock);
            $this->fail('An empty tree must still be replaced.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($plan['resources']['database']['replacement']);
        } finally {
            $lock->release();
        }
    }

    private function plan(array $manifest, BackupMaintenanceLock $lock): array
    {
        $root = $this->directory . '/public';

        return (new RestoreResourcePlan(new BackupAttachmentDisks()))->build(
            $this->directory . '/active.sqlite',
            ['hotels' => [$root], 'payments' => [$root]],
            $manifest,
            'transaction1',
            $lock,
        );
    }

    private function manifest(): array
    {
        return [
            'files' => [
                'database.sqlite' => ['size' => 15, 'sha256' => hash('sha256', 'backup database')],
                'hotels/photo.jpg' => ['size' => 5, 'sha256' => hash('sha256', 'photo')],
                'payments/receipt.pdf' => ['size' => 7, 'sha256' => hash('sha256', 'receipt')],
            ],
            'attachment_disks' => ['hotels/photo.jpg' => 'public', 'payments/receipt.pdf' => 'public'],
        ];
    }
}
