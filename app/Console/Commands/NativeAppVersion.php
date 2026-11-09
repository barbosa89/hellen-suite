<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:native-app-version')]
#[Description('Display the current NativePHP app version')]
class NativeAppVersion extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(config('nativephp.version'));
    }
}
