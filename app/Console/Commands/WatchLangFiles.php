<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;
use Spatie\Watcher\Watch;

class WatchLangFiles extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:watch-lang';

    /**
     * @var string
     */
    protected $description = 'Watch language files for changes';

    public function handle(): int
    {
        if (!app()->isProduction()) {
            $this->newLine();
            $this->info('Watching language files for changes...');

            Watch::path(base_path('lang'))
                ->onAnyChange(function (): void {
                    $this->newLine();
                    $this->info('Language file changed');

                    $exitCode = Artisan::call('vue:translations');

                    $this->newLine();

                    if (Command::SUCCESS !== $exitCode) {
                        $this->error('Failed to update translations.');

                        throw new RuntimeException('Failed to update translations.');
                    }
                    $this->info('Translations updated successfully.');

                })
                ->start();
        }

        $this->info('Watching for language file changes is only enabled in local environment.');

        return Command::SUCCESS;
    }
}
