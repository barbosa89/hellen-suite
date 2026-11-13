<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\DesktopRestore;
use Illuminate\Support\Facades\DB;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;
use ZipArchive;

class DesktopRestoreTest extends TestCase
{
    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-desktop-restore-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/public/hotels');
        mkdir($this->directory . '/private');
        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');
        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory);
        config()->set('nativephp-internal.running', true);
        config()->set('nativephp.version', '1.2.3');
        config()->set('filesystems.default', 'public');
        config()->set('filesystems.disks.public', ['driver' => 'local', 'root' => $this->directory . '/public']);
        config()->set('database.connections.desktop_restore', ['driver' => 'sqlite', 'database' => $this->directory . '/active.sqlite', 'prefix' => '']);
        touch($this->directory . '/active.sqlite');
        DB::setDefaultConnection('desktop_restore');
        DB::statement('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        DB::statement("INSERT INTO migrations VALUES ('first', 1)");
        DB::statement('CREATE TABLE hotels (image TEXT, name TEXT)');
        DB::statement("INSERT INTO hotels VALUES ('hotels/photo.jpg', 'before')");
        DB::statement('CREATE TABLE payments (support_path TEXT)');
        DB::statement('CREATE TABLE hotel_compliance_profiles (credentials TEXT)');
        file_put_contents($this->directory . '/public/hotels/photo.jpg', 'original photo');
    }

    protected function tearDown(): void
    {
        DB::disconnect('desktop_restore');

        if ($this->previousUserDataPath === false) {
            putenv('NATIVEPHP_USER_DATA_PATH');
        } else {
            putenv('NATIVEPHP_USER_DATA_PATH=' . $this->previousUserDataPath);
        }

        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function it_previews_a_selected_archive_without_touching_the_dataset(): void
    {
        $archive = $this->archive('before');

        $preview = app(DesktopRestore::class)->preview($archive, '1.2.3', $this->directory . '/private');

        $this->assertSame('1.2.3', $preview['application_version']);
        $this->assertSame(2, $preview['files']);
        $this->assertSame('before', DB::table('hotels')->value('name'));
        $this->assertSame('original photo', file_get_contents($this->directory . '/public/hotels/photo.jpg'));
        $this->assertSame([], glob($this->directory . '/private/backup-*', GLOB_ONLYDIR));
    }

    #[Test]
    public function it_restores_the_dataset_and_retains_the_previous_database(): void
    {
        $archive = $this->archive('restored');
        DB::table('hotels')->update(['name' => 'changed']);

        app(DesktopRestore::class)->restore($archive, '1.2.3', $this->directory . '/private', $this->directory . '/restore.journal');

        $restored = new PDO('sqlite:' . $this->directory . '/active.sqlite');
        $this->assertSame('restored', $restored->query('SELECT name FROM hotels')->fetchColumn());
        $previous = glob($this->directory . '/.active.sqlite.restore-*-previous');
        $this->assertCount(1, $previous);
        $retained = new PDO('sqlite:' . $previous[0]);
        $this->assertSame('changed', $retained->query('SELECT name FROM hotels')->fetchColumn());
        $restored = null;
        $retained = null;
    }

    #[Test]
    public function it_rejects_an_archive_with_an_incompatible_version(): void
    {
        $archive = $this->archive('before');

        $this->expectException(RuntimeException::class);
        app(DesktopRestore::class)->preview($archive, '9.9.9', $this->directory . '/private');
    }

    #[Test]
    public function it_leaves_the_dataset_untouched_when_the_archive_is_invalid(): void
    {
        $archive = $this->directory . '/invalid.zip';
        file_put_contents($archive, 'not an archive');

        $this->expectException(RuntimeException::class);
        app(DesktopRestore::class)->preview($archive, '1.2.3', $this->directory . '/private');
    }

    private function archive(string $name): string
    {
        $database = $this->directory . '/source.sqlite';
        @unlink($database);
        $source = new PDO('sqlite:' . $database);
        $source->exec('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        $source->exec("INSERT INTO migrations VALUES ('first', 1)");
        $source->exec('CREATE TABLE hotels (image TEXT, name TEXT)');
        $source->exec("INSERT INTO hotels VALUES ('hotels/photo.jpg', '" . $name . "')");
        $source->exec('CREATE TABLE payments (support_path TEXT)');
        $source->exec('CREATE TABLE hotel_compliance_profiles (credentials TEXT)');
        $source = null;

        $contents = file_get_contents($database);
        $photo = 'original photo';
        $manifest = [
            'format_version' => 1,
            'created_at' => '2026-11-12T15:00:00Z',
            'application_version' => '1.2.3',
            'migrations' => [['migration' => 'first', 'batch' => 1]],
            'files' => [
                'database.sqlite' => ['size' => strlen($contents), 'sha256' => hash('sha256', $contents)],
                'hotels/photo.jpg' => ['size' => strlen($photo), 'sha256' => hash('sha256', $photo)],
            ],
            'attachment_disks' => ['hotels/photo.jpg' => 'public'],
        ];

        $path = $this->directory . '/backup.zip';
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::EXCL));
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
        $zip->addFromString('database.sqlite', $contents);
        $zip->addFromString('hotels/photo.jpg', $photo);
        $zip->close();

        return $path;
    }
}
