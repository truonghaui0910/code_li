<?php

namespace App\Http\Controllers;

use App\Common\Locker;
use App\Common\Utils;
use App\Http\Models\Command;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zliveclient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class CommandController extends Controller {

    public function getCommand(Request $request) {
//        $platform = $request->header('platform');
//        if ($platform != "AutoWin") {
//            return ["message" => "Wrong system!"];
//        }
        $locker = new Locker(6789);
        $locker->lock();
        $data = Command::where("status", 0)->first();
        if ($data) {
            $data->status = 1;
            $data->updated = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
            $data->save();
            return $data;
        }
        return "{}";
    }

    public function deleteCommand() {
        DB::statement("delete from  command where status =1 and UNIX_TIMESTAMP(CONVERT_TZ(STR_TO_DATE(created,'%Y/%m/%d %H:%i:%s'),'+07:00', 'SYSTEM')) + 7 * 86400 < " . time());
    }

    public function addCommandKillLid(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|CommandController.addCommandKillLid|request=' . json_encode($request->all()));
        if ($request->isAdmin) {
            $live = Zliveautolive::where("id", $request->id)->first();
        } else {
            $live = Zliveautolive::where("id", $request->id)->where("user_id", $user->user_code)->first();
        }
        $log = "fail";
        if ($live) {
            $client = Zliveclient::where("client_id", $live->server_id)->where("status", "<>", 6)->first();
            if ($client) {
                $check = Command::where("server_id", $live->server_id)->where("live_id", $live->id)->where("key_live", $live->key_live)->orderBy("id", "desc")->first();
                if ($check) {
                    //khóa 2 phút
                    if ((time()) - strtotime("$check->created GMT+7") < 120) {
                        return array('status' => "error", 'message' => "Chức năng này phải sử dụng cách nhau ít nhất 2 phút");
                    }
                }
                $command = new Command();
                $command->server_id = $live->server_id;
                $command->password = $client->client_pass;
                $command->live_id = $live->id;
                $command->key_live = $live->key_live;
                $command->command = 'kill-lid';
                $command->created = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $command->save();
                $log = "kill-lid success";
            }
            $live->action_log = $live->action_log . Utils::timeToStringGmT7(time()) . " $user->user_name send command $log" . PHP_EOL;
            $live->save();
            return array('status' => "success", 'message' => "Success");
        }
        return array('status' => "error", 'message' => "Not found id");
    }

    public static function addCommandKillAll($live) {
        $client = Zliveclient::where("client_id", $live->server_id)->where("status", "<>", 6)->first();
        if ($client) {
            $command = Command::where("server_id", $live->server_id)->where("live_id", $live->id)->where("key_live", $live->key_live)->where("status", 0)->first();
            if (!$command) {
                $command = new Command();
                $command->server_id = $live->server_id;
                $command->password = $client->client_pass;
                $command->live_id = $live->id;
                $command->key_live = $live->key_live;
                $command->created = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $command->save();
            }
        }
    }

}
