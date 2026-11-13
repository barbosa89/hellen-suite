<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopBackupRoutesTest extends TestCase
{
    use RefreshDatabase;

    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-backup-routes-' . bin2hex(random_bytes(8));
        mkdir($this->directory);
        $this->previousUserDataPath = getenv('NATIVEPHP_USER_DATA_PATH');
        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory);
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
    public function the_backup_pages_are_not_available_on_the_web(): void
    {
        $this->get('/settings/backup')->assertNotFound();
        $this->post('/settings/backup/export')->assertNotFound();
        $this->post('/settings/backup/restore/select')->assertNotFound();
        $this->post('/settings/backup/restore/confirm')->assertNotFound();
    }

    #[Test]
    public function the_backup_page_is_available_on_the_desktop_application(): void
    {
        config()->set('nativephp-internal.running', true);

        $this->get('/settings/backup')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Settings/Backup'));
    }

    #[Test]
    public function the_settings_page_links_to_the_backup_page_on_the_desktop(): void
    {
        config()->set('nativephp-internal.running', true);

        $this->get('/settings')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('isDesktop', true));
    }

    #[Test]
    public function the_settings_page_hides_the_backup_link_on_the_web(): void
    {
        config()->set('nativephp-internal.running', false);

        $this->get('/settings')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('isDesktop', false));
    }
}
