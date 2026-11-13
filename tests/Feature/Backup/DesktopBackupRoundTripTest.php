<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Support\Backup\BackupDiskRoots;
use App\Support\Backup\DesktopBackupExport;
use App\Support\Backup\DesktopFileDialog;
use App\Support\Backup\DesktopMaintenanceLock;
use App\Support\Backup\PrepareRestore;
use App\Support\Backup\RestoreInstalledVerifier;
use App\Support\Backup\RestoreInstaller;
use App\Support\Backup\RestoreReplacementStager;
use App\Support\Backup\RestoreResourcePlan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopBackupRoundTripTest extends TestCase
{
    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-round-trip-' . bin2hex(random_bytes(8));

        mkdir($this->directory);
        mkdir($this->directory . '/public');
        mkdir($this->directory . '/public/hotels');
        mkdir($this->directory . '/public/payments');
        mkdir($this->directory . '/restore');

        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');

        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory);
        config()->set('nativephp-internal.running', true);
        config()->set('nativephp.version', '1.2.3');
        config()->set('filesystems.default', 'public');
        config()->set('filesystems.disks.public', ['driver' => 'local', 'root' => $this->directory . '/public']);
        config()->set('database.connections.round_trip', ['driver' => 'sqlite', 'database' => $this->directory . '/active.sqlite', 'prefix' => '']);
        touch($this->directory . '/active.sqlite');

        DB::setDefaultConnection('round_trip');
        DB::statement('CREATE TABLE migrations (migration TEXT NOT NULL, batch INTEGER NOT NULL)');
        DB::statement("INSERT INTO migrations VALUES ('first', 1)");
        DB::statement('CREATE TABLE hotels (image TEXT, name TEXT)');
        DB::statement("INSERT INTO hotels VALUES ('hotels/photo.jpg', 'before')");
        DB::statement('CREATE TABLE payments (support_path TEXT)');
        DB::statement("INSERT INTO payments VALUES ('payments/receipt.pdf')");
        DB::statement('CREATE TABLE hotel_compliance_profiles (credentials TEXT)');
        DB::table('hotel_compliance_profiles')->insert(['credentials' => Crypt::encryptString('{"secret":"example"}')]);

        file_put_contents($this->directory . '/public/hotels/photo.jpg', 'original photo');
        file_put_contents($this->directory . '/public/payments/receipt.pdf', 'original receipt');
    }

    protected function tearDown(): void
    {
        DB::disconnect('round_trip');

        if ($this->previousUserDataPath === false) {
            putenv('NATIVEPHP_USER_DATA_PATH');
        } else {
            putenv('NATIVEPHP_USER_DATA_PATH=' . $this->previousUserDataPath);
        }

        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function a_selected_backup_can_be_restored_through_the_desktop_routes(): void
    {
        config()->set('backup.maintenance_timeout_ms', 0);
        $archive = $this->directory . '/backup.zip';
        app(DesktopBackupExport::class)->export($archive);
        DB::table('hotels')->update(['name' => 'after']);
        file_put_contents($this->directory . '/public/hotels/photo.jpg', 'changed photo');
        $dialog = new class($archive) extends DesktopFileDialog
        {
            public function __construct(private readonly string $archive) {}

            public function open(string $title, string $button, string $defaultPath): null|string
            {
                return $this->archive;
            }
        };
        $this->app->instance(DesktopFileDialog::class, $dialog);

        $this->post('/settings/backup/restore/select')
            ->assertRedirect(route('settings.backup.edit'))
            ->assertSessionHas('backup.selected.path', $archive);

        $this->post('/settings/backup/restore/confirm')
            ->assertRedirect(route('settings.backup.edit'))
            ->assertSessionHas('success');

        $this->assertSame('before', DB::table('hotels')->value('name'));
        $this->assertSame('original photo', file_get_contents($this->directory . '/public/hotels/photo.jpg'));
        $this->assertSame([], glob($this->directory . '/backup-*', GLOB_ONLYDIR));
    }

    #[Test]
    public function a_created_backup_can_be_selected_without_a_preexisting_storage_backup_directory(): void
    {
        $archive = $this->directory . '/backup.zip';
        app(DesktopBackupExport::class)->export($archive);
        $dialog = new class($archive) extends DesktopFileDialog
        {
            public function __construct(private readonly string $archive) {}

            public function open(string $title, string $button, string $defaultPath): null|string
            {
                return $this->archive;
            }
        };
        $this->app->instance(DesktopFileDialog::class, $dialog);
        $storagePath = $this->app->storagePath();
        $this->app->useStoragePath($this->directory . '/missing-storage');

        try {
            $this->post('/settings/backup/restore/select')
                ->assertRedirect(route('settings.backup.edit'))
                ->assertSessionHas('backup.selected.path', $archive);

            $this->assertSame('before', DB::table('hotels')->value('name'));
            $this->assertSame('original photo', file_get_contents($this->directory . '/public/hotels/photo.jpg'));
            $this->assertSame([], glob($this->directory . '/backup-*', GLOB_ONLYDIR));
        } finally {
            $this->app->useStoragePath($storagePath);
        }
    }

    #[Test]
    public function the_desktop_export_route_creates_a_backup_without_blocking_itself(): void
    {
        config()->set('backup.maintenance_timeout_ms', 0);
        $destination = $this->directory . '/backup.zip';
        $dialog = new class($destination) extends DesktopFileDialog
        {
            public function __construct(private readonly string $destination) {}

            public function save(string $title, string $button, string $defaultPath): null|string
            {
                return $this->destination;
            }
        };
        $this->app->instance(DesktopFileDialog::class, $dialog);

        $this->post('/settings/backup/export')
            ->assertRedirect(route('settings.backup.edit'))
            ->assertSessionHas('success');

        $this->assertFileExists($destination);
    }

    #[Test]
    public function an_export_can_be_prepared_for_restore_without_modifying_current_data(): void
    {
        $archive = $this->directory . '/backup.zip';

        app(DesktopBackupExport::class)->export($archive);

        DB::table('hotels')->update(['name' => 'after']);

        file_put_contents($this->directory . '/public/hotels/photo.jpg', 'changed photo');
        unlink($this->directory . '/public/payments/receipt.pdf');

        app(PrepareRestore::class)->prepare($archive, $this->directory . '/restore', DB::connection()->getPdo(), '1.2.3', 100, 1000000);

        $snapshot = new PDO('sqlite:' . $this->directory . '/restore/dataset/database.sqlite');
        $this->assertSame('before', $snapshot->query('SELECT name FROM hotels')->fetchColumn());
        $this->assertSame('original photo', file_get_contents($this->directory . '/restore/dataset/hotels/photo.jpg'));
        $this->assertSame('original receipt', file_get_contents($this->directory . '/restore/dataset/payments/receipt.pdf'));
        $this->assertSame('after', DB::table('hotels')->value('name'));
        $this->assertSame('changed photo', file_get_contents($this->directory . '/public/hotels/photo.jpg'));
        $this->assertFileDoesNotExist($this->directory . '/public/payments/receipt.pdf');
    }

    #[Test]
    public function prepared_resources_can_be_installed_and_verified_with_the_previous_dataset_retained(): void
    {
        $archive = $this->directory . '/backup.zip';
        app(DesktopBackupExport::class)->export($archive);
        DB::table('hotels')->update(['name' => 'after']);
        file_put_contents($this->directory . '/public/hotels/obsolete.jpg', 'obsolete');
        $manifest = app(PrepareRestore::class)->prepare($archive, $this->directory . '/restore', DB::connection()->getPdo(), '1.2.3', 100, 1000000);
        $lock = app(DesktopMaintenanceLock::class)->exclusive();

        try {
            $plan = app(RestoreResourcePlan::class)->build($this->directory . '/active.sqlite', app(BackupDiskRoots::class)->resolve(), $manifest, 'roundtrip', $lock);
            app(RestoreReplacementStager::class)->stage($this->directory . '/restore/dataset', $plan, $manifest, $lock);
            DB::disconnect('round_trip');
            app(RestoreInstaller::class)->install(
                $this->directory . '/restore.journal',
                'roundtrip',
                $plan['resources'],
                $lock,
                fn (array $resources): bool => app(RestoreInstalledVerifier::class)->verify($resources, $plan['files'], $manifest),
            );

            $restored = new PDO('sqlite:' . $this->directory . '/active.sqlite');
            $previous = new PDO('sqlite:' . $plan['resources']['database']['previous']);
            $this->assertSame('before', $restored->query('SELECT name FROM hotels')->fetchColumn());
            $this->assertSame('after', $previous->query('SELECT name FROM hotels')->fetchColumn());
            $this->assertFileDoesNotExist($this->directory . '/public/hotels/obsolete.jpg');
            $this->assertSame('obsolete', file_get_contents($plan['resources']['hotels-public']['previous'] . '/obsolete.jpg'));
            $this->assertFileExists($this->directory . '/restore.journal');
            $restored = null;
            $previous = null;
        } finally {
            $lock->release();
        }
    }

    #[Test]
    public function a_failed_verification_reverts_the_entire_previous_dataset(): void
    {
        $archive = $this->directory . '/backup.zip';
        app(DesktopBackupExport::class)->export($archive);
        DB::table('hotels')->update(['name' => 'after']);
        file_put_contents($this->directory . '/public/hotels/obsolete.jpg', 'obsolete');
        $manifest = app(PrepareRestore::class)->prepare($archive, $this->directory . '/restore', DB::connection()->getPdo(), '1.2.3', 100, 1000000);
        $lock = app(DesktopMaintenanceLock::class)->exclusive();

        try {
            $plan = app(RestoreResourcePlan::class)->build($this->directory . '/active.sqlite', app(BackupDiskRoots::class)->resolve(), $manifest, 'failure', $lock);
            app(RestoreReplacementStager::class)->stage($this->directory . '/restore/dataset', $plan, $manifest, $lock);
            DB::disconnect('round_trip');

            try {
                app(RestoreInstaller::class)->install(
                    $this->directory . '/restore.journal',
                    'failure',
                    $plan['resources'],
                    $lock,
                    fn (): bool => false,
                );
                $this->fail('A failed verification must restore the previous dataset.');
            } catch (RuntimeException) {
                $database = new PDO('sqlite:' . $this->directory . '/active.sqlite');
                $this->assertSame('after', $database->query('SELECT name FROM hotels')->fetchColumn());
                $this->assertSame('obsolete', file_get_contents($this->directory . '/public/hotels/obsolete.jpg'));
                $this->assertFileExists($this->directory . '/restore.journal');
                $database = null;
            }
        } finally {
            $lock->release();
        }
    }
}
