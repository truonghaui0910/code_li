<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class ExpiredMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
//        dd($request);
        Log::info(request()->headers->get('referer'));
        if (Auth::check()) {
            $user = Auth::user();
            $referer  = request()->headers->get('referer');
            
            if ($user->package_end_date < time() && $user->tiktok_end_date < time() && $user->shopee_end_date < time()) {
                $user->expired_scan = 1;
                $user->save();
                return redirect('/pricing')->with("message", 'Tài khoản của bạn đã hết hạn, hãy gia hạn');
            }
            return $next($request);
        } else {
            return redirect('login');
        }
    }

}
