<?php

namespace App\Common;

use App\Http\Models\Zliveclient;
use Illuminate\Support\Facades\DB;
use Log;

class Client {

    public static function getAvailableFile($live, $size) {
        $locker = new Locker(12345);
        $locker->lock();
//        DB::enableQueryLog();
        //2023/01/23 phân chia sv, nếu tiktok thì  chon stream = 2,youtube stream = 1
        $where = " and streaming = 1";
        if ($live->platform == 2) {
            $where = " and streaming = 2 ";
            //2024/08/23 nếu chạy tiktok vn thì cho vào sv
//            $profile = \App\Http\Models\TiktokProfile::where("id", $live->tiktok_profile_id)->first();
//            if ($profile->priority_region == 'vn' && $profile->stl_token == null) {
//                $where .= " and cluster = 'vn' ";
//            }
        }
//        elseif ($live->platform == 3) {
//            $where = " and streaming = 3 ";
//        }
//        $client = Zliveclient::whereRaw("max >= 30 and status=1 and file=1 and health=0 and process<max and disk_free > $size and version > 3.05 and client_id <> '$lastIp' order by (process/max) * rate_file asc")->first();
//        if (!$client) {
        $client = Zliveclient::whereRaw("status=1 $where and file=1 and health=0 and process<max and disk_free > $size and version >= 3.11 and client_id <> '$live->server_id' order by (process/max) * rate_file asc")->first();
//        }
        if ($client) {
            $client->process = $client->process + 1;
            $client->disk_free = $client->disk_free - $size;
            $client->save();
        }
//        Log::info(DB::getQueryLog());
        $locker->unlock();
        return $client;
    }

    public static function getAvailableStream() {
        $locker = new Locker(12346);
        $locker->lock();
        $client = Zliveclient::whereRaw("status=1 and streaming=1 and process<max order by (process/max)*rate_streaming asc")->first();
        if ($client) {
            $client->process = $client->process + 1;
            $client->save();
        }
        $locker->unlock();
        return $client;
    }

    public static function getOnlyAvailableUser($cus_id, $size) {
        $locker = new Locker(12347);
        $locker->lock();
        $client = Zliveclient::whereRaw("status=5 and endcode=1 and health=0 and cus_id ='$cus_id' and process<max and disk_free > $size order by process asc")->first();

        if ($client) {
            $client->process = $client->process + 1;
            $client->disk_free = $client->disk_free - $size;
            $client->save();
        }
        $locker->unlock();
        return $client;
    }

    public static function killProcess($live) {
        sleep(3);
        $client = Zliveclient::where("client_id", $live->server_id)->first();
        if ($client) {
            $pass = $client->client_pass;
            $cmd = "java -jar /home/autolive_cmd/cmd.jar kill-all $client->client_id $pass $live->id $live->key_live";
            Log::info("kill $cmd");
            $rs = shell_exec($cmd);
            Log::info("killrs $rs");
        }
    }

}
