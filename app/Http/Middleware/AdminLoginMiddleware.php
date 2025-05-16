<?php

namespace App\Http\Middleware;

use App\Common\Utils;
use App\Http\Models\Notify;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_encode;
use function redirect;
use function view;

class AdminLoginMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $time = time();
        $notify = Notify::where("del_status", 0)->whereRaw("(start_time = 0 or start_time <= $time) and (end_time =0 or end_time > $time)")->first();

        if ($notify) {
            if ($notify->is_maintenance) {
                $showMaintenance = 1;
                if (Auth::check()) {
                    $user = Auth::user();
                    $arrayRole = explode(",", $user->role);
                    if (in_array('6', $arrayRole)) {
                        $showMaintenance = 0;
                    }
                }
                if ($showMaintenance == 1) {
                    return redirect('/');
                }
            } else {
                view()->share("notify", $notify);
            }
        }

        if (Auth::check()) {
            $user = Auth::user();
            $user->last_activity = time();
            if ($user->ip == null) {
                $user->ip = Utils::getUserIpAddr();
            }
            $user->save();
            $arrayRole = explode(",", $user->role);
            $isAdmin = 0;
            $isVip = 0;
            $isTiktok = 0;
            $isTax = 0;
            $isUpdateSource = 0;
            $isTiktokMulti = 0;
            //được tăng số lượng luông live được tạo
            $isMaxLive = 0;
            if (in_array('1', $arrayRole)) {
                $isAdmin = 1;
            }
            if (in_array('2', $arrayRole)) {
                $isVip = 1;
            }
            if (in_array('5', $arrayRole)) {
                $isTiktok = 1;
            }
            if (in_array('4', $arrayRole)) {
                $isMaxLive = 1;
            }
            if (in_array('7', $arrayRole)) {
                $isTax = 1;
            }
            if (in_array('8', $arrayRole)) {
                $isUpdateSource = 1;
            }
            if (in_array('9', $arrayRole)) {
                $isTiktokMulti = 1;
            }
            view()->share("isAdmin", $isAdmin);
            view()->share("isVip", $isVip);
            view()->share("isTiktok", $isTiktok);
            view()->share("user_login", $user);
            view()->share("isTax", $isTax);
            view()->share("isUpdateSource", $isUpdateSource);
            view()->share("isTiktokMulti", $isTiktokMulti);
            $request['isAdmin'] = $isAdmin;
            $request['isVip'] = $isVip;
            $request['isMaxLive'] = $isMaxLive;
            $request['isTax'] = $isTax;
            $request['isUpdateSource'] = $isUpdateSource;
            $request['isTiktokMulti'] = $isTiktokMulti;
            if ($isAdmin) {
                $count = DB::select("select count(id) as total from `tiktok_profile` where  status_cookie=1 and del_status =0 and (JSON_EXTRACT(active_v3_info, '$.status') = 'waiting')");
                view()->share("count_waiting_v3", $count[0]->total);
            }
            if ($isAdmin || $isTax) {
                $summary = DB::select("SELECT SUM(CASE WHEN vat_code IS NULL THEN 1 ELSE 0 END) AS NOT_VAT,
                                            SUM(CASE WHEN vat_code IS NOT NULL THEN 1 ELSE 0 END) AS VAT
                                            FROM 
                                            invoice_vat where del_status = 0");
                view()->share("count_tax", $summary[0]->NOT_VAT);
            }
            //kiểm tra xem có phải là khách hàng còn hạn không
            $isActiveCus = 0;
            if ($user->package_code != "LIVETEST" && $user->package_end_date > time()) {
                $isActiveCus = 1;
            }
            if ($user->tiktok_package != "TIKTOKTEST" && $user->tiktok_end_date > time()) {
                $isActiveCus = 1;
            }
            view()->share("isActiveCus", $isActiveCus);
//            \View::share("user_login", Auth::user());
//            DB::enableQueryLog();
//            $notify = Notification::where('status', 1)->where('start_date', '<', time())->where('end_date', '>', time())->where('user_id', Auth::user()->id)->orderBy('start_date', 'desc')->limit(5)->get();
//            $notifyAll = Notification::where('status', 1)->where('start_date', '<', time())->where('end_date', '>', time())->where('type', 1)->get();
//            view()->share("notify", $notify);
//            view()->share("notifyAll", $notifyAll);
//            Log::info(DB::getQueryLog());
//            if ($user->status == 0) {
//                Auth::logout();
//                return redirect('login')->with("message", 'Tài khoản của bạn đã chưa được kích hoạt, hãy liên hệ admin');
//            }
            return $next($request);
        } else {
            return redirect('login');
        }
    }

}
