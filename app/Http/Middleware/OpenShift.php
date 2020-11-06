<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Shift;
use App\Models\Voucher;

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
        $voucher = Voucher::where('id', id_decode($request->route('id')))
            ->with('hotel:id')
            ->first(['id', 'hotel_id']);

        // Query all open shifts for authenticated user
        $shifts = Shift::open()->get(['id', 'open', 'hotel_id']);

        if ($shifts->isEmpty() or $shifts->where('hotel_id', $voucher->hotel->id)->count() === 1) {
            return $next($request);
        }

        return abort(403);
    }
}
