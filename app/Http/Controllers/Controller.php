<?php

namespace App\Http\Controllers;

use App\Http\Models\Categorydata;
use App\Http\Models\Invoice;
use App\Http\Models\Languagehelper;
use App\Http\Models\Locationdata;
use App\Http\Models\Package;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zlivecustomer;
use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Log;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    function __construct() {
        
    }

    protected function genTimeZone() {
        $value = array('-10', '-9', '-8', '-7', '-6', '-5', '-4', '-3', '-2', '-1', '+0', '+1', '+2', '+3', '+4', '+5', '+6', '+7', '+8', '+9', '+10', '+11', '+12');
        $label = array('(GMT-10:00) America/Atka (Hawaii-Aleutian Standard Time)', '(GMT-9:00) America/Anchorage (Alaska Standard Time)',
            '(GMT-8:00) America/Dawson (Pacific Standard Time)', '(GMT-7:00) America/Boise (Mountain Standard Time)',
            '(GMT-6:00) America/Belize (Central Standard Time)', '(GMT-5:00) America/Atikokan (Eastern Standard Time)',
            '(GMT-4:00) America/Caracas (Venezuela Time)', '(GMT-3:00) America/St_Johns (Newfoundland Standard Time)',
            '(GMT-2:00) America/Noronha (Fernando de Noronha Time)', '(GMT-1:00) America/Scoresbysund (Eastern Greenland Time)',
            '(GMT+0:00) Africa/Abidjan (Greenwich Mean Time)', '(GMT+1:00) Africa/Algiers (Central European Time)',
            '(GMT+2:00) Africa/Blantyre (Central African Time)', '(GMT+3:00) Africa/Addis_Ababa (Eastern African Time)',
            '(GMT+4:00) Asia/Baku (Azerbaijan Time)', '(GMT+5:00) Asia/Aqtau (Aqtau Time)',
            '(GMT+6:00) Antarctica/Mawson (Mawson Time)', '(GMT+7:00) Asia/Ho_Chi_Minh (Indochina Time)',
            '(GMT+8:00) Asia/Brunei (Brunei Time)', '(GMT+9:00) Asia/Dili (Timor-Leste Time)',
            '(GMT+10:00) Asia/Sakhalin (Sakhalin Time)', '(GMT+11:00) Asia/Magadan (Magadan Time)', '(GMT+12:00) Asia/Anadyr (Anadyr Time)');
        $timeZone = "";
        $user = Auth::user();
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $user->timezone) {
                $timeZone .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $timeZone .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }

        return $timeZone;
    }

    protected function genLimit(Request $request) {
        $value = array('30', '50', '100', '200', '500', '1000', '2000');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->limit) {
                $option .= "<option  selected value='$value[$i]'>$value[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$value[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusUser(Request $request) {
        $value = array('-1', '1', '0');
        $label = array(trans('label.value.select'), trans('label.value.active'), trans('label.value.inactive'));
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->s) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function genStatusInvoice(Request $request) {
        $value = array('-1', '0', '1', '2');
        $label = array(trans('label.value.select'), trans('label.value.new'), trans('label.value.paid'), trans('label.value.expired'));
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->s) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }
    
    protected function genStatusInvoiceVat(Request $request) {
        $value = array('-1', '0', '1');
        $label = array(trans('label.value.select'), "VAT not declared", "VAT declared");
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->status_vat) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    protected function getMonthYearFromTime($time, $timeZone) {
        $period = gmdate("m/Y", $time - $timeZone * 3600);
        return $period;
    }

    protected function calRemainDate($user, $oldPackage, $package, $month) {
        //nếu nâng cấp gói cước thì mới tính dateRemain, gia hạn thì thôi
        $dateRemaining = 0;
        if ($package->platform == 1) {
            $userPackage = $user->package_code;
            $userEndDate = $user->package_end_date;
        } else if ($package->platform == 2) {
            $userPackage = $user->tiktok_package;
            $userEndDate = $user->tiktok_end_date;
        } else if ($package->platform == 3) {
            $userPackage = $user->shopee_package;
            $userEndDate = $user->shopee_end_date;
        }
        if ($userPackage != $package->package_code) {
            if ($userEndDate > time()) {
                $oldInvoice = Invoice::where("user_name", $user->user_name)->where("package_code", $userPackage)->where("status", 1)->where("platform", $package->platform)->orderBy("id", "desc")->first();
                if (!$oldInvoice) {
                    $oldMonth = 1;
                    $moneyGot = $oldPackage->price;
                } else {
                    $oldMonth = $oldInvoice->month;
                    //số tiền đã nhận ở gói cũ
                    $moneyGot = $oldInvoice->payment_money;
                }
                //số ngày còn lại của gói cũ
                $remainDayOld = ($userEndDate - time()) / 86400;
                //số tiền còn lại của gói cũ
                $moneyRemaining = ($moneyGot / ($oldMonth * 31)) * $remainDayOld;
                //tính số tiền 1 ngày của gói mới
                if ($month > 1) {
                    $discout = "discount_$month";
                    $moneyPerDayNewPackage = ($month * $package->price - $package->$discout) / ($month * $package->duration);
                } else {
                    $moneyPerDayNewPackage = ($package->price) / $package->duration;
                }

                //từ số tiền còn lại chuyển thành số ngày của gói mới
                if ($moneyPerDayNewPackage == 0) {
                    $dateRemaining = 0;
                } else {
                    $dateRemaining = round($moneyRemaining / $moneyPerDayNewPackage);
                }
                $packageEndDate = time() + ($month * $package->duration * 86400) + ($dateRemaining * 86400);
                Log::info("user=$user->user_name moneyOld=$moneyGot moneyRemain=$moneyRemaining dayOld=$remainDayOld money/day new=$moneyPerDayNewPackage dateRemain=$dateRemaining");
            } else {
                $packageEndDate = (time() + ($month * $package->duration * 86400));
            }
        } else {
            if ($userEndDate > time()) {
                //nếu tính đến hiện tại gói cước vẫn còn
                $packageEndDate = ($userEndDate + ($month * $package->duration * 86400));
            } else {
                //nếu đã hết hạn
                $packageEndDate = (time() + ($month * $package->duration * 86400));
            }
        }
//        if ($moneyRemaining <= 0) {
//            $moneyRemaining = 0.0;
//        }
//        $moneyRemaining = round($moneyRemaining, -3);
        return list($dateRemain, $packageEndDate) = [$dateRemaining, $packageEndDate];
    }

    protected function loadPackage(Request $request) {

        $datas = Package::where('status', 1)->whereIn('package_type', [0, 1])->orderBy("platform")->orderBy("order_package")->get();
        $lstOption = '';
        foreach ($datas as $data) {
            $mo3 = $data->price * 3 - $data->discount_3;
            $mo6 = $data->price * 6 - $data->discount_6;
            $mo9 = $data->price * 9 - $data->discount_9;
            $mo12 = $data->price * 12 - $data->discount_12;
            $lstOption .= "<option value='$data->package_code' live='$data->number_live' acc='$data->number_account' mo1='$data->price'  mo3='$mo3' mo6='$mo6' mo9='$mo9' mo12='$mo12'>$data->package_name</option>";
        }

        return $lstOption;
    }

    protected function genRole(Request $request) {
        $user = null;
        if (isset($request->username)) {
            $user = User::where('user_name', $request->username)->first();
        }
        $value = array('0', '2', '5');
        $label = array('Normal', 'Create Account', 'Test Tiktok');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($user && in_array($value[$i], explode(",", $user->role))) {
                $option .= "<option  selected value='$value[$i]'>$value[$i] &rarr; $label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$value[$i] &rarr; $label[$i]</option>";
            }
        }
        return $option;
    }

    protected function loadCategory(Request $request) {
        $datas = Categorydata::all();
        $lstOption = "<option value='1' selected>" . trans('label.valueDefault') . "</option>";
        foreach ($datas as $data) {
            $lstOption .= "<option value='" . $data->category_code . "'>" . $data->category_name . "</option>";
        }

        return $lstOption;
    }

    protected function loadLocation(Request $request) {
        $datas = Locationdata::orderBy('location_name')->get();
        $lstOption = "<option " . ($request->location_data == -1 ? 'selected' : '') . "value='1' selected>" . trans('label.valueDefault') . "</option>";
        foreach ($datas as $data) {
            $lstOption .= "<option " . ($request->location_data == $data->location_data ? 'selected' : '') . "value='" . $data->location_data . "'>" . $data->location_name . "</option>";
        }
        return $lstOption;
    }

    protected function loadLanguage() {
        $datas = Languagehelper::all();
        $lstOption = "<option value='1' selected>" . trans('label.valueDefault') . "</option>";
        foreach ($datas as $data) {
            $lstOption .= "<option value='" . $data->code . "'>" . $data->name . "</option>";
        }
        return $lstOption;
    }

    protected function getPackageCodeFromSelectPlan($selectPlan) {
        if ($selectPlan == 'Auto20' || $selectPlan == 'Auto50' || $selectPlan == 'AUTO50') {
            return 'AUTO50';
        } else if ($selectPlan == 'Auto100' || $selectPlan == 'AUTO100') {
            return 'AUTO100';
        } else if ($selectPlan == 'Auto200' || $selectPlan == 'Auto300' || $selectPlan == 'AUTO300') {
            return 'AUTO300';
        } else if ($selectPlan == 'Auto500' || $selectPlan == 'AUTO500') {
            return 'AUTO500';
        } else if ($selectPlan == 'Auto1000' || $selectPlan == 'Auto1100' || $selectPlan == 'Auto1200' || $selectPlan == 'AUTO1000') {
            return 'AUTO1000';
        } else if ($selectPlan == 'AutoVIP' || $selectPlan == 'AUTOVIP') {
            return 'AUTOVIP';
        } else {
            return 'AUTOTEST';
        }
    }

    protected function generateRandomString($length = 16) {
        $today = strtotime(date("m/d/Y H:i:s"));
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
            $randomStringDate = $randomString . $today;
        }

        return $randomStringDate;
    }

    protected function genStatusLive(Request $request) {
        $value = array('-1', '0', '1', '2', 3, 4, 5);
        $label = array(trans('label.value.select'), 'Mới', 'Đang đợi live', 'Đang live', 'Đang đợi dừng', 'Đang xử lý', 'Đang dừng');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->s) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

    //đếm số lệnh đã tạo theo user
    protected function countLiving($userCode) {
        return Zliveautolive::where("user_id", $userCode)->count();
    }

    //đếm số lệnh đang chạy theo user
    protected function countLivingPlaying($userCode, $platform) {
        return Zliveautolive::where("user_id", $userCode)->whereIn("status", [1, 2, 4])->where("del_status", 0)->where("platform", $platform)->count();
    }

    //đếm số lệnh đang chạy theo user_code
    protected function checkLivingPlayingByUsercode($userCode, $platform) {
        $count = Zliveautolive::where("user_id", $userCode)->whereIn("status", [1, 2, 4])->where("del_status", 0)->where("platform", $platform)->count();
        $user = User::where("user_code", $userCode)->first();
        $numberKey = $user->number_key_live;
        if ($platform == 2) {
            $numberKey = $user->tiktok_key_live;
        } elseif ($platform == 3) {
            $numberKey = $user->shopee_key_live;
        }
        if ($count >= $numberKey) {
            return true;
        }
        return false;
    }

    //đếm số lệnh đang chạy theo customer id
    protected function checkLivingPlayingByCustomerId($customerId, $platform) {
        $count = Zliveautolive::where("cus_id", $customerId)->whereIn("status", [1, 2, 4])->where("del_status", 0)->where("platform", $platform)->count();
        $cus = Zlivecustomer::where("customer_id", $customerId)->first();
        $numberKey = $cus->number_key_live;
        if ($platform == 2) {
            $numberKey = $cus->tiktok_key_live;
        } elseif ($platform == 3) {
            $numberKey = $cus->shopee_key_live;
        }
        if ($count >= $numberKey) {
            return true;
        }
        return false;
    }

    //kiểm tra xem user_code expire chưa
    protected function isExpire($userCode, $platform) {
        if ($platform == 1) {
            $packageEndDateKey = "package_end_date";
        } else if ($platform == 2) {
            $packageEndDateKey = "tiktok_end_date";
        } else if ($platform == 3) {
            $packageEndDateKey = "shopee_end_date";
        }
        $check = User::where("user_code", $userCode)->where($packageEndDateKey, ">", time())->first();
        if ($check) {
            return false;
        }
        return true;
    }

    //kiểm tra xem khóa luồng này đã đc chạy chưa
    protected function checkKeyLive($keyLive) {
        $check = Zliveautolive::where("key_live", $keyLive)->whereIn("status", [1, 2, 4])->first();
        return $check;
    }

    protected function checkDriveFile($arraySource) {
        $metadataResult = [];
        $errorResult = "";
        $sizeRsult = 0;
        $errorArrayResult = [];
        foreach ($arraySource as $source) {
            if ($source != "") {
                $cmd = "gbak get-file-info-v2 --idx \"$source\"";
                //$shell = shell_exec("$cmd 2>&1; echo $?");
                $shell = trim(shell_exec($cmd));
                if ($shell != null && $shell != "" && $shell != "Not Found" && !\App\Common\Utils::containString($shell, "Not Found")) {
                    $json = json_decode($shell);
                    if (!empty($json->error) && !empty($json->error->message) && !\App\Common\Utils::containString($json->error->message, "quota")) {
                        $errorResult .= (!empty($json->error->message) ? $json->error->message : "Can not parse error->message") . PHP_EOL;
                        $errorArrayResult[] = (object) [
                                    "link" => $source,
                                    "status" => 0,
                                    "message" => (!empty($json->error->message) ? $json->error->message : "Can not parse error->message")
                        ];
                        continue;
                    }
                    if (!empty($json->capabilities->canDownload)) {
                        if ($json->capabilities->canDownload != true) {
                            $errorResult .= "Hãy cấu hình cho phép download với file:" . PHP_EOL . $source . PHP_EOL;
                            $errorArrayResult[] = (object) [
                                        "link" => $source,
                                        "status" => 0,
                                        "message" => "File không được phép download"
                            ];
                            continue;
                        }
                    }
                    if (!empty($json->fileSize)) {
                        $sizeRsult = $sizeRsult + intval($json->fileSize);
                        $driveId = "";
                        if (!empty($json->id)) {
                            $driveId = $json->id;
                        }
                        if (!empty($json->videoMediaMetadata)) {
                            $meta = $json->videoMediaMetadata;
                            $meta->fileSize = $json->fileSize;
                            $meta->driveId = $driveId;
                            $metadataResult[] = $meta;
                        }
//                        else if(!\App\Common\Utils::containString ($json->iconLink, "mp4") 
//                                && !\App\Common\Utils::containString ($json->iconLink, "webm")
//                                && !\App\Common\Utils::containString ($json->iconLink, "avi")){
//                            $errorResult .= "Link không phải video: " . PHP_EOL . $source . PHP_EOL;
//                            $errorArrayResult[] = (object) [
//                                        "link" => $source,
//                                        "status" => 0,
//                                        "message" => "Link của bạn không phải video, vui lòng nhập link video"
//                            ];
//                            continue;
//                        }
                    }
                } else {
                    $errorResult .= "Link sai định dạng: " . PHP_EOL . $source . PHP_EOL;
                    $errorArrayResult[] = (object) [
                                "link" => $source,
                                "status" => 0,
                                "message" => "Link của bạn sai định dạng, vui lòng copy link share công khai"
                    ];
                    continue;
                }
                $errorArrayResult[] = (object) [
                            "link" => $source,
                            "status" => 1,
                            "message" => "Link ok"
                ];
            }
        }
        return list($size, $error, $errorArray, $metadata) = [$sizeRsult, $errorResult, $errorArrayResult, $metadataResult];
    }

    protected function loadTiktokChannel(Request $request) {
        $user = Auth::user();
        $datas = \App\Http\Models\TiktokProfile::where("status_cookie", 1)->where("status_run", 0)->where("del_status", 0)->where("username", $user->user_name)->get();
        $lstOption = "<option value='-1' >-Chọn-</option>";
        foreach ($datas as $data) {
            $v3 = "v1";
            if ($data->stl_token != null) {
                $v3 = "v3";
            }
            if ($data->id == $request->tiktok_account) {
                $lstOption .= "<option data-type='$v3' selected value='" . $data->id . "'>$data->id - $data->tiktok_name</option>";
            } else {
                $lstOption .= "<option data-type='$v3' value='" . $data->id . "'>$data->id - $data->tiktok_name</option>";
            }
        }
        return $lstOption;
    }

    protected function loadTiktokTopic(Request $request) {
        $datas = \App\Http\Models\TiktokTopic::get();
        $lstOption = "";
        foreach ($datas as $data) {
            if ($data->id == $request->topic) {
                $lstOption .= "<option selected value='" . $data->id . "'>" . $data->topic . "</option>";
            } else {
                $lstOption .= "<option value='" . $data->id . "'>" . $data->topic . "</option>";
            }
        }
        return $lstOption;
    }

    protected function cloneLive($data) {
        $live = new \App\Http\Models\ZliveautoliveError();
        $live->live_id = $data->id;
        $live->status = $data->status;
        $live->url_live = $data->url_live;
        $live->cus_id = $data->cus_id;
        $live->user_id = $data->user_id;
        $live->note = $data->note;
        $live->platform = $data->platform;
        $live->tiktok_profile_id = $data->tiktok_profile_id;
        $live->room_id = $data->room_id;
        $live->key_live = $data->key_live;
        $live->url_source = $data->url_source;
        $live->type_source = $data->type_source;
        $live->seq_source = $data->seq_source;
        $live->repeat = $data->repeat;
        $live->start_alarm = $data->start_alarm;
        $live->end_alarm = $data->end_alarm;
        $live->create_time = $data->create_time;
        $live->server_id = $data->server_id;
        $live->is_vip = $data->is_vip;
        $live->conti_live = $data->conti_live;
        $live->log = $data->log;
        $live->ffmpeg_log = $data->ffmpeg_log;
        $live->time_update = $data->time_update;
        $live->del_status = $data->del_status;
        $live->is_cheat = $data->is_cheat;
        $live->speed = $data->speed;
        $live->started_time = $data->started_time;
        $live->estimate_time_run = $data->estimate_time_run;
        $live->action_log = $data->action_log;
        $live->metadata = $data->metadata;
        $live->is_auto_start = $data->is_auto_start;
        $live->count_auto_start = $data->count_auto_start;
        $live->save();
    }

    protected function genStatusKickV3(Request $request) {
        $value = array('-1', 'waiting', 'kicked', 'done');
        $label = array(trans('label.value.select'), "waiting", 'kicked', 'done');
        $option = "";
        for ($i = 0; $i < count($value); $i++) {
            if ($value[$i] == $request->status_kick) {
                $option .= "<option  selected value='$value[$i]'>$label[$i]</option>";
            } else {
                $option .= "<option  value='$value[$i]'>$label[$i]</option>";
            }
        }
        return $option;
    }

}
