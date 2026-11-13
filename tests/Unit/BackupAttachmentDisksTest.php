<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupAttachmentDisks;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BackupAttachmentDisksTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-disk-map-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/default');
        mkdir($this->directory . '/public/hotels');
        mkdir($this->directory . '/public/payments');
        mkdir($this->directory . '/default/hotels');
        file_put_contents($this->directory . '/public/hotels/public.jpg', 'public');
        file_put_contents($this->directory . '/default/hotels/default.jpg', 'default');
        file_put_contents($this->directory . '/public/payments/receipt.pdf', 'receipt');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_records_logical_disk_roles_without_host_paths(): void
    {
        $mapping = (new BackupAttachmentDisks())->fromResolved([
            'hotels' => [
                'hotels/default.jpg' => $this->directory . '/default/hotels/default.jpg',
                'hotels/public.jpg' => $this->directory . '/public/hotels/public.jpg',
            ],
            'payments' => ['payments/receipt.pdf' => $this->directory . '/public/payments/receipt.pdf'],
        ], [
            'hotels' => [$this->directory . '/default', $this->directory . '/public'],
            'payments' => [$this->directory . '/public'],
        ]);

        $this->assertSame([
            'hotels/default.jpg' => 'default',
            'hotels/public.jpg' => 'public',
            'payments/receipt.pdf' => 'public',
        ], $mapping);
    }

    #[Test]
    public function it_rejects_sources_that_do_not_belong_to_the_expected_roots(): void
    {
        $this->expectException(RuntimeException::class);

        (new BackupAttachmentDisks())->fromResolved([
            'hotels' => ['hotels/public.jpg' => $this->directory . '/default/hotels/default.jpg'],
            'payments' => [],
        ], [
            'hotels' => [$this->directory . '/default', $this->directory . '/public'],
            'payments' => [$this->directory . '/public'],
        ]);
    }
}
