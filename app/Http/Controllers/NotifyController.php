<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Utils;
use App\Http\Models\Notify;
use App\Http\Models\TiktokProfile;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zlivecustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class NotifyController extends Controller {

    public function find($id) {
        $user = Auth::user();
        Log::info("$user->user_name|NotifyController.find|request=$id");
        $notify = Notify::find($id);
        if ($notify) {
            if ($notify->start_time == 0) {
                $notify->start_time_text = "Hiện tại";
            } else {
                $notify->start_time_text = gmdate("Y-m-d", $notify->start_time + 7 * 3600) . "T" . gmdate("H:i", $notify->start_time + 7 * 3600);
            }
            if ($notify->end_time == 0) {
                $notify->end_time_text = "Vĩnh viễn";
            } else {
                $notify->end_time_text = gmdate("Y-m-d", $notify->end_time + 7 * 3600) . "T" . gmdate("H:i", $notify->end_time + 7 * 3600);
            }
            return array("status" => "success", "message" => "Success", "data" => $notify);
        }
        return array("status" => "error", "message" => "Not found Data");
    }

    public function update(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|NotifyController.update|request=" . json_encode($request->all()));
        if (isset($request->id)) {
            $notify = Notify::find($request->id);
            if (!$notify) {
                return array("status" => "error", "message" => "Not found $request->id");
            }
            $notify->del_status = 1;
            $notify->save();
            return array("status" => "success", "message" => "Success");
        }
        return array("status" => "error", "message" => "Not found $request->id");
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|NotifyController.store|request=" . json_encode($request->all()));
//        if ($request->notify_title == null || $request->notify_title == "") {
//            return array("status" => "error", "message" => "Bạn phải nhập tiêu đề");
//        }
        if ($request->notify_content == null || $request->notify_content == "") {
            return array("status" => "error", "message" => "Bạn phải nhập nội dung");
        }
        if ($request->notify_id == null) {
            $notify = new Notify();
        } else {
            $notify = Notify::find($request->notify_id);
        }
        $notify->content = $request->notify_content;
        $dateStart = 0;
        $dateEnd = 0;

        if ($request->date_start != null) {
            $dateStart = strtotime("$request->date_start GMT$user->timezone");
        }
        if (isset($request->chk_date_end)) {
            if ($request->date_end != null) {
                $dateEnd = strtotime("$request->date_end GMT$user->timezone");
            }
        }

        $notify->start_time = $dateStart;
        $notify->end_time = $dateEnd;
        if (isset($request->is_maintenance)) {
            $notify->is_maintenance = 1;
        }
        $notify->save();
        if ($notify->start_time == 0) {
            $notify->start_time_text = "Hiện tại";
        } else {
            $notify->start_time_text = gmdate("Y/m/d H:i:s", $notify->start_time + 7 * 3600);
        }
        if ($notify->end_time == 0) {
            $notify->end_time_text = "Vĩnh viễn";
        } else {
            $notify->end_time_text = gmdate("Y/m/d H:i:s", $notify->end_time + 7 * 3600);
        }
        $notify->type = "Thông báo";
        if ($notify->is_maintenance) {
            $notify->type = "Bảo trì";
        }

        return array("status" => "success", "message" => "Thành công", "data" => $notify);
    }

}
