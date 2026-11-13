<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Support\Backup\DesktopFileDialog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopBackupControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-backup-controller-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');
        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory);
        config()->set('nativephp-internal.running', true);
        config()->set('nativephp.version', '1.2.3');
    }

    protected function tearDown(): void
    {
        if ($this->previousUserDataPath === false) {
            putenv('NATIVEPHP_USER_DATA_PATH');
        } else {
            putenv('NATIVEPHP_USER_DATA_PATH=' . $this->previousUserDataPath);
        }

        (new Filesystem())->remove($this->directory);
        parent::tearDown();
    }

    #[Test]
    public function a_cancelled_export_leaves_everything_unchanged(): void
    {
        $this->fakeDialog(save: null);

        $this->post('/settings/backup/export')
            ->assertRedirect(route('settings.backup.edit'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function an_existing_destination_is_never_replaced_without_confirmation(): void
    {
        $destination = $this->directory . '/backup.zip';
        file_put_contents($destination, 'existing');
        $this->fakeDialog(save: $destination);

        $this->post('/settings/backup/export')
            ->assertRedirect(route('settings.backup.edit'))
            ->assertSessionHas('error')
            ->assertSessionHas('backup.export_destination', $destination);

        $this->assertSame('existing', file_get_contents($destination));
    }

    #[Test]
    public function a_cancelled_restore_leaves_the_dataset_untouched(): void
    {
        $this->fakeDialog(open: null);

        $this->post('/settings/backup/restore/select')
            ->assertRedirect(route('settings.backup.edit'))
            ->assertSessionHas('error');

        $this->assertNull(session('backup.selected'));
    }

    #[Test]
    public function a_restore_without_a_selected_file_is_refused(): void
    {
        $this->post('/settings/backup/restore/confirm')
            ->assertRedirect(route('settings.backup.edit'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function the_selected_backup_is_cleared_when_the_restore_is_cancelled(): void
    {
        $this->withSession(['backup.selected' => ['path' => '/tmp/backup.zip']]);

        $this->post('/settings/backup/restore/cancel')
            ->assertRedirect(route('settings.backup.edit'));

        $this->assertNull(session('backup.selected'));
    }

    private function fakeDialog(null|string $save = null, null|string $open = null): void
    {
        $dialog = new class($save, $open) extends DesktopFileDialog
        {
            public function __construct(private readonly null|string $save, private readonly null|string $open) {}

            public function save(string $title, string $button, string $defaultPath): null|string
            {
                return $this->save;
            }

            public function open(string $title, string $button, string $defaultPath): null|string
            {
                return $this->open;
            }
        };

        $this->app->instance(DesktopFileDialog::class, $dialog);
    }
}
