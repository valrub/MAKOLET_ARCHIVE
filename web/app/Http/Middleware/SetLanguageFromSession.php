<?php

namespace App\Http\Middleware;

use App;
use Config;
use Session;
use Closure;

class SetLanguageFromSession
{

    protected $languages = ['iw', 'en'];

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

        if (!Session::has('lang')) {
            //Session::put('lang', $request->getPreferredLanguage($this->languages));
            Session::put('lang', Config::get('app.locale'));
        }

        App::setLocale(Session::get('lang'));

        return $next($request);
    }
}
