<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ApiLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {   

        $url = \Request::url();
        $ip = \Request::ip();

        \Log::info("REST API - " . $ip . ' requested ' . $url);

        return $next($request);
    }
}
