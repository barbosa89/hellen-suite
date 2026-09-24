<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Settings\AppConfiguration;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAppConfigured
{
    public function __construct(private AppConfiguration $configuration) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->configuration->missingSettings() !== []) {
            return redirect()->route('settings.edit')
                ->with('error', trans('settings.messages.configuration_required'));
        }

        return $next($request);
    }
}
