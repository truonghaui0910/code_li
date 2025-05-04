<?php

namespace App\Http\Middleware;

use Closure;
use Lang;
use Session;

class Locale {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
//        error_log("LOCALE ".config('app.locale'));
//        error_log("LOCALE2 ".Session::get('locale'));
//        error_log("LOCALE3 ".Session::has('locale'));
        if (!Session::has('locale')) {
            Session::put('locale', config('app.locale'));
        }
        Lang::setLocale(Session::get('locale'));
        return $next($request);
    }

}
