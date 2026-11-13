<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\RuntimeException;
use App\Support\Backup\DesktopMaintenanceLock;
use App\Support\Backup\DesktopProcessLease;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class BlockDuringDesktopMaintenance
{
    public function __construct(
        private readonly DesktopMaintenanceLock $maintenance,
        private readonly DesktopProcessLease $processLease,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('nativephp-internal.running', false)) {
            return $next($request);
        }

        try {
            $this->processLease->hold($this->maintenance->shared());
        } catch (RuntimeException) {
            return $this->unavailable();
        }

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $this->processLease->release();

            throw $exception;
        }

        app()->terminating(fn () => $this->processLease->release());

        return $response;
    }

    private function unavailable(): Response
    {
        return response(trans('backup.maintenance_unavailable'), 503, ['Retry-After' => '5']);
    }
}
