<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdministratorsOnly
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

        if (Auth::guest()) {
            return redirect('/login/?next=/admin/');
        }

        if (Auth::user()->admin != 1) {
            return redirect('/');
        }

        return $next($request);
    }
}
