<?php

namespace App\Http\Middleware;

use Closure;

class SanitizeInput
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
        if (!in_array(strtolower($request->method()), ['put', 'post'])) {
            return $next($request);
        }

        $input = $request->all();

        array_walk_recursive($input, function(&$input){
            $input = htmlentities(strip_tags(trim($input)));
        });

        $request->merge($input);

        return $next($request);
    }
}