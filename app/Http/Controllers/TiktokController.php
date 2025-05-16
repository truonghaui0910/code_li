<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Events\Notify;
use App\Http\Models\TiktokDevicePc;
use App\Http\Models\TiktokProductPin;
use App\Http\Models\TiktokProfile;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zlivecustomer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function event;

class TiktokController extends Controller {

//    2023/02/15 tiktok
    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.index|request=" . json_encode($request->all()));
        $tiktokProfile = TiktokProfile::where("username", $user->user_name)->where("del_status", 0)->get(["id", "username", "tiktok_name", "status_cookie", 'violations', "violation_number_stop", "ip", "priority_region", "custom_proxy", "stl_token", "active_v3_info"]);
        if (isset($request->note) && $request->note != '') {
            if ($request->isAdmin) {
                $datas = Zliveautolive::whereIn("del_status", [0, 1])->where("platform", 2)->orderBy("id", "desc");
            } else {
                $datas = Zliveautolive::where("del_status", 0)->where("user_id", $user->user_code)->where("platform", 2)->orderBy("id", "desc");
            }
            $datas = $datas->where(function ($q) use ($request) {
                $q->where('note', 'like', '%' . trim($request->note) . '%')->orWhere('id', $request->note);
                if (Utils::containString($request->note, ",")) {
                    $arrCommand = explode(',', $request->note);
                    $q->orWhereIn("id", $arrCommand)->orWhereIn("id", $arrCommand);
                }
            });
        } else {
            $datas = Zliveautolive::where("del_status", 0)->where("user_id", $user->user_code)->where("platform", 2)->orderBy("id", "desc");
        }
        if (isset($request->s) && $request->s != '-1') {
            $datas = $datas->where("status", $request->s);
        }
        $datas = $datas->get();
//        Log::info(DB::getQueryLog());
        $countRun = 0;
        $countNew = 0;
        $countProcess = 0;
        $countStoped = 0;
        foreach ($datas as $data) {
            if ($data->status == 1 || $data->status == 2) {
                $countRun++;
            }
            if ($data->status == 5 || $data->status == 3) {
                $countStoped++;
            }
            if ($data->status == 0) {
                $countNew++;
            }
            if ($data->status == 4) {
                $countProcess++;
            }
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
            $data->violations_count = 0;
            if ($data->platform == 2) {
                foreach ($tiktokProfile as $tik) {
                    if ($tik->violations != null && $tik->id == $data->tiktok_profile_id) {
                        $data->violations_count = count(json_decode($tik->violations));
                    }
                }
            }
            //
        }

