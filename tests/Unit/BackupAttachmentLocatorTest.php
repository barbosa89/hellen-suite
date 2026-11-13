<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupAttachmentLocator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BackupAttachmentLocatorTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir() . '/hellen-attachments-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/other');
        mkdir($this->directory . '/public/hotels');
        mkdir($this->directory . '/public/payments');
        mkdir($this->directory . '/other/hotels');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);

        parent::tearDown();
    }

    #[Test]
    public function it_resolves_references_across_configured_local_roots(): void
    {
        file_put_contents($this->directory . '/other/hotels/photo.jpg', 'photo');
        file_put_contents($this->directory . '/public/payments/receipt.pdf', 'receipt');

        $this->assertSame([
            'hotels' => ['hotels/photo.jpg' => $this->directory . '/other/hotels/photo.jpg'],
            'payments' => ['payments/receipt.pdf' => $this->directory . '/public/payments/receipt.pdf'],
        ], (new BackupAttachmentLocator())->locate(
            ['hotels' => ['hotels/photo.jpg'], 'payments' => ['payments/receipt.pdf']],
            ['hotels' => [$this->directory . '/public', $this->directory . '/other'], 'payments' => [$this->directory . '/public']],
        ));
    }

    #[Test]
    public function it_rejects_missing_files(): void
    {
        $this->expectException(RuntimeException::class);

        (new BackupAttachmentLocator())->locate(
            ['hotels' => ['hotels/missing.jpg'], 'payments' => []],
            ['hotels' => [$this->directory . '/public'], 'payments' => [$this->directory . '/public']],
        );
    }

    #[Test]
    public function it_rejects_ambiguous_files_on_different_disks(): void
    {
        file_put_contents($this->directory . '/public/hotels/photo.jpg', 'first');
        file_put_contents($this->directory . '/other/hotels/photo.jpg', 'second');

        $this->expectException(RuntimeException::class);

        (new BackupAttachmentLocator())->locate(
            ['hotels' => ['hotels/photo.jpg'], 'payments' => []],
            ['hotels' => [$this->directory . '/public', $this->directory . '/other'], 'payments' => [$this->directory . '/public']],
        );
    }

    #[Test]
    public function it_rejects_links_to_files_outside_the_disk(): void
    {
        file_put_contents($this->directory . '/secret', 'secret');

        if (! @symlink($this->directory . '/secret', $this->directory . '/public/hotels/photo.jpg')) {
            $this->markTestSkipped('Creating symbolic links is not supported.');
        }

        $this->expectException(RuntimeException::class);

        (new BackupAttachmentLocator())->locate(
            ['hotels' => ['hotels/photo.jpg'], 'payments' => []],
            ['hotels' => [$this->directory . '/public'], 'payments' => [$this->directory . '/public']],
        );
    }
}
