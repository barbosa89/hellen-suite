<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupAttachmentStager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BackupAttachmentStagerTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-staging-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/stage');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_stages_files_and_returns_verified_sizes_and_checksums(): void
    {
        $source = $this->directory . '/source';
        file_put_contents($source, 'hotel image');

        $manifest = (new BackupAttachmentStager())->stage(
            ['hotels' => ['hotels/image.jpg' => $source], 'payments' => []],
            $this->directory . '/stage',
        );

        $this->assertSame('hotel image', file_get_contents($this->directory . '/stage/hotels/image.jpg'));
        $this->assertSame([
            'hotels/image.jpg' => ['size' => 11, 'sha256' => hash('sha256', 'hotel image')],
        ], $manifest);
    }

    #[Test]
    public function it_rejects_missing_sources_without_leaving_staged_files(): void
    {
        $source = $this->directory . '/source';
        file_put_contents($source, 'image');

        try {
            (new BackupAttachmentStager())->stage(
                ['hotels' => ['hotels/image.jpg' => $source], 'payments' => ['payments/missing.pdf' => $this->directory . '/missing']],
                $this->directory . '/stage',
            );
            $this->fail('Missing files must fail staging.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($this->directory . '/stage/hotels/image.jpg');
        }
    }

    #[Test]
    public function it_refuses_to_overwrite_a_staged_file(): void
    {
        $source = $this->directory . '/source';
        file_put_contents($source, 'new');
        mkdir($this->directory . '/stage/hotels');
        $target = $this->directory . '/stage/hotels/image.jpg';
        file_put_contents($target, 'existing');

        try {
            (new BackupAttachmentStager())->stage(
                ['hotels' => ['hotels/image.jpg' => $source], 'payments' => []],
                $this->directory . '/stage',
            );
            $this->fail('Existing staged files must not be overwritten.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($target));
        }
    }
}
