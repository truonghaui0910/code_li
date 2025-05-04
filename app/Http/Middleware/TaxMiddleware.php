<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class TaxMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $user = Auth::user();
        $arrayRole = explode(",", $user->role);
        if (in_array('1', $arrayRole) || in_array('7', $arrayRole)) {
            return $next($request);
        } else {
            abort(404, 'Page Not Found');
        }
    }

}
