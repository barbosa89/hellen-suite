<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupArchiveExtractor;
use App\Support\Backup\BackupArchiveInspector;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class BackupArchiveExtractorTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-extract-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/stage');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_extracts_verified_entries_only_to_the_private_staging_directory(): void
    {
        $manifest = $this->manifest();
        $archive = $this->archive($manifest);

        $result = (new BackupArchiveExtractor(new BackupArchiveInspector()))->extract($archive, $this->directory . '/stage', 10, 4096);

        $this->assertSame($manifest, $result);
        $this->assertSame('database', file_get_contents($this->directory . '/stage/database.sqlite'));
        $this->assertSame('photo', file_get_contents($this->directory . '/stage/hotels/photo.jpg'));
    }

    #[Test]
    public function it_preserves_existing_files_and_cleans_up_new_files_on_failure(): void
    {
        $archive = $this->archive($this->manifest());
        mkdir($this->directory . '/stage/hotels');
        file_put_contents($this->directory . '/stage/hotels/photo.jpg', 'existing');

        try {
            (new BackupArchiveExtractor(new BackupArchiveInspector()))->extract($archive, $this->directory . '/stage', 10, 4096);
            $this->fail('Existing files must not be overwritten.');
        } catch (RuntimeException) {
            $this->assertSame('existing', file_get_contents($this->directory . '/stage/hotels/photo.jpg'));
            $this->assertFileDoesNotExist($this->directory . '/stage/database.sqlite');
        }
    }

    private function archive(array $manifest): string
    {
        $path = $this->directory . '/backup.zip';
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::EXCL));
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
        $zip->addFromString('database.sqlite', 'database');
        $zip->addFromString('hotels/photo.jpg', 'photo');
        $zip->close();

        return $path;
    }

    private function manifest(): array
    {
        return [
            'format_version' => 1,
            'created_at' => '2026-11-12T15:00:00Z',
            'application_version' => '1.2.3',
            'migrations' => [],
            'files' => [
                'database.sqlite' => ['size' => 8, 'sha256' => hash('sha256', 'database')],
                'hotels/photo.jpg' => ['size' => 5, 'sha256' => hash('sha256', 'photo')],
            ],
        ];
    }
}
