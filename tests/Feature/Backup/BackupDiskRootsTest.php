<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupDiskRoots;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BackupDiskRootsTest extends TestCase
{
    #[Test]
    public function it_resolves_configured_local_disks_for_hotel_images_and_payment_supports(): void
    {
        config()->set('filesystems.default', 'images');
        config()->set('filesystems.disks.images', ['driver' => 'local', 'root' => '/images']);
        config()->set('filesystems.disks.public', ['driver' => 'local', 'root' => '/public']);

        $this->assertSame([
            'hotels' => ['/images', '/public'],
            'payments' => ['/public'],
        ], app(BackupDiskRoots::class)->resolve());
    }

    #[Test]
    public function it_does_not_duplicate_the_public_root_when_it_is_the_default(): void
    {
        config()->set('filesystems.default', 'public');
        config()->set('filesystems.disks.public', ['driver' => 'local', 'root' => '/public']);

        $this->assertSame([
            'hotels' => ['/public'],
            'payments' => ['/public'],
        ], app(BackupDiskRoots::class)->resolve());
    }

    #[Test]
    public function it_rejects_non_local_disks(): void
    {
        config()->set('filesystems.default', 'remote');
        config()->set('filesystems.disks.remote', ['driver' => 's3']);

        $this->expectException(RuntimeException::class);

        app(BackupDiskRoots::class)->resolve();
    }

    #[Test]
    public function it_rejects_a_local_disk_without_a_root(): void
    {
        config()->set('filesystems.default', 'images');
        config()->set('filesystems.disks.images', ['driver' => 'local']);

        $this->expectException(RuntimeException::class);

        app(BackupDiskRoots::class)->resolve();
    }
}