        foreach ($tiktokProfile as $tik) {
            $tik->region = "";
            if ($tik->priority_region == "vn") {
                $tik->region = "Việt Nam";
            } elseif ($tik->priority_region == "us") {
                $tik->region = "US";
            } elseif ($tik->priority_region == "gb") {
                $tik->region = "UK";
            }
            $tik->violations_count = 0;
            if ($tik->violations != null) {
                $tik->violations_count = count(json_decode($tik->violations));
            }
            $tik->status_run = 0;
            foreach ($datas as $data) {
                if ($data->tiktok_profile_id == $tik->id) {
                    $data->tiktok_profile_name = $tik->tiktok_name;
                    if ($data->status == 1 || $data->status == 2 || $data->status == 4) {
                        $tik->status_run = 2;
                    } else if ($data->status == 3 || $data->status == 0) {
                        $tik->status_run = 1;
                    }
                }
            }


            $tik->status_v3 = "";
            $tik->v3_tooltip = "";


            if ($tik->active_v3_info != null) {
                $json = json_decode($tik->active_v3_info);
                if (isset($json->status)) {
                    $tik->status_v3 = $json->status;
                }
                if ($tik->status_v3 == 'error') {
                    $tik->v3_tooltip = $json->message;
                }
            }
            if ($tik->stl_token != null) {
                $tik->status_v3 = "done";
            }
//            Log::info(json_encode($tik));
        }
        return view("components.tiktok", [
            "tiktokProfile" => $tiktokProfile,
            "datas" => $datas,
            "countRun" => $countRun,
            "countStoped" => $countStoped,
            "countNew" => $countNew,
            "countProcess" => $countProcess,
            "request" => $request,
            "tiktokAccount" => $this->loadTiktokChannel($request),
            "tiktokTopic" => $this->loadTiktokTopic($request),
            "statusLive" => $this->genStatusLive($request),
        ]);
    }

    public function find($id) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.find|request=$id");

        $tiktok = TiktokProfile::where("username", $user->user_name)->where("id", $id)->where("status_cookie", 0)->first(["id", "username", "tiktok_name", "status_cookie"]);
        if ($tiktok) {
            return array("status" => "success", "message" => "Success", "data" => $tiktok);
        }
        return array("status" => "error", "message" => "Not found Data");
    }

    public function load(Request $request) {
        $platform = $request->header('platform');
        if ($platform != "Autolive") {
            return ["message" => "Wrong system!"];
        }
        $locker = new Locker(6788);
        $locker->lock();
        $tiktok = TiktokProfile::where("id", $request->id)->first();
        if ($tiktok) {
            return array("status" => "success", "message" => "Success", "data" => $tiktok);
        }
        return array("status" => "error", "message" => "Not found Data");
    }

    public function update(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.update|request=" . json_encode($request->all()));
        if (isset($request->id)) {
            $tiktok = TiktokProfile::find($request->id);
            if (!$tiktok) {
                return array("status" => "error", "message" => "Not found $request->id");
            }
            if ($request->action == "delete") {
                if ($user->user_name != $tiktok->username) {
                    return array("status" => "error", "message" => "Bạn không có quyền");
                }
                $live = Zliveautolive::where("platform", 2)->where("tiktok_profile_id", $tiktok->id)->where("del_status", 0)->first();
                if ($live) {
                    return array("status" => "error", "message" => "Tài khoản tiktok này đã có lệnh. Bạn hãy xóa lệnh $live->id trước");
                }
                $tiktok->del_status = 1;
            }
            if (isset($request->stl_token)) {
                $tiktok->stl_token = trim($request->stl_token);
                if ($tiktok->active_v3_info == null) {
                    $tiktok->active_v3_info = json_encode(['status' => "done", 'time' => time(), "action_time" => time()]);
                } else {
                    $v3 = json_decode($tiktok->active_v3_info);
                    $v3->status = "done";
                    $v3->action_time = time();
                    $tiktok->active_v3_info = json_encode($v3);
                }
                //thông báo cho ng dùng là kênh v3 đã được duyệt
                event(new Notify(1, [$tiktok->username], "Tài khoản $tiktok->id $tiktok->tiktok_name đã được duyệt v3"));
            }
            if (isset($request->v3_kicked)) {
                if ($tiktok->active_v3_info == null) {
                    $tiktok->active_v3_info = json_encode(['status' => "kicked", 'time' => time(), "action_time" => time()]);
                } else {
                    $v3 = json_decode($tiktok->active_v3_info);
                    $v3->status = "kicked";
                    $v3->action_time = time();
                    $tiktok->active_v3_info = json_encode($v3);
                }
            }

            if (isset($request->v3_error)) {
                if ($tiktok->active_v3_info == null) {
                    $tiktok->active_v3_info = json_encode(['status' => "error", 'time' => time(), "action_time" => time(), "message" => "error"]);
                } else {
                    $v3 = json_decode($tiktok->active_v3_info);
                    $v3->status = "error";
                    $v3->action_time = time();
                    $v3->message = $request->error;
                    $tiktok->active_v3_info = json_encode($v3);
                }
                $tiktok->stl_token = null;
            }

            $tiktok->update_time = Utils::timeToStringGmT7(time());
            $tiktok->save();
            return array("status" => "success", "message" => "Success");
        }
        return array("status" => "error", "message" => "Not found $request->id");
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.store|request=" . json_encode($request->all()));

        $countProfile = TiktokProfile::where("username", $user->user_name)->where("del_status", 0)->count();
        $maxKeyLive = $user->tiktok_key_live * 10;
        if ($countProfile >= $maxKeyLive) {
            return array("status" => "error", "message" => "Bạn chỉ được thêm $maxKeyLive tài khoản tiktok");
        }
        if ($request->tiktok_name == null || $request->tiktok_name == "") {
            return array("status" => "error", "message" => "Bạn phải nhập tên tài khoản Tiktok");
        }
        $tiktokName = Utils::slugify($request->tiktok_name);
//        $check = TiktokProfile::where("tiktok_name", $tiktokName)->where("del_status", 0)->first();
//        if ($check) {
//            return array("status" => "error", "message" => "Tên tài khoản Tiktok đã tồn tại");
//        }
        $profile = new TiktokProfile();
        $profile->username = $user->user_name;
        $profile->tiktok_name = $tiktokName;
        $profile->create_time = Utils::timeToStringGmT7(time());
//        $profile->install_id = rand(1, 7) . Utils::randomDigit(18);
//        $profile->device_id = rand(1, 7) . Utils::randomDigit(18);
        $profile->priority_region = $request->region;
        $profile->violation_number_stop = $request->violation_number_stop;
        if (isset($request->chk_proxy)) {
            $proxyIp = trim($request->proxy_ip);
            $proxyPort = trim($request->proxy_port);
            if ($proxyIp == null || $proxyIp == "") {
                return array("status" => "error", "message" => "IP không được để trống");
            }
            if ($proxyPort == null || $proxyPort == "") {
                return array("status" => "error", "message" => "Port không được để trống");
            }

            $customProxy = (object) [
                        "ip" => $proxyIp,
                        "port" => $proxyPort,
            ];
            $proxyText = "$proxyIp:$proxyPort";
            $proxyauth = null;
            if (isset($request->chk_proxy_pass)) {
                $proxyUser = trim($request->proxy_user);
                $proxyPasss = trim($request->proxy_pass);
                if ($proxyUser == null || $proxyUser == "") {
                    return array("status" => "error", "message" => "Tài khoản proxy không được để trống");
                }
                if ($proxyPasss == null || $proxyPasss == "") {
                    return array("status" => "error", "message" => "Mật khẩu proxy không được để trống");
                }
                $customProxy->user = $proxyUser;
                $customProxy->pass = $proxyPasss;
                $proxyauth = "$proxyUser:$proxyPasss";
            }
            //kiểm tra xem proxy có kết nối được không
            $httpCode = RequestHelper::checkProxy($proxyText, $proxyauth);
            if ($httpCode != 200) {
                return array("status" => "error", "message" => "Không thể kết nối đến proxy, vui lòng kiểm tra lại thông tin");
            }
            $profile->custom_proxy = json_encode($customProxy);
            $profile->ip = $proxyIp;
        }

        //2024/04/15 assign device cho tài khoản
        $devicePc = TiktokDevicePc::where("status", 0)->orderByRaw('RAND()')->first();
        if (!$devicePc) {
            return array("status" => "error", "message" => "Có lỗi khi lấy thiết bị");
        }
        $devicePc->status = 1;
        $devicePc->country = $request->region;
        $devicePc->updated = Utils::timeToStringGmT7(time());

        Log::info("$user->user_name /home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py create_device $devicePc->id");
        $tmp = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py create_device $devicePc->id");
        $shell = trim($tmp);
        Log::info("$user->user_name Shell " . $shell);
        if ($shell == null || $shell == "") {
            return array("status" => "error", "message" => "Có lỗi khi lấy thiết bị");
        }

        $devideInfo = json_decode($shell);
        if (empty($devideInfo->device_id) || $devideInfo->device_id == 0) {
            return array("status" => "error", "message" => "Không tìm thấy thiết bị");
        }
        $profile->device_id = $devideInfo->device_id;
        $profile->install_id = $devideInfo->install_id;
        $profile->save();

        $devicePc->device_id = $devideInfo->device_id;
        $devicePc->install_id = $devideInfo->install_id;
        $devicePc->profile_id = $profile->id;
        $devicePc->save();


        $profile->region = "";
        if ($profile->priority_region == "vn") {
            $profile->region = "Việt Nam";
        } elseif ($profile->priority_region == "us") {
            $profile->region = "US";
        } elseif ($profile->priority_region == "gb") {
            $profile->region = "UK";
        }
        return array("status" => "success", "message" => "Thành công", "data" => $profile);
    }

    public function getCookie(Request $request) {
        $platform = $request->header('platform');
        if ($platform != "Autolive") {
            return ["message" => "Wrong system!"];
        }
        $locker = new Locker(6789);
        $locker->lock();
        $data = TiktokProfile::where("status_session", 0)->where("del_status", 0)->where("status_cookie", 1)->first(["id", "tiktok_name", "cookie"]);
        if ($data) {
            $data->status_session = 2;
            $data->save();
            return $data;
        }
        return "{}";
    }

    public function tiktokSyncCookie(Request $request) {
        Log::info("TiktokController.tiktokSyncCookie|request=" . json_encode($request->all()));
        $check = TiktokProfile::where("tiktok_name", $request->name)->where("username", $request->user)->where("del_status", 0)->first();
        if ($check) {
            if ($check->status_cookie == 1) {
                return 0;
            }
            $check->tiktok_name = trim($request->name);
            $check->username = $request->user;
            if (isset($request->data)) {
                $check->cookie = $request->data;
                $check->status_cookie = 1;
            }
            $check->update_time = Utils::timeToStringGmT7(time());
            $check->last_reset_device = time();
            $check->save();

            //check account info
            Log::info("$request->user add studio cookie /home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $check->id");
            $tmp = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $check->id");
            $shell = trim($tmp);
            Log::info("$request->user add studio cookie Shell " . $shell);
            if ($shell == null || $shell == "") {
                $check->status_cookie = 0;
                $check->save();
            }
            $json = json_decode($shell);
            if (!empty($json->data->user_name)) {

                //2024/05/05 kiểm tra xem tài khoản tiktok có được add vào tài khoản test nào chưa
                $us = User::where("user_name", $request->user)->first();
                if ($us && $us->tiktok_package == 'TIKTOKTEST') {
                    $check2 = TiktokProfile::where("tiktok_account", $json->data->user_name)->first();
                    if ($check2) {
                        $check->status_cookie = 0;
                        $check->cookie = null;
                        $check->save();
                        return 0;
                    }
                }
                $check->tiktok_account = $json->data->user_name;
                $check->save();
            } else {
                $check->status_cookie = 0;
                $check->cookie = null;
                $check->save();
                return 0;
            }

            if ($check->custom_proxy == null) {
                //thêm ip

                for ($i = 1; $i <= 100; $i++) {
                    Log::info($request->user . " " . getmypid() . " get IP");
                    $ip = Utils::getIp($check->priority_region);
                    $ipCheck = TiktokProfile::where("ip", $ip)->where("del_status", 0)->first();
                    if (!$ipCheck) {
                        $check->ip = $ip;
                        $check->last_reset_ip = time();
                        $check->save();
                        break;
                    }
                }
            }

            return 1;
        }
        return 0;
    }

    public function saveLive(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.saveLive|request=" . json_encode($request->all()));
        if ($user->status == 0) {
            return array('status' => "error", 'message' => "Hãy liên hệ Admin để kích hoạt dùng thử");
        }

        if (!isset($request->title) || $request->title == "") {
            return array("status" => "error", "message" => "Hãy nhập Tiêu đề");
        }
        if (mb_strlen(trim($request->title), 'UTF-8') > 32) {
            return array("status" => "error", "message" => "Tiêu đề tối đa 32 ký tự");
        }
        if (!isset($request->url_source) || $request->url_source == "") {
            return array("status" => "error", "message" => "Hãy nhập Link Nguồn");
        }
        $arraySource = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->url_source)));

        $isMultiLive = isset($request->is_multi_live) && $request->is_multi_live;
        $duration = isset($request->duration) ? intval($request->duration) : 0;
        if ($isMultiLive && $duration <= 0) {
            return array("status" => "error", "message" => "Hãy nhập thời lượng cho mỗi live");
        }

        if ($isMultiLive) {
            if (!isset($request->tiktok_account) || $request->tiktok_account == "-1") {
                return array("status" => "error", "message" => "Hãy chọn Tài khoản tiktok");
            }
            $tiktok = TiktokProfile::where("id", $request->tiktok_account);
            if (!$request->isAdmin) {
                $tiktok = $tiktok->where("username", $user->user_name);
            }
            $tiktok = $tiktok->first();
            if (!$tiktok) {
                return array("status" => "error", "message" => "Không tìm thấy Tài khoản tiktok");
            }
            $customer = Zlivecustomer::where("customer_id", $user->customer_id)->first();
            $tiktok->title = trim($request->title);
            $tiktok->topic = $request->topic;
            $tiktok->save();

            $createdIds = [];
            $startTime = 0;

            // Lấy thời gian bắt đầu từ request hoặc thời gian hiện tại
            if (!isset($request->radio_time) && $request->date_start != null) {
                $startTime = strtotime("$request->date_start GMT$user->timezone");
            } else {
                $startTime = time();
            }

            foreach ($arraySource as $index => $sourceUrl) {
                $sourceUrl = trim($sourceUrl);
                if (empty($sourceUrl)) {
                    continue;
                }

                $obj = new Zliveautolive();
                $obj->tiktok_profile_id = $request->tiktok_account;
                $obj->command = $request->live_type;
                $obj->url_source = $sourceUrl;
                $obj->note = trim($request->title);

                // Xử lý link nguồn drive
                if (Utils::containString($sourceUrl, "drive.usercontent.google.com")) {
                    $driveId = Utils::getDriveID($sourceUrl);
                    $obj->url_source = "https://drive.google.com/file/d/$driveId/view";
                }

                // Thiết lập các thuộc tính
                $typeSource = isset($request->type_source) ? $request->type_source : 0;
                $sequence = isset($request->radio_by) ? $request->radio_by : 0;
                $repeat = isset($request->live_repeat) ? intval($request->live_repeat) : 1;

                $obj->type_source = $typeSource;
                $obj->seq_source = $sequence; //0: lần lượt, 1: random
                $obj->repeat = 1; //0: vô cùng, 1: 1ần
                // Tính toán thời gian bắt đầu và kết thúc cho mỗi lệnh
                if ($index == 0) {
                    $obj->start_alarm = $startTime;
                } else {
                    // Lệnh sau sẽ bắt đầu sau khi lệnh trước kết thúc 5 phút
                    $delay = isset($request->delay) ? intval($request->delay) : 5;
                    $obj->start_alarm = $startTime + ($duration * 60) + ($delay * 60);
                }

                // Cập nhật startTime cho lệnh tiếp theo
                $startTime = $obj->start_alarm;

                // Thời gian kết thúc nếu có
                if ($duration > 0) {
                    $obj->end_alarm = $obj->start_alarm + ($duration * 60);
                }

                $obj->create_time = time();
                $obj->status = 0;
                $obj->user_id = $user->user_code;
                $obj->cus_id = $user->customer_id;
                $obj->is_vip = $customer->is_vip;
                $obj->conti_live = 0;
                $obj->is_cheat = 0;
                $obj->platform = 2;
                $obj->action_log = Utils::timeToStringGmT7(time()) . " $user->user_name added in multi-live mode" . PHP_EOL;
                $obj->infinite_loop = 0; // Không cho phép vô hạn trong chế độ multi_live
                $obj->is_report = 2; 
                $obj->save();

                $createdIds[] = $obj->id;
            }

            return array(
                "status" => "success",
                "message" => "Đã tạo " . count($createdIds) . " lệnh live thành công",
                "ids" => $createdIds
            );
        } else {

            if ($request->edit_id == null) {
                $tiktok = TiktokProfile::where("id", $request->tiktok_account);
                if (!$request->isAdmin) {
                    $tiktok = $tiktok->where("username", $user->user_name);
                }
                $tiktok = $tiktok->first();
                if (!$tiktok) {
                    return array("status" => "error", "message" => "Không tìm thấy Tài khoản tiktok");
                }
                $countTotalLive = Zliveautolive::where("user_id", $user->user_code)->where("del_status", 0)->where("platform", 2)->count();
                if ($countTotalLive >= $user->tiktok_key_live) {
                    return array('status' => "error", 'message' => "Bạn chỉ được tạo tối đa " . ($user->tiktok_key_live) . " luồng live tiktok");
                }
                if (!isset($request->tiktok_account) || $request->tiktok_account == "-1") {
                    return array("status" => "error", "message" => "Hãy chọn Tài khoản tiktok");
                }
                //kiểm tra tiktok_account này đã tạo lệnh chưa
                $check = Zliveautolive::where("tiktok_profile_id", $request->tiktok_account)->where("del_status", 0)->first();
                if ($check) {
                    return array("status" => "error", "message" => "Bạn đã tạo lệnh live cho Tài khoản tiktok nay rồi ($check->id)");
                }

                $obj = new Zliveautolive();
                $obj->tiktok_profile_id = $request->tiktok_account;
            } else {
                $obj = Zliveautolive::where("id", $request->edit_id)->where("del_status", 0)->first();
                if (!$obj || $obj->user_id != $user->user_code) {
                    return array('status' => "error", 'message' => "Không tìm thấy thông tin");
                }
                if ($obj->status == 1 || $obj->status == 2 || $obj->status == 4) {
                    return array('status' => "error", 'message' => "Hãy dừng live trước khi sửa thông tin");
                }
                $tiktok = TiktokProfile::where("id", $obj->tiktok_profile_id);
                if (!$request->isAdmin) {
                    $tiktok = $tiktok->where("username", $user->user_name);
                }
                $tiktok = $tiktok->first();
                if (!$tiktok) {
                    return array("status" => "error", "message" => "Không tìm thấy Tài khoản tiktok");
                }
            }
            $tiktok->title = trim($request->title);
            $tiktok->topic = $request->topic;
            $tiktok->save();
            $customer = Zlivecustomer::where("customer_id", $user->customer_id)->first();
            //2024/02/27 bổ sung live type
            $obj->command = $request->live_type;
            $obj->url_source = $request->url_source;
            $obj->note = $request->title;
            $countUrlSource = 0;

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
            $typeSource = 0;
            if (isset($request->type_source)) {
                $typeSource = $request->type_source;
            }
            $sequence = 0;
            if (isset($request->radio_by)) {
                $sequence = $request->radio_by;
            }
            $repeat = 1;
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
            $obj->platform = 2;
        if ($countUrlSource < 2 && $obj->repeat == 0) {
                $obj->is_cheat = 1;
            }
            $obj->action_log = $obj->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name added or edited" . PHP_EOL;
            $obj->infinite_loop = $infiniteLoop;
            $obj->save();
            return array("status" => "success", "message" => "Thành công");
        }
    }

    public function tiktokProductList(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.tiktokProductList|request=" . json_encode($request->all()));
        $live = Zliveautolive::where("id", $request->id)->first();
        if (!$live) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->id");
        }
        if (!$request->isAdmin) {
            if ($user->user_code != $live->user_id) {
                return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->id trên tài khoản của bạn");
            }
        }
        $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_6_capt.py product_list $live->tiktok_profile_id";
        Log::info("$user->user_name cmd:" . $cmd);
        $tmp = shell_exec($cmd);
