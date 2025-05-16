<?php

namespace App\Http\Controllers;

use App\Common\Client;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Http\Models\TiktokProfile;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zliveclient;
use App\Http\Models\Zlivecustomer;
use App\Http\Models\Zliveupdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class LiveController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.index|request=' . json_encode($request->all()));
        DB::enableQueryLog();

        if (isset($request->note) && $request->note != '') {
            if ($request->isAdmin) {
                $datas = Zliveautolive::whereIn("del_status", [0, 1])->where("platform", 1)->where("id", trim($request->note))->orderBy("id", "desc");
            } else {
                $datas = Zliveautolive::where("del_status", 0)->where("user_id", $user->user_code)->where("platform", 1)->orderBy("id", "desc");
            }
//            $datas = $datas->where(function ($q) use ($request) {
//                $q->where('note', 'like', '%' . trim($request->note) . '%')->orWhere('id', $request->note);
//                if (Utils::containString($request->note, ",")) {
//                    $arrCommand = explode(',', $request->note);
//                    $q->orWhereIn("id", $arrCommand)->orWhereIn("id", $arrCommand);
//                }
//            });
        } else {
            $datas = Zliveautolive::where("del_status", 0)->where("user_id", $user->user_code)->where("platform", 1)->orderBy("id", "desc");
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

        return view('components.live', [
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
//            "notify" => $notify,
        ]);
    }

    public function store(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.store|request=' . json_encode($request->all()));
        if ($user->status == 0) {
            return array('status' => "error", 'message' => "Hãy liên hệ Admin để kích hoạt dùng thử");
        }
        $customer = Zlivecustomer::where("customer_id", $user->customer_id)->first();
        $isUpdateSource = 0;
        //2023/03/15 thêm nhiều lệnh live 1 lúc
        $isMultyLive = 0;
        $arrayKey = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->key_live)));
        $arraySource = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", trim($request->url_source)));
        $countKey = count($arrayKey);
        if ($countKey > 1 && !in_array("", $arrayKey)) {
            $isMultyLive = 1;
        }
        if (mb_strlen(trim($request->note), 'UTF-8') > 100) {
            return array("status" => "error", "message" => "Tên luồng live tối đa 100 ký tự");
        }
        if ($isMultyLive) {

            Log::info("xxxx " . count($arrayKey) . " - " . count($arraySource));
            if (count($arrayKey) > count($arraySource)) {
                return array('status' => "error", 'message' => "Số lượng Key Live phải <= số lượng Link Nguồn");
            }
            $countTotalLive = Zliveautolive::where("user_id", $user->user_code)->where("del_status", 0)->count();
            $maxCreate = $user->number_key_live * 5;
            if ($request->isMaxLive) {
                $maxCreate = $user->number_key_live * 30;
            }
            if ($countTotalLive + $countKey >= $maxCreate) {
                return array('status' => "error", 'message' => "Bạn chỉ được tạo tối đa " . ($maxCreate) . " luồng live, chỉ còn được tạo " . ($maxCreate - $countTotalLive) . " luồng nữa");
            }
            $count = 0;
            $currId = 0;
            $lastId = 0;
            $dateStart = time();
            $dateEnd = time() + rand(2, 3) * 3600;
            $typeSource = 0;
            if (isset($request->type_source)) {
                $typeSource = $request->type_source;
            }
            $sequence = 0;
            if (isset($request->radio_by)) {
                $sequence = $request->radio_by;
            }
            if ($typeSource == 1) {
                $client = Client::getAvailableFile(0, 0);
            } elseif ($typeSource == 2) {
                $client = Client::getAvailableStream();
            } else {
                $client = Client::getAvailableFile(0, 0);
            }
            $clTmp = Client::getOnlyAvailableUser(0, 0);
            if (isset($clTmp)) {
                $client = $clTmp;
            }
            foreach ($arrayKey as $index => $key) {
                $count++;
                $obj = new Zliveautolive();
                $obj->url_live = $request->url_live;
                $obj->key_live = $key;
                $obj->url_source = $arraySource[$index];
                $obj->note = $request->note;

                $obj->type_source = 1; //1:video, 2:streaming
                $obj->seq_source = 1; //0: lần lượt, 1: random
                $obj->repeat = 1; //0: vô cùng, 1: 1ần
                $obj->infinite_loop = 0;
                $obj->create_time = time();
                $obj->server_id = $client->client_id;
                if ($index == 0) {
                    $obj->status = 1;
                    $obj->started_time = time();
                    $obj->conti_live = 0;
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
                } else {
                    $obj->status = 0;
                    $dateStart = $dateEnd;
                    $dateEnd = $dateStart + rand(2, 3) * 3600;
                }
                $obj->start_alarm = $dateStart;
                $obj->end_alarm = $dateEnd;
                $obj->user_id = $user->user_code;
                $obj->cus_id = $user->customer_id;
                $obj->is_vip = $customer->is_vip;
                $obj->conti_live = 0;
                $obj->is_cheat = 0;
                $obj->action_log = $obj->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name create multiple" . PHP_EOL;
                $obj->save();
                $lastId = $currId;
                $currId = $obj->id;
                if ($index > 0) {
                    Zliveautolive::where("id", $lastId)->update(["conti_live" => $currId]);
                }
            }
            return array('status' => "success", 'message' => "Tạo thành công $count lệnh");
        }

        if ($request->edit_id == null) {
            $countTotalLive = Zliveautolive::where("user_id", $user->user_code)->where("platform", 1)->where("del_status", 0)->count();
            if ($countTotalLive >= $user->number_key_live * 5) {
                return array('status' => "error", 'message' => "Bạn chỉ được tạo tối đa " . ($user->number_key_live * 5) . " luồng live");
            }
            $obj = new Zliveautolive();
        } else {
            $obj = Zliveautolive::where("id", $request->edit_id)->where("del_status", 0)->first();
            if (!$obj || $obj->user_id != $user->user_code) {
                return array('status' => "error", 'message' => "Không tìm thấy thông tin");
            }
            //2025/05/08 cho phép sửa source khi đang live
            if ($request->isUpdateSource || $request->isAdmin) {
                if ($request->url_source != $obj->url_source) {
                    $isUpdateSource = 1;
                }
            } else {
                if ($obj->status == 1 || $obj->status == 2 || $obj->status == 4 || $obj->status == 6) {
                    return array('status' => "error", 'message' => "Hãy dừng live trước khi sửa thông tin");
                }
            }
        }

        $obj->url_source = $request->url_source;
        $countUrlSource = 0;
        if (strpos($obj->url_source, "openrec.tv") !== false) {
            $tmpSource = explode('\n', $obj->url_source);
            $tmpSource2 = array();
            foreach ($tmpSource as $itemSource) {
                $countUrlSource++;
                $itemSource = str_replace('\r', '', $itemSource);
                if (strpos($itemSource, "openrec.tv") !== false) {
                    preg_match('/data-file=\\"(.+)\\"/', file_get_contents($itemSource), $results);
                    if (count($results) > 1) {
                        $tmpSource2[] = $results[1];
                    } else {
                        $tmpSource2[] = $itemSource;
                    }
                } else {
                    $tmpSource2[] = $itemSource;
                }
            }
            $obj->url_source = implode('\n', $tmpSource2);
        }
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
        if ($isUpdateSource && $obj->status == 2) {

            $size = 0;
            $sizeGB = 0;
            $error = "";
            $metadata = [];
            if (Utils::containString($obj->url_source, "drive.google.com")) {
                list($size, $error, $errorArray, $metadata) = $this->checkDriveFile($tmpSource2);
                $sizeGB = ceil($size / 1024 / 1024 / 1024);
            }
            //tốc độ trung bình 30Mbps
            $estimate = $size / (40 / 8 * 1024 * 1024 );
            Log::info("user=$user->user_name,id=$obj->id size=$sizeGB GB estimate=$estimate error=$error");
            if ($error != "") {
                $obj->log = $error;
                $obj->save();
                return array('status' => "error", 'message' => "Link nguồn của bạn có vấn đề, hãy kiểm tra trong log", "live" => $obj);
            }
            //2024/07/02 nếu ko check được size thì set mặc định là 3GB
            if ($sizeGB == 0) {
                $sizeGB = 5;
            }

            //kiểm tra xem client hiện tại còn đủ dung lượng không
            $lientCheck = Zliveclient::where("client_id", $obj->server_id)->first();
            if ($lientCheck->disk_free < $sizeGB) {
                return array('status' => "error", 'message' => "Dung lượng file quá lớn, vui lòng thử lại sau", "live" => $obj);
            }
            $obj->status = 2;
            $obj->action_log = $obj->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name update source" . PHP_EOL;
            $obj->save();

            return array('status' => "success", 'message' => "Video mới của bạn sẽ được Livestream nối tiếp vào video hiện tại!", "live" => $obj);
        }

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
        $obj->status = 0;
        $obj->url_live = $request->url_live;
        $obj->key_live = $request->key_live;
        $obj->note = $request->note;
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

        $obj->user_id = $user->user_code;
        $obj->cus_id = $user->customer_id;
        $obj->is_vip = $customer->is_vip;
        $obj->conti_live = 0;
        $obj->is_cheat = 0;
        if ($countUrlSource < 2 && $obj->repeat == 0) {
            $obj->is_cheat = 1;
        }
        $obj->action_log = $obj->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name added or edited" . PHP_EOL;
        $obj->infinite_loop = $infiniteLoop;
        $obj->save();
        return array('status' => "success", 'message' => "Success", "live" => $obj);
    }

    public function update(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.update|request=' . json_encode($request->all()));
        $live = Zliveautolive::where("id", $request->id)->where("del_status", 0)->first(["id", "status", "user_id", "url_source", "type_source", "action_log", "key_live", "note", "end_alarm", "count_auto_start", "tiktok_profile_id", "platform", "server_id", "command"]);
        if (!$live) {
            return array('status' => "error", 'message' => "Không tìm thấy thông tin $request->id");
        }
        if (!in_array("1", explode(",", $user->role))) {
            if ($user->user_code != $live->user_id) {
                return array('status' => "error", 'message' => "Bạn không có quyền thực hiện");
            }
        }
        if (isset($request->status)) {
            $live->is_auto_start = 0;
            if ($request->status == 1) {
                $endDate = $user->package_end_date;
                $numberKey = $user->number_key_live;
                if ($live->platform == 2) {
                    $endDate = $user->tiktok_end_date;
                    $numberKey = $user->tiktok_key_live;
                }
                if ($live->platform == 3) {
                    $endDate = $user->shopee_end_date;
                    $numberKey = $user->shopee_key_live;
                }
                if (time() > $endDate) {
                    return array('status' => "error", 'message' => "Tài khoản của bạn đã hết hạn");
                }
                if ($this->countLivingPlaying($user->user_code, $live->platform) >= $numberKey) {
                    return array('status' => "error", 'message' => "Tài khoản của bạn đã dùng hết $numberKey luồng live đồng thời");
                }

                if ($this->checkLivingPlayingByCustomerId($user->customer_id, $live->platform)) {
                    return array('status' => "error", 'message' => "Nhóm tài khoản của bạn đã dùng hết số luồng live đồng thời");
                }

                if ($live->status != 0) {
                    return array('status' => "error", 'message' => "Chỉ start được luồng live có trạng thái là Mới");
                }
                if ($live->platform == 1) {
                    $checkKeyLive = $this->checkKeyLive($live->key_live);
                    if ($checkKeyLive) {
                        return array('status' => "error", 'message' => "Khóa luồng này đã được sử dụng ở luồng tên là: '$checkKeyLive->note', hãy dừng luồng đó rồi thử lại");
                    }
                }

                $size = 0;
                $sizeGB = 0;
                $error = "";
                $metadata = [];
                if (Utils::containString($live->url_source, "drive.google.com")) {
                    //kiểm tra dung lượng file
                    $txtSource = str_replace(array("\r\n", "\n"), "@;@", $live->url_source);
                    $arraySource = explode("@;@", $txtSource);
                    list($size, $error, $errorArray, $metadata) = $this->checkDriveFile($arraySource);
                    $sizeGB = ceil($size / 1024 / 1024 / 1024);
                }
                //tốc độ trung bình 30Mbps
                $estimate = $size / (40 / 8 * 1024 * 1024 );
                Log::info("user=$user->user_name,id=$live->id size=$sizeGB GB estimate=$estimate error=$error");
                if ($error != "") {
                    $live->log = $error;
                    $live->save();
                    return array('status' => "error", 'message' => "Link nguồn của bạn có vấn đề, hãy kiểm tra trong log", "reload" => 1);
                }
                //2024/07/02 nếu ko check được size thì set mặc định là 3GB
                if ($sizeGB == 0) {
                    $sizeGB = 3;
                }
                //2023/11/15 kill process lệnh cũ khi start lệnh mới                                    
                CommandController::addCommandKillAll($live);


                $clTmp = Client::getOnlyAvailableUser($user->customer_id, $sizeGB);
                if (isset($clTmp)) {
                    $client = $clTmp;
                } else {
                    if (strpos($live->url_source, 'youtu') !== false) {
                        $client = Client::getAvailableStream();
                    } elseif ($live->type_source == 1) {
                        $client = Client::getAvailableFile($live, $sizeGB);
                    } elseif ($live->type_source == 2) {
                        $client = Client::getAvailableStream();
                    } else {
                        $client = Client::getAvailableFile($live, $sizeGB);
                    }
                }


                if (isset($client)) {
                    $live->status = 1;
                    $live->is_report = 0;
                    $live->server_id = $client->client_id;
                    $live->time_update = time();
                    $live->started_time = time();
                    $live->time_fix_nodata = time() + 3 * 3600;

                    $live->log = null;
                    $live->speed = null;
                    $live->estimate_time_run = time() + $estimate;
                    $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name change status=1, server_id=$live->server_id, size=$size" . PHP_EOL;
                    $live->metadata = json_encode($metadata);
                    $live->is_auto_start = 1;
                    $live->count_auto_start = 0;
                    if ($live->end_alarm > 0) {
                        $live->is_auto_start = 0;
                    }
                    //thay key live tiktok
                    if ($live->platform == 2) {
                        //2024/02/27 vừa chạy mobile, vừa chạy studio
                        if ($live->command == "live_mobile") {
                            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_create_mobile $live->tiktok_profile_id";
                            Log::info("$user->user_name $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info("$user->user_name Shell " . $shell);
                        } elseif ($live->command == "live_studio_v2") {
                            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_create_studio_v2 $live->tiktok_profile_id";
                            Log::info("$user->user_name $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info("$user->user_name Shell " . $shell);
                        } elseif ($live->command == "live_studio_v3") {
                            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_create_studio_v3 $live->tiktok_profile_id";
                            Log::info("$user->user_name $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info("$user->user_name Shell " . $shell);
                        } else {
                            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_create $live->tiktok_profile_id";
                            Log::info("$user->user_name $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info("$user->user_name Shell " . $shell);
                        }

                        //2024/07/23 thử lại nếu Shell null
                        if ($shell == null || $shell == "") {
                            for ($n = 1; $n <= 2; $n++) {
                                sleep(3);
                                $tmp = shell_exec($cmd);
                                Log::info("$user->user_name retry$n $cmd");
                                $shell = trim($tmp);
                                Log::info("$user->user_name retry$n Shell " . $shell);
                                if ($shell != null && $shell != "") {
                                    break;
                                }
                            }
//                                return array("status" => "error", "message" => "Tài Khoản Tiktok của bạn chưa đủ điều kiện livestream trên Tiktok Studio. Bạn vui lòng kiểm tra lại");
                        }

                        $keys = explode(";;", $shell);
                        if (count($keys) != 2) {
                            if ($live->command == "live_mobile") {
                                //2024/02/28 khi tạo live fail thì chạy lệch active rồi thử lại
                                $cmdActive = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py active_live_mobile $live->tiktok_profile_id";
                                Log::info("$user->user_name $cmdActive");
                                $tmp = shell_exec($cmdActive);
                                $shell = trim($tmp);
                                Log::info("$user->user_name Shell " . $shell);

                                Log::info("$user->user_name $cmd");
                                $tmp = shell_exec($cmd);
                                $shell = trim($tmp);
                                Log::info("$user->user_name Shell " . $shell);
                                $keys = explode(";;", $shell);
                                if (count($keys) != 2) {
                                    $message = "Lỗi lấy key tiktok";
                                    if ($shell != null && $shell != "") {
                                        $json = json_decode($shell);
                                        if (!empty($json->data->message)) {
                                            $message = $json->data->message;
                                        } elseif (!empty($json->data->prompts)) {
                                            $message = $json->data->prompts;
                                        }
                                        if (Utils::containString($message, "no live auth")) {
                                            $message = "Tài khoản không có quyền live, vui lòng kiểm tra lại tài khoản, hoặc kiểm tra trạng thái gậy và số lượng follow";
                                        }
                                    }
                                    return array("status" => "error", "message" => $message);
                                }
                            } else {
                                $message = "Lỗi lấy key tiktok";
                                if ($shell != null && $shell != "") {
                                    $json = json_decode($shell);
                                    if (!empty($json->data->message)) {
                                        $message = $json->data->message;
                                    } elseif (!empty($json->data->prompts)) {
                                        $message = $json->data->prompts;
                                    }
                                    if (Utils::containString($message, "no live auth")) {
                                        $message = "Tài khoản không có quyền live, vui lòng kiểm tra lại tài khoản, hoặc kiểm tra trạng thái gậy và số lượng follow";
                                    }
                                }
                                return array("status" => "error", "message" => $message);
                            }
                        }
                        $roomId = $keys[1];
                        $pos = strripos($keys[0], '/');
                        $urlLive = substr($keys[0], 0, $pos);
                        $keyLive = trim(substr($keys[0], $pos + 1, strlen($keys[0]) - 1));
                        $live->url_live = $urlLive;
                        $live->key_live = $keyLive;
                        $live->room_id = $roomId;

//                        //2024/01/29 thêm lệnh 
                        if ($live->command == "live_studio" || $live->command == 'live_studio_v2') {
                            preg_match("/stream-(\d+)/", $keyLive, $m);
                            if (count($m) == 2) {
                                $streamId = $m[1];
                                $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py solve_captcha $live->tiktok_profile_id $roomId $streamId";
                                Log::info("$user->user_name $cmd");
                                $tmp = shell_exec($cmd);
                                $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name send command $cmd" . PHP_EOL;
                                $live->action_log = $live->action_log . trim($tmp) . PHP_EOL;
                                $live->save();
                            }
                        }
                        //lấy link direct vói trường hợp link nguồn là link tikok live
                        if (Utils::containString(strtolower($live->url_source), "tiktok.com")) {
                            $link = (object) [
                                        "script_name" => "tiktok",
                                        "func_name" => "get_stream_url",
                                        "params" => [
                                            (object) [
                                                "name" => "url",
                                                "type" => "string",
                                                "value" => $live->url_source
                                            ]
                                        ]
                            ];

                            $taskLists = [];
                            $taskLists[] = $link;
                            $req = (object) [
                                        "gmail" => "hodgeprice48082",
                                        "task_list" => json_encode($taskLists),
                                        "run_time" => 0,
                                        "type" => 88,
                                        "studio_id" => $live->id,
                                        "piority" => 10,
                                        "call_back" => "https://autolive.vip/callback/tiktok/live"
                            ];
                            $bas = RequestHelper::callAPI("POST", "http://bas.reupnet.info/job/add", $req);
                            Log::info("$user->user_name get tiktok live link $live->id " . json_encode($bas));
                            $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name get direct live link jobId=$bas->job_id" . PHP_EOL;
                            $live->status = 6;
                            $live->save();
                        }
                    }
                    $live->save();
                } else {
                    $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name No server available" . PHP_EOL;
                    $live->status = 0;
                    $live->save();
                    return array('status' => "error", 'message' => "Đã có lỗi khi bắt đầu luồng live của bạn, hãy liên hệ admin để được hỗ trợ");
                }
            } else if ($request->status == -1) {
                if ($live->status != 0) {
                    return array('status' => "error", 'message' => "Bạn phải dừng luồng live trước khi xóa");
                }
                $live->del_status = 1;
                $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name change del_status=1" . PHP_EOL;
                //thay đổi trạng thái status_run khi xóa lệnh
                if ($live->platform == 2) {
                    TiktokProfile::where("id", $live->tiktok_profile_id)->update(["status_run" => 0]);
                }
                $live->save();
                return array('status' => "success", 'message' => "Success");
            } else if ($request->status == 3) {
                if ($live->status == 2 || ($live->status == 4 && $live->estimate_time_run < time()) || $live->status == 1) {
                    $live->estimate_time_run = 0;
                    $live->status = 3;
                    $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name change status=3" . PHP_EOL;
                    $live->save();
                    if ($live->platform == 2) {
                        Log::info("$user->user_name /home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_finish $live->tiktok_profile_id");
                        $tmp = shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_finish $live->tiktok_profile_id");
                    }
                    //2023/11/15 kill process khi stop lệnh
                    sleep(3);
                    CommandController::addCommandKillAll($live);
                } else {
                    return array('status' => "error", 'message' => "Chỉ được dừng luồng live đang chạy");
                }
            }
            return array('status' => "success", 'message' => "Success");
        }
    }

    public function find($id) {
        $user = Auth::user();
//        Log::info($user->user_name . '|LiveController.find|request=' . $id);
        $arrCol = ["id", "status", "user_id", "note", "url_live", "key_live", "type_source", "url_source", "start_alarm", "end_alarm", "repeat", "estimate_time_run", "seq_source", "platform", "tiktok_profile_id", "infinite_loop", "command"];
        if (in_array("1", explode(",", $user->role))) {
            $arrCol[] = "action_log";
        }
        $data = Zliveautolive::where("id", $id)->first($arrCol);
        if (!in_array("1", explode(",", $user->role))) {
            if ($data->user_id != $user->user_code) {
                return array('status' => "error", 'message' => "Bạn không có quyền thực hiện");
            }
        }
        if ($data->start_alarm != 0) {
            $data->start_alarm_text = gmdate("m/d/Y H:i", $data->start_alarm + $user->timezone * 3600);
        }
        if ($data->end_alarm != 0) {
            $data->end_alarm_text = gmdate("m/d/Y H:i", $data->end_alarm + $user->timezone * 3600);
        }
        $data->user_id = "";
        $data->estimate = "";
        if ($data->estimate_time_run != 0) {
            $data->estimate = '<br><span class="font-13">Đang download, dự kiến live lúc ' . gmdate("H:i:s", $data->estimate_time_run + 7 * 3600) . '</span>';
        }
        $data->topic = 0;
        if ($data->platform == 2) {
            $tiktok = TiktokProfile::where("id", $data->tiktok_profile_id)->first();
            $data->topic = $tiktok->topic;
            $data->violations = null;
            if ($tiktok->violations != null) {
                foreach (json_decode($tiktok->violations) as $vio) {
                    $data->violations .= "Tài khoản tiktok $tiktok->id:$tiktok->tiktok_name có video '" . $vio->live_info->title . "' vi phạm với nội dung '" . $vio->punish_info->punish_reason . "'" . PHP_EOL;
                }
            }
        }
        return $data;
    }

    public function validateUrl(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.validateUrl|request=' . json_encode($request->all()));
//        $size = 0;
//        $error = "";
//        $metadata = [];
//        $errorArray = [];
        if (Utils::containString($request->url_source, "drive.google.com")) {
            $arraySource = explode("@;@", str_replace(array("\r\n", "\n"), "@;@", $request->url_source));
            list($size, $error, $errorArray, $metadata) = $this->checkDriveFile($arraySource);
            return $errorArray;
        }
        return [];
    }

    public function requestTest(Request $request) {
        $user = Auth::user();
        $platform = "";
        if (isset($request->platform)) {
            $platform = $request->platform;
        }
        RequestHelper::telegram("[TEST] $user->user_name yêu cầu dùng thử $platform");
        return array('status' => "success", 'message' => "Thực hiện thành công");
    }

    public function reportBug(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.reportBug|request=' . json_encode($request->all()));
        $live = Zliveautolive::where("id", $request->id)->first();
        if ($live) {
            $this->cloneLive($live);
            $live->is_report = 1;
            $live->save();
            return array('status' => "success", 'message' => "Success");
        }
        return array('status' => "error", 'message' => "Not found id");
    }

    //2023/01/08 start lại luồng live nhưng không thay server mới.
    public function quickRestart(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.quickRestart|request=' . json_encode($request->all()));
        if ($request->isAdmin) {
            $live = Zliveautolive::where("id", $request->id)->where("status", 0)->first();
        } else {
            $live = Zliveautolive::where("id", $request->id)->where("user_id", $user->user_code)->where("status", 0)->first();
        }
        if ($live) {
            $live->status = 1;
            $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name quick restart" . PHP_EOL;
            $live->save();
            return array('status' => "success", 'message' => "Success");
        }
        return array('status' => "error", 'message' => "Thực hiện không thành công");
    }

    public function updateSource(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.updateSource|request=' . json_encode($request->all()));
        $data = Zliveautolive::where("id", $request->id)->where("user_id", $user->user_code)->where("status", 2)->first();
        if (!$data) {
            return array('status' => "error", 'message' => "Không tìm thấy thông tin");
        }
        $zliveupdate = Zliveupdate::where("zlive_id", $data->id)->first();
        if ($zliveupdate) {
            if ($zliveupdate->status == 2 || $zliveupdate->status == 1) {
                return array('status' => "error", 'message' => "Lệnh live của bạn đang chờ để chuyển, hãy chờ đợi 1 vài phút");
            }
        } else {
            $zliveupdate = new Zliveupdate();
        }

        $zliveupdate->zlive_id = $data->id;
        $zliveupdate->url_source = $data->url_source;
        $zliveupdate->status = 1;
        $zliveupdate->save();
        return array('status' => "success", 'message' => "Thực hiện thành công");
    }

}
