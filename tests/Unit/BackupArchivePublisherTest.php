<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupArchivePublisher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BackupArchivePublisherTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-publish-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        file_put_contents($this->directory . '/source.zip', 'backup contents');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_publishes_the_complete_archive_to_a_new_file(): void
    {
        (new BackupArchivePublisher())->publish($this->directory . '/source.zip', $this->directory . '/backup.zip');

        $this->assertSame('backup contents', file_get_contents($this->directory . '/backup.zip'));
        $this->assertSame([], glob($this->directory . '/.hellen-backup-*.tmp'));
    }

    #[Test]
    public function it_preserves_an_existing_destination_and_removes_temporary_files(): void
    {
        file_put_contents($this->directory . '/backup.zip', 'existing');

        try {
            (new BackupArchivePublisher())->publish($this->directory . '/source.zip', $this->directory . '/backup.zip');
            $this->fail('Existing backups must not be overwritten.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($this->directory . '/backup.zip'));
            $this->assertSame([], glob($this->directory . '/.hellen-backup-*.tmp'));
        }
    }

    #[Test]
    public function it_does_not_create_a_destination_when_the_source_is_missing(): void
    {
        try {
            (new BackupArchivePublisher())->publish($this->directory . '/missing.zip', $this->directory . '/backup.zip');
            $this->fail('An unreadable source must not be published.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($this->directory . '/backup.zip');
        }
    }
}
