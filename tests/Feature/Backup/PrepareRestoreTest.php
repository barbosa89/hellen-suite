<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\PrepareRestore;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;
use ZipArchive;

class PrepareRestoreTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-restore-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/stage');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_prepares_a_compatible_backup_without_modifying_the_current_database(): void
    {
        $database = $this->database();
        $archive = $this->archive(true);

        $manifest = app(PrepareRestore::class)->prepare($archive, $this->directory . '/stage', $database, '1.2.3', 10, 1000000);

        $this->assertSame('1.2.3', $manifest['application_version']);
        $this->assertFileExists($this->directory . '/stage/selected-backup.zip');
        $this->assertSame('image', file_get_contents($this->directory . '/stage/dataset/hotels/photo.jpg'));
        $this->assertSame(1, (int) $database->query('SELECT COUNT(*) FROM hotels')->fetchColumn());
    }

    #[Test]
    public function it_rejects_and_cleans_a_snapshot_referencing_a_missing_attachment(): void
    {
        $database = $this->database();
        $archive = $this->archive(false);

        try {
            app(PrepareRestore::class)->prepare($archive, $this->directory . '/stage', $database, '1.2.3', 10, 1000000);
            $this->fail('A referenced missing attachment must be rejected.');
        } catch (RuntimeException) {
            $this->assertDirectoryDoesNotExist($this->directory . '/stage/dataset');
            $this->assertFileDoesNotExist($this->directory . '/stage/selected-backup.zip');
            $this->assertSame(1, (int) $database->query('SELECT COUNT(*) FROM hotels')->fetchColumn());
        }
    }

    #[Test]
    public function it_rejects_backups_with_an_invalid_attachment_disk_role(): void
    {
        $database = $this->database();
        $archive = $this->archive(true, 'unexpected');

        try {
            app(PrepareRestore::class)->prepare($archive, $this->directory . '/stage', $database, '1.2.3', 10, 1000000);
            $this->fail('Invalid attachment disk roles must be rejected.');
        } catch (RuntimeException) {
            $this->assertDirectoryDoesNotExist($this->directory . '/stage/dataset');
        }
    }

    private function database(): PDO
    {
        $database = new PDO('sqlite:' . $this->directory . '/live.sqlite', options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $database->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        $database->exec("INSERT INTO migrations VALUES ('first', 1)");
        $database->exec('CREATE TABLE hotel_compliance_profiles (credentials TEXT)');
        $database->exec('CREATE TABLE hotels (image TEXT)');
        $database->exec("INSERT INTO hotels VALUES ('hotels/photo.jpg')");
        $database->exec('CREATE TABLE payments (support_path TEXT)');

        return $database;
    }

    private function archive(bool $includePhoto, string $disk = 'public'): string
    {
        $databasePath = $this->directory . '/live.sqlite';
        $databaseContents = file_get_contents($databasePath);
        $files = ['database.sqlite' => ['size' => strlen($databaseContents), 'sha256' => hash('sha256', $databaseContents)]];

        if ($includePhoto) {
            $files['hotels/photo.jpg'] = ['size' => 5, 'sha256' => hash('sha256', 'image')];
        }

        $manifest = [
            'format_version' => 1,
            'created_at' => (new DateTimeImmutable('2026-11-12T15:00:00Z'))->format('Y-m-d\TH:i:s\Z'),
            'application_version' => '1.2.3',
            'migrations' => [['migration' => 'first', 'batch' => 1]],
            'files' => $files,
            'attachment_disks' => $includePhoto ? ['hotels/photo.jpg' => $disk] : [],
        ];
        $path = $this->directory . '/backup.zip';
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::EXCL));
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
        $zip->addFromString('database.sqlite', $databaseContents);

        if ($includePhoto) {
            $zip->addFromString('hotels/photo.jpg', 'image');
        }

        $zip->close();

        return $path;
    }
}
