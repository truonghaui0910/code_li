<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class ShopeeExpiredMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->shopee_end_date < time()) {
                $user->expired_scan = 1;
                $user->save();
                return redirect('/sppricing')->with("message", 'Tài khoản của bạn đã hết hạn, hãy gia hạn');
            }
//            $arrayRole = explode(",", $user->role);
//            if (!in_array('5', $arrayRole)) {
//                return redirect('/profile');
//            }
            return $next($request);
        } else {
            return redirect('login');
        }
    }

}