//        Log::info("$user->user_name tmp:" . $tmp);
        $shell = trim($tmp);
        if ($shell == null || $shell == "") {
            return array("status" => "error", "message" => "Không lấy được danh sách sản phẩm");
        }
        $productsText = "";
        $products = [];
        $pro = json_decode($shell);
        if ($pro->code == 0 && !empty($pro->data->products)) {
            $products = $pro->data->products;
        }
        foreach ($products as $pro) {
            $productsText .= "https://shop.tiktok.com/view/product/$pro->product_id?region=VN&local=en" . PHP_EOL;
        }
        return array("status" => "success", "id" => $request->id, "products" => $products, "productsText" => $productsText);
    }

    public function tiktokProductAdd(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.tiktokProductAdd|request=" . json_encode($request->all()));
        $live = Zliveautolive::where("id", $request->live_id)->first();
        if (!$live) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->live_id");
        }
        if (!$request->isAdmin) {
            if ($user->user_code != $live->user_id) {
                return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->live_id trên tài khoản của bạn");
            }
        }
        $arrayLink = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->product_link)));
        $count = 0;
        $results = [];
        foreach ($arrayLink as $data) {
            $link = trim($data);
            $cmd = "/home/tiktok_tools/env/bin/python /home/v21.autolive.vip/public_html/python.py product_add  $live->tiktok_profile_id $live->room_id \"$link\"";
            Log::info("$user->user_name|tiktokProductAdd|cmd:" . $cmd);
            $tmp = shell_exec($cmd);
            Log::info("$user->user_name|tiktokProductAdd|tmp:" . $tmp);
            $shell = trim($tmp);
            $status = "error";
            if ($shell != null && $shell != "") {
                if (Utils::containString($shell, "permission")) {
                    return array("status" => "error", "message" => "Tài khoản của bạn cần kích hoạt Tiktok Shop! Xin Cảm Ơn", "result" => $results);
                }
                $pro = json_decode($shell);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Thử chuyển ' thành " một cách an toàn
                    $fixedShell = preg_replace_callback("/'([^']*)'/", function ($matches) {
                        return '"' . addslashes($matches[1]) . '"';
                    }, $shell);

                    $pro = json_decode($fixedShell);
                }
                Log::info(json_encode($pro));
                if ($pro->code == 0 && $pro->message == 'success') {
                    $count++;
                    $status = "success";
                }
            }
            $results[] = (object) [
                        "link" => $link,
                        "status" => $status
            ];
        }

        return array("status" => "success", "message" => "Success", "result" => $results);
    }

    public function tiktokProductPin(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.tiktokProductPin|request=" . json_encode($request->all()));
        $live = Zliveautolive::where("id", $request->id)->first();
        if (!$live) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->id");
        }
        if (!$request->isAdmin) {
            if ($user->user_code != $live->user_id) {
                return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->id trên tài khoản của bạn");
            }
        }
        $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_6_capt.py product_pin  $live->tiktok_profile_id $live->room_id $request->product_id";
        Log::info("$user->user_name cmd:" . $cmd);
        $tmp = shell_exec($cmd);
        Log::info("$user->user_name tmp:" . $tmp);
        $shell = trim($tmp);
        if ($shell == null || $shell == "") {
            return array("status" => "error", "message" => "Pin sản phẩm bị lỗi");
        }
        $pro = json_decode($shell);
        if ($pro->code == 0) {
            return array("status" => "success", "message" => "Thực hiện thành công", "id" => $request->id);
        }
        return array("status" => "error", "message" => "Đã có lỗi xảy ra", "id" => $request->id);
    }

    //2023/03/27 tự động pin sản phẩm
    public function tiktokProductPinSetting(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.tiktokProductPinSetting|request=" . json_encode($request->all()));
        $live = Zliveautolive::where("id", $request->job_id)->first();
        if (!$live) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->job_id");
        }
        if ($live->status != 2) {
            return array("status" => "error", "message" => "Luồng live của bạn phải đang chạy");
        }
        if (!$request->isAdmin) {
            if ($user->user_code != $live->user_id) {
                return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->id trên tài khoản của bạn");
            }
        }
        if (isset($request->is_auto_pin)) {
            $listProduct = json_decode($request->product_ids);
            if (count($listProduct) == 0) {
                return array("status" => "error", "message" => "Không danh sách sản phẩm");
            }
            $minute = trim($request->minute_pin);
//            if (!is_int($minute) || $minute < 0) {
//                return array("status" => "error", "message" => "Số phút phải là số nguyên lớn hơn 0");
//            }
            $tiktokPin = TiktokProductPin::where("job_id", $request->job_id)->first();
            if (!$tiktokPin) {
                $tiktokPin = new TiktokProductPin();
            }
            $tiktokPin->job_id = $live->id;
            $tiktokPin->product_ids = $request->product_ids;
            $tiktokPin->product_ids_run = null;
            $tiktokPin->status = 1;
            $tiktokPin->created = Utils::timeToStringGmT7(time());
            $tiktokPin->last_update = Utils::timeToStringGmT7(time());
            $tiktokPin->interval = $minute;
            $tiktokPin->next_time_run = time();
            $tiktokPin->save();
        } else {
            $tiktokPin = TiktokProductPin::where("job_id", $request->job_id)->first();
            if ($tiktokPin) {
                $tiktokPin->status = 0;
                $tiktokPin->last_update = Utils::timeToStringGmT7(time());
                $tiktokPin->save();
            }
        }

        return array("status" => "success", "message" => "Success");
    }

    public function autoPinProduct() {

        $datas = TiktokProductPin::where("status", 1)->where("next_time_run", "<=", time())->get();
        foreach ($datas as $data) {
            $live = Zliveautolive::where("id", $data->job_id)->where("status", 2)->first();
            if (!$live) {
                $data->status = 0;
                $data->last_update = Utils::timeToStringGmT7(time());
                $data->save();
                continue;
            }
            $productIdsRun = $data->product_ids_run == null ? [] : json_decode($data->product_ids_run);
            $productIds = json_decode($data->product_ids);
            if (count($productIds) == 0) {
                $productIds = $productIdsRun;
                $productIdsRun = [];
            }
            $productId = array_shift($productIds);
            array_push($productIdsRun, $productId);
            $data->last_update = Utils::timeToStringGmT7(time());
            $data->next_time_run = time() + $data->interval * 60;
            $data->product_ids = json_encode($productIds);
            $data->product_ids_run = json_encode($productIdsRun);
            $data->save();

            //thực hiện lệnh pin
            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_6_capt.py product_pin  $live->tiktok_profile_id $live->room_id $productId";
            Log::info("autoPinProduct cmd:" . $cmd);
            $tmp = shell_exec($cmd);
//            Log::info("autoPinProduct tmp:" . $tmp);
            $shell = trim($tmp);
            if ($shell == null || $shell == "") {
                $data->status_run = 0;
                $data->save();
            }
            $pro = json_decode($shell);
            if (!empty($pro->code) && $pro->code == 0) {
                $data->status_run = 1;
                $data->save();
            }
        }
    }

    public function tiktokProductDelete(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.tiktokProductDelete|request=" . json_encode($request->all()));
        $live = Zliveautolive::where("id", $request->id)->first();
        if (!$live) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->id");
        }
        if (!$request->isAdmin) {
            if ($user->user_code != $live->user_id) {
                return array("status" => "error", "message" => "Không tìm thấy thông tin lệnh $request->id trên tài khoản của bạn");
            }
        }
        $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_6_capt.py product_delete $live->tiktok_profile_id $request->product_id";
        Log::info("$user->user_name cmd:" . $cmd);
        $tmp = shell_exec($cmd);
        Log::info("$user->user_name  tmp:" . $tmp);
        $shell = trim($tmp);
        if ($shell == null || $shell == "") {
            return array("status" => "error", "message" => "Xóa sản phẩm bị lỗi");
        }
        $pro = json_decode($shell);
        if ($pro->code == 0) {
            return array("status" => "success", "message" => "Thực hiện thành công", "id" => $request->id);
        }
        return array("status" => "error", "message" => "Đã có lỗi xảy ra", "id" => $request->id);
    }

    //2023/03/24 live from tiktok live
    //lấy link diriect từ link live tiktok
    public function callbackTiktokLive(Request $request) {
//        Log::info("TiktokController.callbackTiktokLive|request=id=$request->id,ref_id=$request->studio_id,gmail=$request->gmail,status=$request->status");
        Log::info("TiktokController.callbackTiktokLive|request=" . json_encode($request->all()));
        $live = Zliveautolive::find($request->studio_id)->where("del_status", 0)->where("status", 6)->first();
        if (!$live) {
            Log::info("TiktokController.callbackTiktokLive|not found $request->studio_id");
            return 0;
        }

        if ($request->status == 4 || $request->result == null || $request->result == "") {
            $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " system cant get direct link jobId=$request->id" . PHP_EOL;
            $live->status = 0;
            $live->is_auto_start = 0;
            $live->log = "Có lỗi khi lấy link direct";
            $live->save();
            return 0;
        }

        $result = json_decode($request->result);
        if (empty($result->result)) {
            $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " system cant get direct link jobId=$request->id" . PHP_EOL;
            $live->log = "Có lỗi khi lấy link direct";
            $live->is_auto_start = 0;
            $live->status = 0;
            $live->save();
            return 0;
        }
        $link = $result->result;
        $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " system update status=1,source_old=$live->url_source source_new=$link" . PHP_EOL;
        $live->url_source = $link;
        $live->status = 1;
        $live->save();
        return 1;
    }

    //2023/01/16 thay đổi thiết bị
    public function renewDevice(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.renewDevice|request=" . json_encode($request->all()));

        if ($request->isAdmin) {
            $profile = TiktokProfile::where("id", $request->id)->first();
        } else {
            $profile = TiktokProfile::where("id", $request->id)->where("username", $user->user_name)->first();
        }
        if (!$profile) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin tài khoản");
        }
        if (time() - $profile->last_reset_device < 30 * 60) {
            return array("status" => "error", "message" => "Thời gian đổi thiết bị phải cách nhau 30 phút, bạn có thể đổi thiết bị vào lúc  " . Utils::timeToStringGmT7($profile->last_reset_device + (30 * 60)) . " GMT+7");
        }
        $check = Zliveautolive::where("tiktok_profile_id", $profile->id)->whereIn("status", [1, 2, 4])->first();
        if ($check) {
            return array("status" => "error", "message" => "Hãy dừng luồng live $check->id trước khi thực hiện đổi thiết bị");
        }
        sleep(rand(5, 10));
