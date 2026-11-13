<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupArchiveInspector;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class BackupArchiveInspectorTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-inspect-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_reads_a_validated_manifest_without_extracting_files(): void
    {
        $manifest = $this->manifest();
        $archive = $this->archive($manifest);

        $this->assertSame($manifest, (new BackupArchiveInspector())->inspect($archive, 10, 4096));
        $this->assertFileDoesNotExist($this->directory . '/database.sqlite');
    }

    #[Test]
    public function it_rejects_unexpected_archive_entries(): void
    {
        $archive = $this->archive($this->manifest(), ['../secret' => 'secret']);

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 10, 4096);
    }

    #[Test]
    public function it_rejects_mismatched_checksums(): void
    {
        $manifest = $this->manifest();
        $manifest['files']['database.sqlite']['sha256'] = hash('sha256', 'different');
        $archive = $this->archive($manifest);

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 10, 4096);
    }

    #[Test]
    public function it_enforces_decompressed_size_limits(): void
    {
        $archive = $this->archive($this->manifest());

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 10, 1);
    }

    #[Test]
    public function it_rejects_archives_exceeding_the_entry_count_limit(): void
    {
        $archive = $this->archive($this->manifest());

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 1, 4096);
    }

    #[Test]
    public function it_rejects_symbolic_links_inside_the_archive(): void
    {
        $manifest = $this->manifest();
        $manifest['files']['hotels/link'] = ['size' => 6, 'sha256' => hash('sha256', 'target')];
        $archive = $this->archive($manifest, ['hotels/link' => 'target']);
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($archive));
        $this->assertTrue($zip->setExternalAttributesName('hotels/link', ZipArchive::OPSYS_UNIX, 0120777 << 16));
        $zip->close();

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 10, 4096);
    }

    #[Test]
    public function it_rejects_malformed_manifest_data(): void
    {
        $archive = $this->archive($this->manifest());
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($archive));
        $zip->addFromString('manifest.json', '{invalid json');
        $zip->close();

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 10, 4096);
    }

    #[Test]
    public function it_rejects_an_invalid_manifest_creation_date(): void
    {
        $manifest = $this->manifest();
        $manifest['created_at'] = 'not a date';
        $archive = $this->archive($manifest);

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 10, 4096);
    }

    #[Test]
    public function it_rejects_invalid_migration_metadata(): void
    {
        $manifest = $this->manifest();
        $manifest['migrations'] = [['migration' => 'first', 'batch' => -1]];
        $archive = $this->archive($manifest);

        $this->expectException(RuntimeException::class);
        (new BackupArchiveInspector())->inspect($archive, 10, 4096);
    }

    private function archive(array $manifest, array $extra = []): string
    {
        $path = $this->directory . '/backup.zip';
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::EXCL));
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
        $zip->addFromString('database.sqlite', 'database');

        foreach ($extra as $name => $contents) {
            $zip->addFromString($name, $contents);
        }

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
            'files' => ['database.sqlite' => ['size' => 8, 'sha256' => hash('sha256', 'database')]],
        ];
    }
}
