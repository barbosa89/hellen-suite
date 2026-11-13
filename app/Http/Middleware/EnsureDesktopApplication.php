<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureDesktopApplication
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('nativephp-internal.running', false)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
