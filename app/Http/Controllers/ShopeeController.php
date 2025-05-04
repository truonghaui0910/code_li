<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Http\Models\Notify;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zlivecustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function view;

class ShopeeController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.index|request=' . json_encode($request->all()));
        DB::enableQueryLog();

        if (isset($request->note) && $request->note != '') {
            if ($request->isAdmin) {
                $datas = Zliveautolive::whereIn("del_status", [0, 1])->where("platform", 3)->orderBy("id", "desc");
            } else {
                $datas = Zliveautolive::where("del_status", 0)->where("user_id", $user->user_code)->where("platform", 3)->orderBy("id", "desc");
            }
            $datas = $datas->where(function ($q) use ($request) {
                $q->where('note', 'like', '%' . trim($request->note) . '%')->orWhere('id', $request->note);
                if (Utils::containString($request->note, ",")) {
                    $arrCommand = explode(',', $request->note);
                    $q->orWhereIn("id", $arrCommand)->orWhereIn("id", $arrCommand);
                }
            });
        } else {
            $datas = Zliveautolive::where("del_status", 0)->where("user_id", $user->user_code)->where("platform", 3)->orderBy("id", "desc");
        }

        $queries = [];
        $limit = 30;
        if (isset($request->limit)) {
            if ($request->limit <= 2000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->s) && $request->s != '-1') {
            $datas = $datas->where("status", $request->s);
            $queries['status'] = $request->s;
        }
//        $datas = $datas->get();
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo status asc
            $request['sort'] = 'id';
            $request['direction'] = 'desc';
            $queries['sort'] = 'id';
            $queries['direction'] = 'desc';
        }
        $dataCount = $datas->get();
        $datas = $datas->sortable()->paginate($limit)->appends($queries);
        $maxCreated = $user->number_key_live * 5;
        if ($request->isMaxLive) {
            $maxCreated = $user->number_key_live * 30;
        }
        $countRun = 0;
        $countNew = 0;
        $countProcess = 0;
        $countStoped = 0;
        foreach ($dataCount as $d) {
            if ($d->status == 1 || $d->status == 2 || $d->status == 4) {
                $countRun++;
            }
            if ($d->status == 5 || $d->status == 3) {
                $countStoped++;
            }
            if ($d->status == 0) {
                $countNew++;
            }
            if ($d->status == 4) {
                $countProcess++;
            }
        }
        foreach ($datas as $data) {
            $times = [];
            $speeds = [];
            if ($data->status == 2) {
                if ($data->speed != null) {
                    $temps = json_decode($data->speed);
                    foreach ($temps as $temp) {
                        $times[] = $temp->time;
                        $speed = $temp->speed;
                        $speed = str_replace("speed", "", $speed);
                        $speed = str_replace("=", "", $speed);
                        $speed = str_replace(" ", "", $speed);
                        if (floatval($speed) > 1) {
                            $speed = 1;
                        }
                        $speeds[] = $speed;
                    }
                }
            }
            $data->times = json_encode($times);
            $data->speeds = json_encode($speeds);
            $data->estimate = "";
            if ($data->estimate_time_run != 0) {
                $data->estimate = '<br><span class="font-13">Đang download, dự kiến live lúc ' . gmdate("H:i:s", $data->estimate_time_run + 7 * 3600) . '</span>';
            }
        }
