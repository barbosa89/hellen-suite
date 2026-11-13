<?php

declare(strict_types=1);

namespace Tests\Feature\Backup;

use App\Exceptions\RuntimeException;
use App\Http\Middleware\BlockDuringDesktopMaintenance;
use App\Support\Backup\BackupMaintenanceLock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase;

class DesktopMaintenanceMiddlewareTest extends TestCase
{
    private string $directory;

    private string|false $previousUserDataPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/hellen-middleware-' . bin2hex(random_bytes(8));
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
    public function desktop_requests_are_blocked_during_exclusive_maintenance(): void
    {
        config()->set('nativephp-internal.running', true);
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');

        app()->setLocale('en');
        $this->get('/')->assertStatus(503)->assertSee('Wait for maintenance to finish');
        app()->setLocale('es');
        $this->get('/')->assertStatus(503)->assertSee('Espera a que termine el mantenimiento');

        $lock->release();
        $this->get('/')->assertRedirect(route('hotels.index'));
    }

    #[Test]
    public function web_requests_do_not_use_the_desktop_lock(): void
    {
        config()->set('nativephp-internal.running', false);
        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');

        $this->get('/')->assertRedirect(route('hotels.index'));

        $lock->release();
    }

    #[Test]
    public function desktop_requests_fail_closed_when_the_lock_location_is_unavailable(): void
    {
        config()->set('nativephp-internal.running', true);
        putenv('NATIVEPHP_USER_DATA_PATH=' . $this->directory . '/missing');

        $this->get('/')->assertStatus(503);
    }

    #[Test]
    public function desktop_requests_hold_a_shared_lock_while_the_route_runs(): void
    {
        config()->set('nativephp-internal.running', true);
        $path = $this->directory . '/backup-maintenance.lock';
        Route::get('/maintenance-lock-test', function () use ($path) {
            try {
                BackupMaintenanceLock::exclusive($path);

                return response('not protected', 500);
            } catch (RuntimeException) {
                return response('protected');
            }
        });

        $this->get('/maintenance-lock-test')->assertOk()->assertSee('protected');
        $lock = BackupMaintenanceLock::exclusive($path);
        $lock->release();
    }

    #[Test]
    public function streamed_responses_hold_the_lock_until_application_termination(): void
    {
        config()->set('nativephp-internal.running', true);
        $path = $this->directory . '/backup-maintenance.lock';
        $middleware = app(BlockDuringDesktopMaintenance::class);
        $response = $middleware->handle(Request::create('/'), fn () => response()->stream(fn () => print ('content')));

        try {
            BackupMaintenanceLock::exclusive($path);
            $this->fail('Streaming must retain the request maintenance lock.');
        } catch (RuntimeException) {
            $this->assertTrue(true);
        }

        ob_start();
        $response->sendContent();
        $this->assertSame('content', ob_get_clean());
        app()->terminate();
        $lock = BackupMaintenanceLock::exclusive($path);
        $lock->release();
    }

    #[Test]
    public function failed_requests_release_the_shared_lock(): void
    {
        config()->set('nativephp-internal.running', true);

        try {
            app(BlockDuringDesktopMaintenance::class)->handle(Request::create('/'), function () {
                throw new RuntimeException('Test failure.');
            });
            $this->fail('The route failure must propagate.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Test failure.', $exception->getMessage());
        }

        $lock = BackupMaintenanceLock::exclusive($this->directory . '/backup-maintenance.lock');
        $lock->release();
    }
}
