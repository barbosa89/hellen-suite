<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Backup\DesktopProcessLease;
use App\Support\Backup\DesktopQueueMaintenance;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Events\JobAttempted;
use Illuminate\Queue\Events\JobPopping;
use Illuminate\Queue\Events\Looping;
use Illuminate\Queue\Events\WorkerIdle;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DesktopQueueMaintenance::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Model::shouldBeStrict();

        Event::listen(Looping::class, fn (): null|bool => $this->app->make(DesktopQueueMaintenance::class)->begin());
        Event::listen(JobPopping::class, fn () => $this->app->make(DesktopQueueMaintenance::class)->beforePop());
        Event::listen(WorkerIdle::class, fn () => $this->app->make(DesktopQueueMaintenance::class)->finish());
        Event::listen(JobAttempted::class, fn () => $this->app->make(DesktopQueueMaintenance::class)->finish());
        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            if ($event->command !== 'queue:listen' || ! config('nativephp-internal.running', false)) {
                return;
            }

            $database = $this->app->make('db');

            foreach (array_keys($database->getConnections()) as $name) {
                $database->purge($name);
            }

            $this->app->make(DesktopProcessLease::class)->release();
        });
        Event::listen(CommandFinished::class, function (CommandFinished $event): void {
            if ($event->command === 'queue:work') {
                $this->app->make(DesktopQueueMaintenance::class)->finish();
            }
        });

        $this->app->terminating(fn () => $this->app->make(DesktopQueueMaintenance::class)->finish());
    }
}
