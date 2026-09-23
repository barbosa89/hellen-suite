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
        $currencyRoutes = [
            'hotels.rooms.create',
            'hotels.rooms.store',
            'hotels.rooms.edit',
            'hotels.rooms.update',
            'hotels.stays.create',
            'hotels.stays.store',
            'hotels.stays.show',
            'hotels.stays.expected-check-out.update',
            'hotels.stays.check-out',
            'hotels.stays.guests.store',
            'hotels.stays.room-occupancies.transfer',
        ];
        $routeName = $request->route()?->getName();

        if (! str_starts_with((string) $routeName, 'hotels.reservations.') && ! in_array($routeName, $currencyRoutes, true)) {
            return $next($request);
        }

        if ($this->settings->currency === null) {
            return redirect()->route('settings.edit')
                ->with('error', trans('settings.messages.currency_required'));
        }

        return $next($request);
    }
}
