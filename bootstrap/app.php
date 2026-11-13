<?php

declare(strict_types=1);

use App\Exceptions\AppKeyException;
use App\Http\Middleware\BlockDuringDesktopMaintenance;
use App\Http\Middleware\EnsureAppConfigured;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use App\Support\Backup\ActiveSqliteDatabase;
use App\Support\Backup\BackupDiskRoots;
use App\Support\Backup\DesktopMaintenanceGate;
use App\Support\Backup\DesktopProcessLease;
use App\Support\Backup\DesktopRestoreRecoveryRunner;
use App\Support\NativeAppKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(BlockDuringDesktopMaintenance::class);
        $middleware->web(append: [
            SetLocale::class,
            EnsureAppConfigured::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

$processLease = new DesktopProcessLease();
$app->instance(DesktopProcessLease::class, $processLease);

$app->afterBootstrapping(LoadConfiguration::class, function (Application $app) use ($processLease): void {
    if (getenv('NATIVEPHP_RUNNING') !== 'true') {
        return;
    }

    $path = getenv('NATIVEPHP_USER_DATA_PATH');

    if ($path === false || $path === '') {
        throw new AppKeyException('NativePHP user data path is unavailable.');
    }

    $processLease->hold((new DesktopMaintenanceGate($path))->enter());

    if (getenv('APP_ENV') !== 'production') {
        return;
    }

    $app['config']->set('app.key', NativeAppKey::load($path));
    $app['config']->set('app.previous_keys', []);
});

/**
 * An interrupted restore is recovered before any provider touches the database,
 * so the application never starts on a partially replaced dataset.
 */
$app->afterBootstrapping(RegisterProviders::class, function (Application $app): void {
    if (getenv('NATIVEPHP_RUNNING') !== 'true') {
        return;
    }

    $directory = getenv('NATIVEPHP_USER_DATA_PATH');

    if ($directory === false || $directory === '') {
        return;
    }

    $app->make(DesktopRestoreRecoveryRunner::class)->run(
        $directory,
        $app->make(ActiveSqliteDatabase::class)->configuredPath(),
        $app->make(BackupDiskRoots::class)->resolve(),
    );
});

$app->afterBootstrapping(BootProviders::class, function (Application $app) use ($processLease): void {
    if ($app->runningInConsole()) {
        $app->terminating(fn () => $processLease->release());

        return;
    }

    $processLease->release();
});

return $app;
