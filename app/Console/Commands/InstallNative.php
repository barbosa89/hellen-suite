<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

#[Signature('app:native:install
    {--force : Overwrite existing NativePHP files by default}
    {--publish : Publish the Electron project to the project root}
    {--installer=npm : The package installer to use: npm or yarn}')]
#[Description('Install NativePHP and run the Electron postinstall manually')]
class InstallNative extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $exitCode = $this->call('native:install', [
            '--force' => $this->option('force'),
            '--publish' => $this->option('publish'),
            '--installer' => $this->option('installer'),
            '--no-interaction' => $this->option('no-interaction'),
        ]);

        if ($exitCode !== self::SUCCESS) {
            return $exitCode;
        }

        return $this->runNativePostInstall();
    }

    private function runNativePostInstall(): int
    {
        $this->components->info('Running NativePHP Electron postinstall...');

        $result = Process::run('node ./vendor/nativephp/desktop/resources/electron/node_modules/electron/install.js', function (string $_, string $output): void {
            $this->output->write($output);
        });

        if ($result->failed()) {
            $this->components->error("NativePHP Electron postinstall failed with exit code {$result->exitCode()}.");

            return $result->exitCode();
        }

        return self::SUCCESS;
    }
}
