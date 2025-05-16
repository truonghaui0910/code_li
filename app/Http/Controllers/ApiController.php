<?php

namespace App\Http\Controllers;

use App\Common\Client;
use App\Common\Locker;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Command;
use App\Http\Models\Invoice;
use App\Http\Models\TiktokFakeChannel;
use App\Http\Models\TiktokProfile;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zliveclient;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use function public_path;
use function response;

class ApiController extends Controller {

    public function autoRestartLive() {
        $locker = new Locker(12357);
        $locker->lock();
        $success = 0;
        $fail = 0;
        $liveSuccess = [];
        $liveFail = [];
        $curr = time();
//        $datas = Zliveautolive::where("platform", 1)->where("del_status", 0)->where("status", 0)->where("repeat", 0)->where("is_auto_start", 1)->where("count_auto_start", "<", 10)->where("end_alarm", 0)->whereRaw("user_id in (select user_code from users where (package_end_date > $curr or tiktok_end_date > $curr) and (package_code <> 'LIVETEST' or tiktok_package <> 'TIKTOKTEST')) ")->get(["id", "user_id", "cus_id", "key_live", "type_source", "url_source", "status", "is_auto_start", "count_auto_start", "action_log", "server_id", "platform", "tiktok_profile_id", "server_id"]);
        $datas = Zliveautolive::where("platform", 1)
                ->where("del_status", 0)
                ->where("status", 0)
                ->where("repeat", 0)
                ->where("is_auto_start", 1)
                ->where("count_auto_start", "<", 3)
                ->where("end_alarm", 0)
                ->whereRaw("user_id in (select user_code from users where (package_end_date > $curr or tiktok_end_date > $curr) and (package_code <> 'LIVETEST' or tiktok_package <> 'TIKTOKTEST')) ")
                ->whereRaw("log not like '%is not support%'")
                ->whereRaw("url_live not like '%facebook%'")
                ->get();
//        Log::info(count($datas));
        foreach ($datas as $live) {
//            Log::info($live->id);
            if ($this->checkLivingPlayingByUsercode($live->user_id, $live->platform)) {
                continue;
            }
            if ($this->checkLivingPlayingByCustomerId($live->cus_id, $live->platform)) {
                continue;
            }
//
//            $checkKeyLive = $this->checkKeyLive($live->key_live);
//            if ($checkKeyLive) {
//                continue;
//            }
            //clone luồng bị lỗi ra để check
            if ($live->count_auto_start == 0 && $live->is_report == 0) {
                $this->cloneLive($live);
                $live->is_report = 1;
                $live->save();
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
            //tốc độ trung bình 50Mbps
            $estimate = $size / (50 / 8 * 1024 * 1024 );

            if ($error != "") {
                $live->log = $error;
                $live->save();
            }

            //2023/11/15 kill process lệnh cũ khi start lệnh mới                                    
            CommandController::addCommandKillAll($live);


            if (strpos($live->url_source, 'youtu') !== false) {
                $client = Client::getAvailableStream();
            } elseif ($live->type_source == 1) {
                $client = Client::getAvailableFile($live, $sizeGB);
            } elseif ($live->type_source == 2) {
                $client = Client::getAvailableStream();
            } else {
                $client = Client::getAvailableFile($live, $sizeGB);
            }
            $clTmp = Client::getOnlyAvailableUser($live->cus_id, $sizeGB);
            if (isset($clTmp)) {
                $client = $clTmp;
            }
            if (isset($client)) {
                $live->status = 1;
                $live->server_id = $client->client_id;
                $live->time_update = time();
                $live->log = null;
                $live->speed = null;
                $live->estimate_time_run = time() + $estimate;
                $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " autostart live change status=1, server_id=$live->server_id, size=$size" . PHP_EOL;
                $live->metadata = json_encode($metadata);
                $live->is_auto_start = 1;
                $live->count_auto_start = $live->count_auto_start + 1;
                $live->save();
                $success++;
                $liveSuccess[] = $live->id;
            } else {
                $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " autostart live No server available" . PHP_EOL;
                $live->status = 0;
                $live->save();
                $fail++;
                $liveFail[] = $live->id;
            }
        }
        if ($success > 0 || $fail > 0) {
            RequestHelper::liveLog(urlencode("[LIVE] Autostart success $success lệnh " . implode(",", $liveSuccess) . ", fail $fail lệnh " . implode(",", $liveFail) . ""));
        }
        $locker->unlock();
    }

    public function checkExpiredLive() {
        $time = time();
        $livings = Zliveautolive::where("status", 2)->get(["id", "user_id", "cus_id", "platform", "tiktok_profile_id"]);
        foreach ($livings as $live) {
//             Log::info("$live->id $live->user_id checking");
            if ($this->isExpire($live->user_id, $live->platform)) {
                $live->status = 3;
                $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " system change status=3, user expire" . PHP_EOL;
                $live->is_auto_start = 0;
                $live->save();
                error_log("$live->user_id expire");
            }
        }
        error_log("Finish checkExpiredLive: " . (time() - $time) . 's');
    }

    //tự động chạy fix nodata sau 3 tiếng đối với tiktok
    public function autoFixNodata() {
        $curr = time();
        $datas = Zliveautolive::where("status", 2)->where("infinite_loop", 1)
                ->where("platform", 2)
                ->where("time_fix_nodata", "<", $curr)->take(50)
                ->get();
        foreach ($datas as $data) {
            Log::info(getmypid() . " autoFixNodata $data->id");
            $client = Zliveclient::where("client_id", $data->server_id)->first();
            if ($client) {
                $command = new Command();
                $command->server_id = $data->server_id;
                $command->password = $client->client_pass;
                $command->live_id = $data->id;
                $command->key_live = $data->key_live;
                $command->command = 'kill-lid';
                $command->created = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $command->save();

                $data->action_log = $data->action_log . Utils::timeToStringGmT7(time()) . " send command auto kill-lid success" . PHP_EOL;
                $data->time_fix_nodata = time() + 3 * 3600;
                $data->save();
            }
        }
    }

    //lưu gậy tiktok
    public function checkViolation() {
        $start = time();
        Log::info("start checkViolation " . getmypid());
        $lives = Zliveautolive::where("status", 2)->where("platform", 2)->get();
        foreach ($lives as $live) {
            $tiktok = TiktokProfile::where("id", $live->tiktok_profile_id)->first();
            $result = shell_exec("python3 /home/tiktok_tools/tiktok_helper_2_proxy.py check_violation $tiktok->id");
            $tiktok->last_check_violation = time();
            $tiktok->save();
            if ($result != null && $result != "") {
                $json = json_decode($result);
                if (!empty($json->data->records)) {
                    $tiktok->violations = json_encode($json->data->records);
                    $tiktok->save();
                    $count = count($json->data->records);
                    if ($tiktok->violation_number_stop > 0 && $count >= $tiktok->violation_number_stop) {
                        $live = Zliveautolive::where("tiktok_profile_id", $tiktok->id)->where("status", 2)->first();
                        CommandController::addCommandKillAll($live);
                        $live->status = 3;
                        $live->save();
                    }
                }
            }
//            break;
        }
        Log::info("finish checkViolation " . getmypid() . " " . (time() - $start) . "s");
    }

    public function getRunningTiktok() {
        return DB::select("select id,tiktok_profile_id,room_id,key_live from zliveautolive where platform = 2 and status =2 ");
    }

    public function addFakingChannel(Request $request) {
        $data = TiktokFakeChannel::where("channel_id", $request->channel_id)->first();
        if (!$data) {
            $data = new TiktokFakeChannel();
            $data->channel_id = "https://www.youtube.com/channel/$request->channel_id";
            $data->channel_name = $request->channel_name;
            $data->avatar = $request->avatar;
            $data->avatar = $request->avatar;
            $data->listvideo = json_encode($request->listvideo);
            $data->created = Utils::timeToStringGmT7(time());
            $data->save();
            return response()->json(["status" => "success", "message" => "Success"]);
        }
        return response()->json(["status" => "error", "message" => "Exists"]);
    }

    //lấy kênh để chạy fake bằng extention
    public function getFakingChannel() {
        $datas = TiktokFakeChannel::where("status", 0)->where("del_status", 0);
        $dataReturn = $datas->get();
        Log::info("getFakingChannel " . count($dataReturn));
//        $update = $datas->update(["status" => 1]);
        return response()->json($dataReturn);
    }

    //lấy ảnh fake để đăng ký v3
    public function getFakedChannel(Request $request) {
        $locker = new Locker(12358);
        $locker->lock();
        $data = TiktokFakeChannel::where("status", 2)->where("del_status", 0)
                ->first(["id",
            "status",
            DB::raw("concat('https://www.youtube.com/channel/',channel_id) as channel_id"),
            DB::raw("concat('https://www.youtube.com/',handle) as handle"),
            "channel_name",
            "tiktok_profile_id",
            "fake_image_url",
            "avatar", "subs"]);
        if ($data) {
            $data->status = 3;
            $data->updated = Utils::timeToStringGmT7(time());
            if (isset($request->id)) {
                $data->tiktok_profile_id = $request->id;
            }
            $data->save();
        }

        $locker->unlock();
        return response()->json(["data" => $data]);
    }

    public function updateFakedChannel(Request $request) {
        Log::info('|ApiController|updateFakedChannel=' . $request->id);
        $data = TiktokFakeChannel::where("id", $request->id)->first();
        if ($data) {
            if (isset($request->fake_image)) {
                $data->fake_image_base64 = $request->fake_image;
                $data->status = 2;
                $image = base64_decode($request->fake_image);
                $fileName = Utils::slugify($data->handle) . '.jpg';
                $filePath = public_path('channels/' . $fileName);

                // Save the image to the specified path
                file_put_contents($filePath, $image);
                $data->fake_image_url = "https://v21.autolive.me/channels/$fileName";
            }
            if (isset($request->tiktok_profile_id)) {
                $data->tiktok_profile_id = $request->tiktok_profile_id;
            }
            $data->save();
            return response()->json(["status" => "success", "message" => "Success", "image" => $data->fake_image_url]);
        }
        return response()->json(["status" => "error", "message" => "Not exists"]);
    }

    public function scanChannel() {
        Log::info("scanChannel");
        $channels = TiktokFakeChannel::where("status", 6)->where("del_status", 0)->get();
        $total = count($channels);
        foreach ($channels as $index => $channel) {
            $index++;
            Log::info("scanChannel $index/$total $channel->channel_id");
            error_log("scanChannel $index/$total $channel->channel_id");
            $ch = $channel->handle;
            if ($channel->channel_id != null) {
                $ch = $channel->channel_id;
            }
            $chennelInfo = YoutubeHelper::getChannelInfoV2($ch, 1);
            Log::info(json_encode($chennelInfo));
            if ($chennelInfo["status"] == 1) {
                $channel->channel_id = $chennelInfo["channelId"];
                $channel->channel_name = $chennelInfo["channelName"];
                $channel->subs = $chennelInfo["subscribers"];
                $channel->avatar = $chennelInfo["avatar"];
                $channel->created = Utils::timeToStringGmT7(time());
                $channel->updated = Utils::timeToStringGmT7(time());
                $channel->handle = $chennelInfo["handle"];
                //scan xong channel
                $channel->status = 7;
                $channel->save();
                $playlistId = substr_replace($channel->channel_id, "UU", 0, 2);
                $lists = YoutubeHelper::getPlaylist($playlistId, 10);
                $listVideoId = $lists['list_video_id'];
                $listVideo = [];
                foreach ($listVideoId as $video) {

                    $videoInfo = YoutubeHelper::getVideoInfoHtmlDesktop($video);
                    if ($videoInfo["length"] > 0) {
                        $duration = Utils::convertSecondsToTime($videoInfo["length"]);
                    } else {
                        $duration = Utils::convertSecondsToTime(180);
                    }
                    if ($videoInfo["publish_date"] && $videoInfo["publish_date"] != "false") {
                        $publish = gmdate("M d, Y", $videoInfo["publish_date"]);
                    } else {
                        $publish = gmdate("M d, Y", time());
                    }
                    $listVideo[] = (object) [
                                "id" => $video,
                                "title" => $videoInfo["title"],
                                "description" => $videoInfo["description"],
                                "thumbnail" => "https://i.ytimg.com/vi/$video/default.jpg",
                                "duration" => $duration,
                                "visibility" => "Public",
                                "restrictions" => "None",
                                "publishDate" => $publish,
                                "views" => $videoInfo["view"],
                                "comments" => $videoInfo["comment"],
                                "likes" => "–"
                    ];
                }
                $channel->listvideo = json_encode($listVideo);
                $channel->status = 0;
                $channel->save();
//                Log::info(json_encode($listVideo));
            }
        }
        return $total;
    }

    public function callbackAcbTransaction(Request $request) {
        Log::info("ApiController.callbackAcbTransaction|request=" . json_encode($request->all()));
        $apiKey = $request->header('x-api-key');
        if ($apiKey != "ps1YU4GyfPGmE4TsK4n9Cp2HxldIBqnX") {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $invoiceId = null;
        $money = null;
        $debitOrCredit = null;

        if (isset($request->requests[0]["requestParams"]["transactions"][0]["debitOrCredit"])) {
            $debitOrCredit = $request->requests[0]["requestParams"]["transactions"][0]["debitOrCredit"];
        }
        if (isset($request->requests[0]["requestParams"]["transactions"][0]["transactionContent"])) {
            $invoiceId = $request->requests[0]["requestParams"]["transactions"][0]["transactionContent"];
        }
        if (isset($request->requests[0]["requestParams"]["transactions"][0]["amount"])) {
            $money = $request->requests[0]["requestParams"]["transactions"][0]["amount"];
        }
        Log::info("content: $invoiceId,money: $money");
        if ($invoiceId != null && $money != null) {
            if ($debitOrCredit == 'credit') {
                //lấy hết danh sách invoice chưa thành toán trong ngày để so sánh
                $invoices = Invoice::where("status", 0)->where("del_status", 0)->where("system_create_date", ">", time() - 259200)->get();
                foreach ($invoices as $invoice) {
                    if (Utils::containString($invoiceId, $invoice->invoice_id) && $invoice->payment_money == $money) {
                        Log::info("found invoice " . json_encode($invoice));
                        $invoice->transaction_log = json_encode($request->all());
                        $iv = new InvoiceController();
                        $iv->processInvoice("system", 1, $invoice);
                        break;
                    }
                }
            }
        }
        $result = (object) [
                    "timestamp" => gmdate("Y-m-d") . "T" . gmdate("H:i:s") . "Z",
                    "responseCode" => "00000000",
                    "message" => "Success"
        ];

        return response()->json($result);
    }

    public function callbackAcbQuery(Request $request) {
        Log::info("ApiController.callbackAcbQuery|request=" . json_encode($request->all()));
//        $apiKey = $request->header('x-api-key');
//        if ($apiKey != "ps1YU4GyfPGmE4TsK4n9Cp2HxldIBqnX") {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
        $result = (object) [
                    "timestamp" => gmdate("Y-m-d") . "T" . gmdate("H:i:s") . "Z",
                    "responseCode" => "00000000",
                    "message" => "Success"
        ];

        return response()->json($result);
    }

    public function downloadImg($id) {
        Log::info('ApiController|downloadImg=' . $id);
        $data = TiktokFakeChannel::where("id", $id)->first();
        Log::info($data->fake_image_url);
        $image = str_replace("https://v21.autolive.me/channels/", "", $data->fake_image_url);
        $filePath = "/home/v21.autolive.vip/public_html/public/channels/$image";
        $file = file_get_contents($filePath);
        Log::info("image $image");
        Log::info("filePath $filePath");
        // Kiểm tra nếu file tồn tại
        if (!file_exists($filePath)) {
            Log::info("file không tồn tại");
            abort(404, 'File not found');
        }
        return response($file, 200)
                        ->header('Content-Type', 'image/jpeg')
                        ->header('Content-Disposition', 'attachment; filename="' . $image . '"');
    }

    public function scanEndAlarmRecords() {
        $locker = new Locker(14506);
        $locker->lock();
        // Lấy thời gian hiện tại
        $currentTime = time();

        $records = Zliveautolive::where('end_alarm', '>', 0)
                ->whereIn('is_report', [2, 3, 4])
                ->where('end_alarm', '<=', $currentTime)
                ->where('del_status', 0)
                ->get();
        Log::info("scanEndAlarmRecords " . count($records) . " pid=" . getmypid());
        $processedCount = 0;
        foreach ($records as $record) {
            try {
                shell_exec("/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_finish $record->tiktok_profile_id");
                $record->is_report = 5;
                $record->estimate_time_run = 0;
                $record->status = 3;
                $record->action_log = $record->action_log . Utils::timeToStringGmT7(time()) . " System stopped due to end_alarm time reached" . PHP_EOL;
                $record->save();
                sleep(3);
                CommandController::addCommandKillAll($record);
                $processedCount++;
                Log::info("TiktokController.scanEndAlarmRecords: #$record->id: ");
            } catch (Exception $e) {
                Log::error("TiktokController.scanEndAlarmRecords: Lỗi khi xử lý live #$record->id: " . $e->getMessage());
            }
        }


        $locker->unlock();
        return $processedCount;
    }

    public function scanStartAlarmRecords() {
        $locker = new Locker(14506);
        $locker->lock();
        // Lấy thời gian hiện tại
        $currentTime = time();

        // Tìm tất cả các bản ghi có trạng thái chưa bắt đầu live (status = 0) và đã đến thời gian bắt đầu
        // start_alarm > 0: chỉ xử lý những bản ghi có thiết lập thời gian bắt đầu
        // start_alarm <= currentTime: thời gian bắt đầu đã đến hoặc đã qua
        // start_alarm >= (currentTime - 300): không quét các lệnh đã bỏ lỡ quá 5 phút
        $records = Zliveautolive::where('status', 0)
                ->where('is_report', 2)
                ->where('del_status', 0)
                ->where('start_alarm', '>', 0)
                ->where('start_alarm', '<=', $currentTime)
                ->where('start_alarm', '>=', $currentTime - 300) // Chỉ xử lý các lệnh trong 5 phút gần đây
                ->get();

        $processedCount = 0;
        Log::info(__FUNCTION__ . " " . count($records) . " pid=" . getmypid());

        foreach ($records as $live) {
            try {
                $processedCount++;
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
                if ($sizeGB == 0) {
                    $sizeGB = 3;
                }
//                CommandController::addCommandKillAll($live);
//                $ips = ["95.217.153.132", "65.109.137.215", "65.108.218.156"];
//                $randomIp = $ips[array_rand($ips)];
//                $client = Zliveclient::where("client_id", $randomIp)->first();
                $clTmp = Client::getOnlyAvailableUser("htAzEnx8d5AaHyj91745514140", $sizeGB);
                if (isset($clTmp)) {
                    $client = $clTmp;
                } else {
                    $client = Client::getAvailableFile($live, $sizeGB);
                }
                if (isset($client)) {
                    $live->is_report = 6;
                    $live->save();
                    $live->status = 1;
                    $live->server_id = $client->client_id;
                    $live->time_update = time();
                    $live->started_time = time();
                    $live->time_fix_nodata = time() + 3 * 3600;

                    $live->log = null;
                    $live->speed = null;
                    $live->estimate_time_run = time() + $estimate;
                    $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " system change status=1, server_id=$live->server_id, size=$size" . PHP_EOL;
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
                            Log::info(__FUNCTION__ . " $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info(__FUNCTION__ . " Shell " . $shell);
                        } elseif ($live->command == "live_studio_v2") {
                            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_create_studio_v2 $live->tiktok_profile_id";
                            Log::info(__FUNCTION__ . " $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info(__FUNCTION__ . " Shell " . $shell);
                        } elseif ($live->command == "live_studio_v3") {
                            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_create_studio_v3 $live->tiktok_profile_id";
                            Log::info(__FUNCTION__ . " $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info(__FUNCTION__ . " Shell " . $shell);
                        } else {
                            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt.py live_create $live->tiktok_profile_id";
                            Log::info(__FUNCTION__ . " $cmd");
                            $tmp = shell_exec($cmd);
                            $shell = trim($tmp);
                            Log::info(__FUNCTION__ . " Shell " . $shell);
                        }

                        //2024/07/23 thử lại nếu Shell null
                        if ($shell == null || $shell == "") {
                            for ($n = 1; $n <= 2; $n++) {
                                sleep(3);
                                $tmp = shell_exec($cmd);
                                Log::info(__FUNCTION__ . " retry$n $cmd");
                                $shell = trim($tmp);
                                Log::info(__FUNCTION__ . " retry$n Shell " . $shell);
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
                                Log::info("system $cmdActive");
                                $tmp = shell_exec($cmdActive);
                                $shell = trim($tmp);
                                Log::info("system Shell " . $shell);

                                Log::info("system $cmd");
                                $tmp = shell_exec($cmd);
                                $shell = trim($tmp);
                                Log::info("system Shell " . $shell);
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
                                    Log::info($message);
                                    continue;
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
                                Log::info($message);
                                continue;
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
                                Log::info(__FUNCTION__ . " $cmd");
                                $tmp = shell_exec($cmd);
                                $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " system send command $cmd" . PHP_EOL;
                                $live->action_log = $live->action_log . trim($tmp) . PHP_EOL;
                                $live->save();
                            }
                        }
                    }
                    Log::info("TiktokController.scanStartAlarmRecords: #$live->id: ");
                    $live->save();
                }
            } catch (Exception $e) {
                Log::error("TiktokController.scanStartAlarmRecords: Lỗi khi xử lý live #$live->id: " . $e->getMessage());
            }
        }


        $locker->unlock();
        return $processedCount;
    }

    //Cập nhật chính xác thời gian started_time cho các luồng live
    public function updateLiveStartedTime() {
        $locker = new Locker(14504);
        $locker->lock();
        // Lấy thời gian hiện tại
        $currentTime = time();

        // Lấy tất cả các live đang chạy và chưa cập nhật thời gian started_time
        $lives = Zliveautolive::where('status', 2)
                ->where('del_status', 0)
                ->where('is_report', 6)
                ->get();

        $processedCount = 0;
        Log::info(__FUNCTION__ . ": Tìm thấy " . count($lives) . " luồng live cần cập nhật thời gian bắt đầu" . " pid=" . getmypid());
        foreach ($lives as $live) {
            try {
                // Cập nhật thời gian started_time là thời gian hiện tại
                $live->started_time = $currentTime;
                // Đánh dấu là đã cập nhật thời gian started_time
                $live->is_report = 3;
                $live->save();

                $processedCount++;
                Log::info(__FUNCTION__ . ": Đã cập nhật thời gian bắt đầu cho luồng live #{$live->id}: " . $currentTime);
            } catch (Exception $e) {
                Log::error(__FUNCTION__ . ": Lỗi khi cập nhật thời gian bắt đầu cho luồng live #{$live->id}: " . $e->getMessage());
            }
        }

        $locker->unlock();
        return $processedCount;
    }

    //Tự động thêm sản phẩm vào luồng live dựa theo cấu hình của profile
    public function autoAddProductsToLive() {
        $locker = new Locker(14502);
        $locker->lock();
        // Lấy thời gian hiện tại
        $currentTime = time();

        // Lấy tất cả các live đang chạy và đã cập nhật thời gian started_time
        $lives = Zliveautolive::where('status', 2)
                ->where('del_status', 0)
                ->where('is_report', 3)
                ->get();

        $processedCount = 0;
        Log::info(__FUNCTION__ . ": Tìm thấy " . count($lives) . " luồng live cần xử lý" . " pid=" . getmypid());

        foreach ($lives as $live) {
            try {
                // Kiểm tra thời gian bắt đầu live
                if (!$live->started_time) {
                    Log::info(__FUNCTION__ . ": Luồng live #{$live->id} không có thời gian bắt đầu, bỏ qua");
                    continue;
                }

                // Chỉ xử lý khi đã live ít nhất 5 phút
                $liveStartedTime = $live->started_time; // Đã là timestamp
                $elapsedMinutes = floor(($currentTime - $liveStartedTime) / 60);

                if ($elapsedMinutes < 1) {
                    Log::info(__FUNCTION__ . ": Luồng live #{$live->id} mới chạy được {$elapsedMinutes} phút, chưa đủ 1 phút, bỏ qua");
                    continue;
                }

                // Lấy thông tin profile TikTok
                $profile = TiktokProfile::where('id', $live->tiktok_profile_id)->first();
                if (!$profile || !$profile->product_pin_config) {
                    Log::info(__FUNCTION__ . ": Luồng live #{$live->id}: Không tìm thấy profile hoặc cấu hình pin");
                    continue;
                }

                // Parse cấu hình pin
                $pinConfig = json_decode($profile->product_pin_config, false); // Trả về object
                if (!$pinConfig || !isset($pinConfig->product_set_id)) {
                    Log::info(__FUNCTION__ . ": Luồng live #{$live->id}: Không có product_set_id trong cấu hình pin");
                    continue;
                }

                $productSetId = $pinConfig->product_set_id;

                // Lấy user sở hữu profile
                $user = User::where('user_name', $profile->username)->first();
                if (!$user || !$user->product_sets) {
                    Log::info(__FUNCTION__ . ": Luồng live #{$live->id}: Không tìm thấy user hoặc product_sets");
                    continue;
                }

                // Tìm bộ sản phẩm tương ứng
                $productSets = json_decode($user->product_sets, false); // Trả về object
                $selectedSet = null;

                foreach ($productSets as $set) {
                    if ($set->id == $productSetId) {
                        $selectedSet = $set;
                        break;
                    }
                }

                if (!$selectedSet || empty($selectedSet->products)) {
                    Log::info(__FUNCTION__ . ": Luồng live #{$live->id}: Không tìm thấy bộ sản phẩm hoặc bộ không có sản phẩm nào");
                    continue;
                }

                // Thực hiện thêm sản phẩm vào luồng live
                $addedCount = 0;
                $totalProducts = count($selectedSet->products);

                foreach ($selectedSet->products as $product) {
                    if (!isset($product->product_id)) {
                        continue;
                    }

                    $productId = $product->product_id;
                    $link = "https://shop.tiktok.com/view/product/{$productId}?region=VN&local=en";

                    // Sử dụng lệnh cmd mới để thêm sản phẩm vào livestream
                    $cmd = "/home/tiktok_tools/env/bin/python /home/v21.autolive.vip/public_html/python.py product_add {$live->tiktok_profile_id} {$live->room_id} \"{$link}\"";
                    Log::info(__FUNCTION__ . ": Thêm sản phẩm #{$productId} vào luồng live #{$live->id} - cmd: {$cmd}");

                    $tmp = shell_exec($cmd);
                    $shell = trim($tmp);

                    if ($shell != null && $shell != "") {
                        if (Utils::containString($shell, "permission")) {
                            Log::info(__FUNCTION__ . ": Luồng live #{$live->id}: Tài khoản cần kích hoạt TikTok Shop");
                            break;
                        }

                        $response = json_decode($shell, false); // Trả về object
                        if (isset($response->code) && $response->code == 0 && isset($response->message) && $response->message == 'success') {
                            $addedCount++;
                            Log::info(__FUNCTION__ . ": Thêm thành công sản phẩm #{$productId} vào luồng live #{$live->id}");
                        } else {
                            Log::info(__FUNCTION__ . ": Lỗi khi thêm sản phẩm #{$productId} vào luồng live #{$live->id}: " . $shell);
                        }
                    } else {
                        Log::info(__FUNCTION__ . ": Lỗi khi thêm sản phẩm #{$productId} vào luồng live #{$live->id}: Không có phản hồi");
                    }

                    // Tạm dừng 2 giây giữa các lần thêm sản phẩm để tránh quá tải API
                    sleep(2);
                }

                // Đánh dấu là đã thêm sản phẩm vào luồng live
                $live->is_report = 4; // Đánh dấu đã thêm sản phẩm
                $live->save();

                $processedCount++;
                Log::info(__FUNCTION__ . ": Đã thêm {$addedCount}/{$totalProducts} sản phẩm vào luồng live #{$live->id}");
            } catch (Exception $e) {
                Log::error(__FUNCTION__ . ": Lỗi khi thêm sản phẩm vào luồng live #{$live->id}: " . $e->getMessage());
            }
        }
        $locker->unlock();
        return $processedCount;
    }

    //Tự động pin sản phẩm cho các luồng live đang chạy dựa trên cấu hình pin
    public function autoProductPinning() {
        // Sử dụng FileLock để tránh chạy đồng thời
        $locker = new Locker(14503);
        $locker->lock();

        // Lấy thời gian hiện tại
        $currentTime = time();

        // Lấy tất cả các live đang chạy (status=2) và đã thêm sản phẩm (is_report=4)
        $lives = Zliveautolive::where('status', 2)
                ->where('del_status', 0)
                ->where('is_report', 4)
                ->get();

        $processedCount = 0;
//        Log::info(__FUNCTION__ . ": Tìm thấy " . count($lives) . " luồng live cần kiểm tra pin sản phẩm ". "pid=" . getmypid());

        foreach ($lives as $live) {
            try {
                // Lấy thông tin profile TikTok
                $profile = TiktokProfile::where('id', $live->tiktok_profile_id)->first();
                if (!$profile || !$profile->product_pin_config) {
                    continue;
                }

                // Parse cấu hình pin
                $pinConfig = json_decode($profile->product_pin_config, false); // Trả về object
                if (!$pinConfig || !isset($pinConfig->pin_type) || !isset($pinConfig->product_set_id)) {
                    continue;
                }

                if (!isset($pinConfig->is_autopin) || $pinConfig->is_autopin === false) {
                    continue;
                }

                // Lấy user sở hữu profile
                $user = User::where('user_name', $profile->username)->first();
                if (!$user || !$user->product_sets) {
                    continue;
                }

                // Tìm bộ sản phẩm tương ứng
                $productSets = json_decode($user->product_sets, false); // Trả về object
                $selectedSet = null;

                foreach ($productSets as $set) {
                    if ($set->id == $pinConfig->product_set_id) {
                        $selectedSet = $set;
                        break;
                    }
                }

                if (!$selectedSet || empty($selectedSet->products)) {
                    continue;
                }

                // Kiểm tra nếu trường pinned_info chưa tồn tại trong cấu hình pin, khởi tạo
                if (!isset($pinConfig->pinned_info)) {
                    $pinConfig->pinned_info = new \stdClass();
                    $pinConfig->pinned_info->last_pin_time = 0;
                    $pinConfig->pinned_info->pinned_products = [];
                    $pinConfig->pinned_info->current_index = 0;
                }

                // Xử lý theo kiểu pin
                if ($pinConfig->pin_type === 'interval') {
                    $this->processIntervalPinning($live, $profile, $pinConfig, $selectedSet);
                } else if ($pinConfig->pin_type === 'specific') {
                    $this->processSpecificPinning($live, $profile, $pinConfig, $selectedSet);
                }

                $processedCount++;
            } catch (\Exception $e) {
                Log::error(__FUNCTION__ . ": Lỗi khi xử lý pin sản phẩm cho luồng live #{$live->id}: " . $e->getMessage());
            }
        }

//        Log::info(__FUNCTION__ . ": Đã xử lý {$processedCount} luồng live");
        $locker->unlock();

        return $processedCount;
    }

    /**
     * Xử lý pin sản phẩm theo khoảng thời gian
     * @param Zliveautolive $live - Luồng live đang xử lý
     * @param TiktokProfile $profile - Profile TikTok
     * @param object $pinConfig - Cấu hình pin
     * @param object $selectedSet - Bộ sản phẩm đã chọn
     */
    private function processIntervalPinning($live, $profile, $pinConfig, $selectedSet) {
        $currentTime = time();

        // Kiểm tra xem đã đến thời gian pin tiếp theo chưa
        if ($currentTime < $pinConfig->pinned_info->last_pin_time + $pinConfig->interval) {
            return;
        }

        // Lấy danh sách sản phẩm
        $products = $selectedSet->products;
        if (empty($products)) {
            return;
        }

        // Lấy index của sản phẩm tiếp theo cần pin
        $nextIndex = $pinConfig->pinned_info->current_index;

        // Nếu index vượt quá số lượng sản phẩm, reset về 0
        if ($nextIndex >= count($products)) {
            $nextIndex = 0;
        }

        // Lấy sản phẩm cần pin
        $product = $products[$nextIndex];

        // Thực hiện pin sản phẩm
        $success = $this->pinProduct($live, $profile, $product->product_id);

        // Cập nhật thông tin pin
        if ($success) {
            $pinConfig->pinned_info->last_pin_time = $currentTime;
            $pinConfig->pinned_info->current_index = $nextIndex + 1;

            // Thêm sản phẩm vào danh sách đã pin
            if (!in_array($product->product_id, $pinConfig->pinned_info->pinned_products)) {
                $pinConfig->pinned_info->pinned_products[] = $product->product_id;
            }

            // Lưu lại cấu hình pin
            $profile->product_pin_config = json_encode($pinConfig);
            $profile->save();

            Log::info("processIntervalPinning: Đã pin sản phẩm #{$product->product_id} cho luồng live #{$live->id}");
        }
    }

    /**
     * Xử lý pin sản phẩm theo thời điểm cụ thể
     * @param Zliveautolive $live - Luồng live đang xử lý
     * @param TiktokProfile $profile - Profile TikTok
     * @param object $pinConfig - Cấu hình pin
     * @param object $selectedSet - Bộ sản phẩm đã chọn
     */
    private function processSpecificPinning($live, $profile, $pinConfig, $selectedSet) {
        // Kiểm tra thời gian bắt đầu live
        if (!$live->started_time) {
            return;
        }

        $currentTime = time();
        $liveStartedTime = $live->started_time;
        $elapsedMinutes = floor(($currentTime - $liveStartedTime) / 60);

        // Lấy danh sách sản phẩm cần pin theo thời điểm cụ thể
        $specificProducts = $pinConfig->products;
        if (empty($specificProducts)) {
            return;
        }

        // Kiểm tra từng sản phẩm
        foreach ($specificProducts as $specificProduct) {
            // Bỏ qua nếu đã pin
            if (in_array($specificProduct->product_id, $pinConfig->pinned_info->pinned_products)) {
                continue;
            }

            // Kiểm tra xem đã đến thời điểm pin chưa
            if ($elapsedMinutes >= $specificProduct->pin_time) {
                // Tìm thông tin sản phẩm trong bộ sản phẩm
                $product = null;
                foreach ($selectedSet->products as $p) {
                    if ($p->product_id == $specificProduct->product_id) {
                        $product = $p;
                        break;
                    }
                }

                if (!$product) {
                    continue;
                }

                // Thực hiện pin sản phẩm
                $success = $this->pinProduct($live, $profile, $product->product_id);

                // Cập nhật thông tin pin
                if ($success) {
                    $pinConfig->pinned_info->last_pin_time = $currentTime;

                    // Thêm sản phẩm vào danh sách đã pin
                    $pinConfig->pinned_info->pinned_products[] = $product->product_id;

                    // Lưu lại cấu hình pin
                    $profile->product_pin_config = json_encode($pinConfig);
                    $profile->save();

                    Log::info("processSpecificPinning: Đã pin sản phẩm #{$product->product_id} cho luồng live #{$live->id} tại phút thứ {$specificProduct->pin_time}");
                }
            }
        }
    }

    /**
     * Thực hiện pin sản phẩm
     * @param Zliveautolive $live - Luồng live đang xử lý
     * @param TiktokProfile $profile - Profile TikTok
     * @param string $productId - ID sản phẩm cần pin
     * @return bool - Kết quả pin
     */
    private function pinProduct($live, $profile, $productId) {
        $cmd = "/home/tiktok_tools/env/bin/python /home/v21.autolive.vip/public_html/python.py product_pin {$profile->id} {$live->room_id} {$productId}";
        Log::info("pinProduct: Gọi lệnh pin sản phẩm: " . $cmd);

        $tmp = shell_exec($cmd);
        $shell = trim($tmp);

        if ($shell == null || $shell == "") {
            Log::error("pinProduct: Không có phản hồi khi pin sản phẩm #{$productId}");
            return false;
        }

        $response = json_decode($shell, false);
        if (isset($response->code) && $response->code == 0) {
            return true;
        } else {
            $errorMsg = isset($response->message) ? $response->message : $shell;
            Log::error("pinProduct: Lỗi khi pin sản phẩm #{$productId}: " . $errorMsg);
            return false;
        }
    }

}
