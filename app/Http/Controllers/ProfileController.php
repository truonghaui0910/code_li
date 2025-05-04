<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Http\Models\Invoice;
use App\Http\Models\Zliveautolive;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ProfileController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ProfileController.index|request=' . json_encode($request->all()));
        if ($request->isAdmin) {
//            DB::select("");
        } else {
            $refs = DB::select("select c.period,c.ref,count(invoice_id) as count,sum(c.payment_money * 0.1) as rev from 
                            (select a.user_name,b.ref,a.period,a.invoice_id,a.package_code,a.payment_money from invoice a,users b where a.user_name = b.user_name and b.ref is not null and a.status =1 and ref='$user->user_name') c 
                            group by c.period,c.ref
                            order by c.period desc,c.ref asc");
        }
        $datas = Zliveautolive::where("del_status", 0)->where("user_id", $user->user_code)->get();
        $countRun = 0;
        foreach ($datas as $data) {
            if ($data->status == 1 || $data->status == 2) {
                $countRun++;
            }
        }
        $countLiving = Zliveautolive::where("cus_id", $user->customer_id)->whereIn("status", [1, 2, 4])->where("del_status", 0)->count();
        $invoices = Invoice::where("user_name", $user->user_name)->orderBy("id", "desc")->get();
        $childs = [];
        if ($request->isVip) {
            $childs = User::where("customer_id", $user->customer_id)->where("user_name", '<>', $user->user_name)->get();
            $childLiving = DB::select("select user_id,platform,count(*) as living from zliveautolive where cus_id = '$user->customer_id' and status in (1,2,4) group by user_id,platform");
            foreach ($childs as $child) {
                $child->youtube_living = 0;
                $child->tiktok_living = 0;
                if (count($childLiving) > 0) {
                    foreach ($childLiving as $childLive) {
                        if ($child->user_code == $childLive->user_id) {
                            if ($childLive->platform == 1) {
                                $child->youtube_living = $childLive->living;
                            } elseif ($childLive->platform == 2) {
                                $child->tiktok_living = $childLive->living;
                            }elseif ($childLive->platform == 3) {
                                $child->shopee_living = $childLive->living;
                            }
                        }
                    }
                }
            }
        }


        return view('components.profile', [
            "countAll" => count($datas),
            "countRun" => $countRun,
            "invoices" => $invoices,
            "childs" => $childs
        ]);
    }

    public function requestVip(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ProfileController.requestVip|request=' . json_encode($request->all()));
        $check = User::where("customer_id", $user->customer_id)->where("role", "like", "%2%")->first();
        if ($check) {
            return array('status' => "error", 'message' => "Tài khoản gốc của bạn là $check->user_name");
        }
        $user->role = 2;
        $user->is_default = 1;
        $user->log = $user->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " Update role=2,is_default=1";
        $user->save();
        return array('status' => "success", 'message' => "Thực hiện thành công");
    }

    public function caculateLive(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ProfileController.caculateLive|request=' . json_encode($request->all()));
        //check xem đủ điều kiện để trừ hoặc cộng ko
        $child = User::where("id", $request->id)->first();
        if (!$child) {
            return array('status' => "error", 'message' => "Tài khoản không tồn tại");
        }

        //kiểm tra xem user truyền lên có đúng thuộc customer_id đang đăng nhập không
        if ($user->customer_id != $child->customer_id) {
            return array('status' => "error", 'message' => "Thông tin tài khoản con không chính xác");
        }

        $platform = 1;
        if (isset($request->platform)) {
            $platform = $request->platform;
        }
        if ($platform == 1) {
            $packageCodeKey = "package_code";
            $packageStartDateKey = "package_start_date";
            $packageEndDateKey = "package_end_date";
            $numberKeyLiveKey = "number_key_live";
            $selectPlanKey = "select_plan";
            $dateEndKey = "date_end";
        } else if ($platform == 2) {
            $packageCodeKey = "tiktok_package";
            $packageStartDateKey = "tiktok_start_date";
            $packageEndDateKey = "tiktok_end_date";
            $numberKeyLiveKey = "tiktok_key_live";
            $selectPlanKey = "tiktok_plan";
            $dateEndKey = "tiktok_end_date";
        }else if ($platform == 3) {
            $packageCodeKey = "shopee_package";
            $packageStartDateKey = "shopee_start_date";
            $packageEndDateKey = "shopee_end_date";
            $numberKeyLiveKey = "shopee_key_live";
            $selectPlanKey = "shopee_plan";
            $dateEndKey = "shopee_end_date";
        }

        if ($request->cal == "m") {
            $childLiving = Zliveautolive::where("user_id", $child->user_code)->whereIn("status", [1, 2, 4])->where("platform",$platform)->count();
            if($child->$numberKeyLiveKey==0){
                return array('status' => "error", 'message' => "Số luồng đã bằng 0");
            }
            if ($child->$numberKeyLiveKey - 1 < $childLiving) {
                return array('status' => "error", 'message' => "Bạn phải tắt 1 luồng đang chạy");
            }
            $childKeyLive = $child->$numberKeyLiveKey - 1;
            $parentKeyLive = $user->$numberKeyLiveKey + 1;
        } else if ($request->cal == "p") {

            

            if ($user->$numberKeyLiveKey <= 0) {
                return array('status' => "error", 'message' => "Tài khoản của bạn đã hết luồng live để chia");
            }
            $childKeyLive = $child->$numberKeyLiveKey + 1;
            $parentKeyLive = $user->$numberKeyLiveKey - 1;
        }
        $user->$numberKeyLiveKey = $parentKeyLive;
        $user->save();
        $child->$numberKeyLiveKey = $childKeyLive;
        $child->save();
        return array('status' => "success", 'message' => "Thành công", "child" => $childKeyLive, "parent" => $parentKeyLive);
    }

}
