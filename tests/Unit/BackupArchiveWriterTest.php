<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupArchiveWriter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class BackupArchiveWriterTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-archive-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        file_put_contents($this->directory . '/database.sqlite', 'database');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_creates_a_verified_archive_with_the_manifest_and_snapshot(): void
    {
        $manifest = $this->manifest();
        $target = $this->directory . '/backup.zip';

        (new BackupArchiveWriter())->create($this->directory, $target, $manifest);

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($target));
        $this->assertSame('database', $zip->getFromName('database.sqlite'));
        $this->assertSame($manifest, json_decode($zip->getFromName('manifest.json'), true, flags: JSON_THROW_ON_ERROR));
        $zip->close();
    }

    #[Test]
    public function it_rejects_checksum_mismatch_and_removes_incomplete_archive(): void
    {
        $manifest = $this->manifest();
        $manifest['files']['database.sqlite']['sha256'] = hash('sha256', 'different');
        $target = $this->directory . '/backup.zip';

        try {
            (new BackupArchiveWriter())->create($this->directory, $target, $manifest);
            $this->fail('A mismatching checksum must fail.');
        } catch (RuntimeException) {
            $this->assertFileDoesNotExist($target);
        }
    }

    #[Test]
    public function it_never_overwrites_an_existing_archive(): void
    {
        $target = $this->directory . '/backup.zip';
        file_put_contents($target, 'existing');

        try {
            (new BackupArchiveWriter())->create($this->directory, $target, $this->manifest());
            $this->fail('An existing file must not be overwritten.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($target));
        }
    }

    private function manifest(): array
    {
        return [
            'format_version' => 1,
            'created_at' => '2026-11-12T15:00:00Z',
            'application_version' => '1.2.3',
            'migrations' => [],
            'files' => ['database.sqlite' => ['size' => 8, 'sha256' => hash('sha256', 'database')]],
        ];
    }
}
