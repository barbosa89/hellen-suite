<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Backup\DesktopRestoreRecovery;
use App\Support\Backup\DesktopRestoreRecoveryRunner;
use App\Support\Backup\RestoreCleanup;
use App\Support\Backup\RestoreJournal;
use App\Support\Backup\RestoreJournalTrust;
use App\Support\Backup\RestoreRecovery;
use App\Support\Backup\RestoreTargetResolver;
use Illuminate\Support\ServiceProvider;

class DesktopRestoreRecoveryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RestoreJournal::class);
        $this->app->singleton(RestoreJournalTrust::class);
        $this->app->singleton(RestoreTargetResolver::class);
        $this->app->singleton(RestoreRecovery::class);
        $this->app->singleton(RestoreCleanup::class);
        $this->app->singleton(DesktopRestoreRecovery::class);
        $this->app->singleton(DesktopRestoreRecoveryRunner::class);
    }
}
