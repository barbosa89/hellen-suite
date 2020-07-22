<?php

namespace App\Http\Middleware;

use Closure;

class OpenShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hotelId = $request->route('id');

        // Query all open shifts for authenticated user
        $shifts = \App\Welkome\Shift::open()->get(['id', 'open', 'hotel_id']);

        if ($shifts->isEmpty() or $shifts->where('hotel_id', $hotelId)->count() === 1) {
            return $next($request);
        }

        return abort(403);
    }
}
