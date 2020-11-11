<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class VerifyUserPlan
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
        if (Auth::check() && !Auth::user()->hasRole('root')) {
            $user = Auth::user()->load('plans:id,price,months,type,status');

            if ($user->plans->isEmpty()) {
                return redirect()->route('plans.choose');
            }

            if ($this->hasNoActivePlans($user->plans)) {
                return redirect()->route('plans.renew');
            }
        }

        return $next($request);
    }

    /**
     * Check the user has no active plans.
     *
     * @param \Illuminate\Support\Collection $plans
     * @return boolean
     */
    private function hasNoActivePlans(Collection $plans): bool
    {
        $actives = $plans->filter(function ($plan) {
            return $plan->isActive();
        });

        return $actives->isEmpty();
    }
}
