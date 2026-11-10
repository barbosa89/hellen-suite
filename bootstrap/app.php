<?php

declare(strict_types=1);

use App\Exceptions\AppKeyException;
use App\Http\Middleware\EnsureAppConfigured;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use App\Support\NativeAppKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
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

$app->afterBootstrapping(LoadConfiguration::class, function (Application $app): void {
    if (getenv('NATIVEPHP_RUNNING') !== 'true' || getenv('APP_ENV') !== 'production') {
        return;
    }

    $path = getenv('NATIVEPHP_USER_DATA_PATH');

    if (! is_string($path) || $path === '') {
        throw new AppKeyException('NativePHP user data path is unavailable.');
    }

    $app['config']->set('app.key', NativeAppKey::load($path));
    $app['config']->set('app.previous_keys', []);
});

return $app;