//        Log::info(DB::getQueryLog());

        return view('components.shopee', [
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
            "datas" => $datas,
            "countRun" => $countRun,
            "countStoped" => $countStoped,
            "countNew" => $countNew,
            "countProcess" => $countProcess,
            "maxCreated" => $maxCreated,
            "request" => $request,
            "statusLive" => $this->genStatusLive($request),
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|ShopeeController.store|request=' . json_encode($request->all()));
        if ($user->status == 0) {
            return array('status' => "error", 'message' => "Hãy liên hệ Admin để kích hoạt dùng thử");
        }
        $customer = Zlivecustomer::where("customer_id", $user->customer_id)->first();

        //2023/03/15 thêm nhiều lệnh live 1 lúc
        $isMultyLive = 0;
        $arrayKey = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->key_live)));
        $arraySource = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->url_source)));
        $countKey = count($arrayKey);
        if ($countKey > 1 && !in_array("", $arrayKey)) {
            $isMultyLive = 1;
        }


        if ($request->edit_id == null) {
            $countTotalLive = Zliveautolive::where("user_id", $user->user_code)->where("del_status", 0)->where("platform",3)->count();
            if ($countTotalLive >= $user->shopee_key_live * 5) {
                return array('status' => "error", 'message' => "Bạn chỉ được tạo tối đa " . ($user->shopee_key_live * 5) . " luồng live");
            }
            $obj = new Zliveautolive();
        } else {
            $obj = Zliveautolive::where("id", $request->edit_id)->where("del_status", 0)->first();
            if (!$obj || $obj->user_id != $user->user_code) {
                return array('status' => "error", 'message' => "Không tìm thấy thông tin");
            }
            if ($obj->status == 1 || $obj->status == 2 || $obj->status == 4 || $obj->status == 6) {
                return array('status' => "error", 'message' => "Hãy dừng live trước khi sửa thông tin");
            }
        }
        $obj->platform = 3;
        $obj->url_live = $request->url_live;
        $obj->key_live = $request->key_live;
        $obj->url_source = $request->url_source;
        $tmpSource = explode("\n", $obj->url_source);
        $tmpSource2 = array();
        foreach ($tmpSource as $itemSource) {
            $itemSource = str_replace("\r", "", $itemSource);
            $itemSource = trim($itemSource);

            //2024/02/22 xử lý link nguồn drive.usercontent.google.com
            if (Utils::containString($itemSource, "drive.usercontent.google.com")) {
                $driveId = Utils::getDriveID($itemSource);
                $itemSource = "https://drive.google.com/file/d/$driveId/view";
            }
            $tmpSource2[] = $itemSource;
        }
        $obj->url_source = implode("\n", $tmpSource2);
        $obj->note = $request->note;
        $typeSource = 0;
        if (isset($request->type_source)) {
            $typeSource = $request->type_source;
        }
        $sequence = 0;
        if (isset($request->radio_by)) {
            $sequence = $request->radio_by;
        }
        $repeat = 1;
        // 2023/11/20 thêm infinite_loop, 
        // khi hết video ko start lại luống từ đầu mà chỉ kill luồng hết đi để hệ thống tự start luồng mới
        // 
        $infiniteLoop = 0;

        if (isset($request->live_repeat)) {
            $repeat = intval($request->live_repeat);
            if (isset($request->infinite_loop)) {
                $infiniteLoop = 1;
                if (count($arraySource) > 1) {
                    return array('status' => "error", 'message' => "Tiến trình vô hạn chỉ khả dụng cho 1 link nguồn, số link nguồn của bạn hiện tại là " . count($arraySource) . ", bạn muốn dùng 2 link nguồn hãy bỏ tích 'Tiến trình vô hạn'");
                }
            }
        }
        $obj->type_source = $typeSource;
        $obj->seq_source = $sequence; //0: lần lượt, 1: random
        $obj->repeat = $repeat; //0: vô cùng, 1: 1ần
        $dateStart = 0;
        $dateEnd = 0;
        if (!isset($request->radio_time)) {
            if ($request->date_start != null) {
                $dateStart = strtotime("$request->date_start GMT$user->timezone");
            }
            if (isset($request->chk_date_end)) {
                if ($request->date_end != null) {
                    $dateEnd = strtotime("$request->date_end GMT$user->timezone");
                }
            }
        }
        $obj->start_alarm = $dateStart;
        $obj->end_alarm = $dateEnd;
        $obj->create_time = time();
        $obj->status = 0;
        $obj->user_id = $user->user_code;
        $obj->cus_id = $user->customer_id;
        $obj->is_vip = $customer->is_vip;
        $obj->conti_live = 0;
        $obj->is_cheat = 0;
        $obj->action_log = $obj->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name added or edited" . PHP_EOL;
        $obj->infinite_loop = $infiniteLoop;
        $obj->save();
        return array('status' => "success", 'message' => "Success", "live" => $obj);
    }

}