//        $profile->install_id = rand(1, 7) . Utils::randomDigit(18);
//        $profile->device_id = rand(1, 7) . Utils::randomDigit(18);
        $devicePc = TiktokDevicePc::where("status", 0)->orderByRaw('RAND()')->first();
        $devicePc->status = 1;
        $devicePc->country = $profile->priority_region;
        $devicePc->profile_id = $profile->id;
        $devicePc->updated = Utils::timeToStringGmT7(time());
        $devicePc->save();

        Log::info("$user->user_name /home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py create_device $devicePc->id");
        $tmp = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py create_device $devicePc->id");
        $shell = trim($tmp);
        Log::info("$user->user_name Shell " . $shell);
        if ($shell == null || $shell == "") {
            return array("status" => "error", "message" => "Có lỗi khi lấy thiết bị");
        }

        $devideInfo = json_decode($shell);
        if (empty($devideInfo->device_id) || $devideInfo->device_id == 0) {
            return array("status" => "error", "message" => "Không tìm thấy thiết bị");
        }
        $profile->device_id = $devideInfo->device_id;
        $profile->install_id = $devideInfo->install_id;
        $profile->save();

        $devicePc->device_id = $devideInfo->device_id;
        $devicePc->install_id = $devideInfo->install_id;
        $devicePc->save();




        $profile->last_reset_device = time();
        $profile->save();
        return array("status" => "success", "message" => "Thành công");
    }

    public function renewIp(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.renewIp|request=" . json_encode($request->all()));

        if ($request->isAdmin) {
            $profile = TiktokProfile::where("id", $request->id)->first();
        } else {
            $profile = TiktokProfile::where("id", $request->id)->where("username", $user->user_name)->first();
        }
        if (!$profile) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin tài khoản");
        }
        if (time() - $profile->last_reset_ip < 30 * 60) {
            return array("status" => "error", "profile" => $profile, "message" => "Thời gian đổi IP phải cách nhau 30 phút, bạn có thể đổi IP vào lúc  " . Utils::timeToStringGmT7($profile->last_reset_ip + (30 * 60)) . " GMT+7");
        }
        $check = Zliveautolive::where("tiktok_profile_id", $profile->id)->whereIn("status", [1, 2, 4])->first();
        if ($check) {
            return array("status" => "error", "profile" => $profile, "message" => "Hãy dừng luồng live $check->id trước khi thực hiện đổi IP");
        }
        if ($profile->custom_proxy != null) {
            return array("status" => "error", "message" => "Tài khoản này đã cài đặt proxy riêng, không thể thay đổi IP");
        }
        sleep(rand(10, 15));
        for ($i = 1; $i <= 100; $i++) {
            Log::info(getmypid() . " get IP");
            $ip = Utils::getIp($profile->priority_region);
            $ipCheck = TiktokProfile::where("ip", $ip)->where("del_status", 0)->first();
            if (!$ipCheck) {
                $profile->ip = $ip;
                $profile->last_reset_ip = time();
                $profile->save();
                break;
            }
        }
        return array("status" => "success", "message" => "Thành công", "profile" => $profile);
    }

    public function parseCookie($data) {

//        // Sample cookies string
////        $cookiesString = "SAPISID=qo5SZ2owtCFdO6j-/A2sR-kIFqobow4bGE;__Secure-3PAPISID=qo5SZ2owtCFdO6j-/A2sR-kIFqobow4bGE";
////        $cookiesString = trim($request->cookie_string);
//        // Split the cookies string into individual cookies
//        $cookies = explode(';', $cookiesString);
//
//        $result = array();
//
//        // Parse each cookie to extract its name and value
//        foreach ($cookies as $cookie) {
//            // Extract name and value of the cookie
//            $cookieParts = explode('=', $cookie, 2);
//            $cookieName = trim($cookieParts[0]);
//            $cookieValue = isset($cookieParts[1]) ? trim($cookieParts[1]) : '';
//
//            // Add cookie to result array
//            $result[$cookieName] = $cookieValue;
//        }
//
//        // Convert result array to JSON
//        $jsonResult = json_encode($result);


        $xdata = json_decode($data, true);

// Initialize an empty dictionary (associative array)
        $dict = array();

// Iterate through each item in $xdata
        if (!empty($xdata)) {
            foreach ($xdata as $item) {
                // Assign 'value' to the corresponding 'name' key in the dictionary
                $dict[$item['name']] = $item['value'];
            }
        }

        return $dict;
    }

    public function addCookie(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.addCookie|request=" . json_encode($request->all()));
        $cookie = $this->parseCookie(trim($request->cookie_content));
        $profile = TiktokProfile::where("id", $request->profile_cookie_id)->where("username", $user->user_name)->first();
        if (!$profile) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin tài khoản");
        }
        if ($profile->status_cookie == 1) {
            return array("status" => "error", "message" => "Tài khoản này đã được add cookie rồi");
        }
        $profile->update_time = Utils::timeToStringGmT7(time());
        $profile->cookie = json_encode($cookie);
        $profile->save();
        //check cookie
        Log::info("$user->user_name /home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $profile->id");
        $tmp = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $profile->id");
        $shell = trim($tmp);
        Log::info("$user->user_name Shell " . $shell);
        if ($shell == null || $shell == "") {
            return array("status" => "error", "message" => "Cookie không hợp lệ");            //thêm ip
        }

        $json = json_decode($shell);
        if (!empty($json->message)) {
            if ($json->message == 'success') {
                $profile->status_cookie = 1;
                if (!empty($json->data->user_name)) {
                    $profile->tiktok_account = $json->data->user_name;

                    //2024/05/05 kiểm tra xem tài khoản tiktok có được add vào tài khoản test nào chưa
                    if ($user->tiktok_package == 'TIKTOKTEST') {
                        $check = TiktokProfile::where("tiktok_account", $profile->tiktok_account)->first();
                        if ($check) {
                            $profile->status_cookie = 0;
                            $profile->cookie = null;
                            $profile->save();
                            return array("status" => "error", "message" => "Tài khoản này đã được thêm vào hệ thống rồi, vui lòng mua gói để được sử dụng");
                        }
                    }
                } else {
                    $profile->status_cookie = 0;
                    $profile->cookie = null;
                    $profile->save();
                    return array("status" => "error", "message" => "Tài khoản này không đủ điều kiện để thêm vào tool");
                }
                if ($profile->custom_proxy == null) {
                    for ($i = 1; $i <= 100; $i++) {
                        Log::info($user->user_name . " " . getmypid() . " get IP");
                        $ip = Utils::getIp($profile->priority_region);
                        $ipCheck = TiktokProfile::where("ip", $ip)->where("del_status", 0)->first();
                        if (!$ipCheck) {
                            $profile->ip = $ip;
                            $profile->save();
                            break;
                        }
                    }
                }
                $profile->save();
                return array("status" => "success", "message" => "Thành công");
            } else {
                return array("status" => "error", "message" => $json->message);
            }
        } else {
            return array("status" => "error", "message" => "Cookie không hợp lệ");
        }
    }

    public function scanAccountTiktok() {
        $pid = getmypid();
        $datas = TiktokProfile::where("del_status", 0)->where("status_cookie", 1)->whereNull("tiktok_account")->get();
        foreach ($datas as $profile) {
            Log::info($pid . "|system /home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $profile->id");
            $tmp = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $profile->id");
            $shell = trim($tmp);
            Log::info("system Shell " . $shell);
            if ($shell != null && $shell != "") {
                $json = json_decode($shell);
                if (!empty($json->data->user_name)) {
                    $profile->tiktok_account = $json->data->user_name;
                    $profile->save();
                }
            }
        }
    }

    public function getViolation(Request $request) {
        $start = time();
        Log::info("start getViolation " . getmypid());
        $violations = [];
        $tiktok = TiktokProfile::where("id", $request->id);
        if (!$request->isAdmin) {
            $tiktok = $tiktok->where("username", Auth::user()->user_name);
        }
        $tiktok = $tiktok->first();
        if (!$tiktok) {
            $live = Zliveautolive::where("status", 2)->where("platform", 2)->where("id", $request->id)->first();
            if ($live) {
                $tiktok = TiktokProfile::where("id", $live->profile_id)->first();
            }
        }
        if ($tiktok) {
            $result = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_5_capt.py check_violation $tiktok->id");
            if ($result != null && $result != "") {
                $json = json_decode($result);
                if (!empty($json->data->records)) {
                    $violations = $json->data->records;
                }
            }
        }
        Log::info("finish getViolation " . getmypid() . " " . (time() - $start) . "s");
        return response()->json($violations);
    }

    public function checkCookie(Request $request) {
        Log::info("checkCookie: /home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $request->id");
        $tmp = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py get_account_info $request->id");
        $shell = trim($tmp);

        $json = json_decode($shell, true);

        $newKey = ['id' => $request->id, 'is_v3' => 0];
        if (is_array($json)) {
            $json = array_merge($newKey, $json);
        }
//        $json->id = $request->id;
//        $json->is_v3 = 0;

        if (!empty($json['data']['user_name'])) {
            $check = TiktokProfile::where("tiktok_account", $json['data']['user_name'])->whereNotNull("stl_token")->first();
            if ($check) {
                TiktokProfile::where("id", $request->id)->update(["stl_token" => $check->stl_token]);
                $json['is_v3'] = 1;
            }
        }
        return json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    //2024/12/23 đăng ký tiktok v3
    public function requestV3(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.requestV3|request=" . json_encode($request->all()));

        if ($user->tiktok_package == "TIKTOKTEST") {
            return response()->json(array('status' => "error", 'message' => "Bạn phải sử dụng tối thiểu gói TIKTOK1"));
        }
        //đếm số tài khoản v3 hiện đang sử dụng
        $count = DB::select("select count(id) as total from `tiktok_profile` where username='$user->user_name' and status_cookie=1 and del_status =0 and (stl_token is not null or JSON_EXTRACT(active_v3_info, '$.status') = 'waiting') ");
        //1 luồng sẽ được 2 tài khoản kich v3
        $total = $count[0]->total;
        if ($total >= $user->tiktok_key_live * 2) {
            return response()->json(array('status' => "error", 'message' => "Tài khoản của bạn chỉ được kích hoạt tối đa " . ($user->tiktok_key_live * 2) . " kênh"));
        }

        //kiểm tra xem tài khoản đủ điều kiện chưa
        $rp = $this->checkCookie($request);
        Log::info("requestV3: " . $rp);
        $check = json_decode($rp);
        if (isset($check->code)) {
            if ($check->code == 0) {
                if (isset($check->is_v3)) {
                    if ($check->is_v3 == 0) {
                        TiktokProfile::where("id", $request->id)->update(["active_v3_info" => json_encode(['status' => "waiting", 'time' => time()])]);
                        event(new Notify(1, ["hoabt2", "truongpv"], "Tài khoản $request->id yêu cầu kích hoạt v3"));
                        return response()->json(array('status' => "success", 'message' => "Đã gửi yêu cầu thành công"));
                    } else {
                        TiktokProfile::where("id", $request->id)->update(["active_v3_info" => json_encode(['status' => "done", 'time' => time(), "action_time" => time()])]);
                        return response()->json(array('status' => "success", 'message' => "Tài khoản của bạn đã được kích hoạt V3 thành công"));
                    }
                }
            } else {
                return response()->json(array('status' => "error", 'message' => "Tài khoản của bạn bị lỗi: " . $check->message));
            }
        } else {
            return response()->json(array('status' => "error", 'message' => "Đã có lỗi khi kiểm tra tài khoản của bạn"));
        }
        return response()->json(array('status' => "success", 'message' => "Success"));
    }

    public function listv3(Request $request) {
        DB::enableQueryLog();
//        $datas = DB::select("select * from `tiktok_profile` where  status_cookie=1 and del_status =0 and (stl_token is not null or JSON_EXTRACT(active_v3_info, '$.status') = 'waiting') ");
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.index|request=' . json_encode($request->all()));
        $datas = TiktokProfile::where("status_cookie", 1)->where("del_status", 0)->whereRaw("(stl_token is not null or JSON_EXTRACT(active_v3_info, '$.status') = 'waiting' or JSON_EXTRACT(active_v3_info, '$.status') = 'kicked' or JSON_EXTRACT(active_v3_info, '$.status') = 'error') ");
        $queries = [];

        $limit = 30;
        if (isset($request->limit)) {
            if ($request->limit <= 2000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }

        if (isset($request->id)) {
            $datas = $datas->where("id", trim($request->id));
            $queries['id'] = $request->id;
        }
        if (isset($request->status_kick) && $request->status_kick != -1) {
            if ($request->status_kick == "waiting") {
                $datas = $datas->whereRaw("JSON_EXTRACT(active_v3_info, '$.status') = 'waiting'");
            } elseif ($request->status_kick == "done") {
                $datas = $datas->whereRaw("JSON_EXTRACT(active_v3_info, '$.status') = 'done' or stl_token is not null");
            } elseif ($request->status_kick == "kicked") {
                $datas = $datas->whereRaw("JSON_EXTRACT(active_v3_info, '$.status') = 'kicked'");
            }
            $queries['status_kick'] = $request->status_kick;
        }
//
//        if (isset($request->ip)) {
//            $datas = $datas->where("ip", $request->ip);
//            $queries['ip'] = $request->ip;
//        }
//        if (isset($request->customer_id)) {
//            $datas = $datas->where("customer_id", $request->customer_id);
//            $queries['customer_id'] = $request->customer_id;
//        }
        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo last_number_view_playlist desc
            $request['sort'] = 'id';
            $request['order'] = 'desc';
            $queries['sort'] = 'id';
            $queries['order'] = 'desc';
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);

        foreach ($datas as $data) {
            $data->status_v3 = "";
            $data->action_time = "";
            $data->v3_tooltip = "";
            if ($data->active_v3_info != null) {
                $json = json_decode($data->active_v3_info);
                if (isset($json->status)) {
                    $data->status_v3 = $json->status;
                }
                if ($data->status_v3 == 'error') {
                    $data->v3_tooltip = $json->message;
                }
                if (isset($json->action_time)) {
                    $data->action_time = '(' . Utils::timeText($json->action_time) . ')';
                }
            }
        }
        $countFakeChannel = DB::select("select status,count(*) as number from tiktok_fake_channel group by status");
        return view('components.activev3', [
            "datas" => $datas,
            'request' => $request,
            'limitSelectbox' => $this->genLimit($request),
            'status_kick' => $this->genStatusKickV3($request),
            'limit' => $limit,
            'countFakeChannel' => $countFakeChannel,
        ]);
    }

}
