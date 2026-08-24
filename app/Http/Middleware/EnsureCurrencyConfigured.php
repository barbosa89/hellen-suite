<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Settings\GeneralSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use function in_array;

class EnsureCurrencyConfigured
{
    public function __construct(private GeneralSettings $settings) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $routes = [
            'hotels.rooms.create',
            'hotels.rooms.store',
            'hotels.rooms.edit',
            'hotels.rooms.update',
        ];

        if (! in_array($request->route()?->getName(), $routes, true)) {
            return $next($request);
        }

        if ($this->settings->currency === null) {
            return redirect()->route('settings.edit')
                ->with('error', trans('settings.messages.currency_required'));
        }

        return $next($request);
    }
}
