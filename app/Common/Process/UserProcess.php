<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Process;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Description of UserProcess
 *
 * @author hoabt2
 */
class UserProcess {

    public function scanExpiredUser($threadId) {
        $processName = "scan-expired-user-$threadId";
        $numberThread = Config::get('config.number_thread_scan_expired_user');
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $jobs = \App\User::where(DB::raw("id % $numberThread"), $threadId)->where("expired_scan", 0)->where("package_end_date", "<", time())->limit(50)->get();
            echo "total expired: " . count($jobs) . " ---->";
            foreach ($jobs as $job) {
                $now = time();
                \App\Http\Models\PlaylistDetail::where("user_id", $job->id)->where("status", 2)->update(['status' => 6]);
                \App\Http\Models\BloggerDetail::where("user_id", $job->id)->where("status", 1)->update(['status' => 6]);
                $job->expired_scan = 1;
                $job->save();
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            echo "Process $processName locked. next time";
        }
    }

    public function scanPrepareExpiredUser($threadId) {
        $processName = "scan-prepare-expired-user-$threadId";
        $numberThread = Config::get('config.number_thread_scan_prepare_expired_user');
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $now = time();
            $next10days = $now + 259200;
            $jobs = \App\User::where(DB::raw("id % $numberThread"), $threadId)->where("expired_scan", 0)->where("package_end_date", "<", $next10days)->where("next_time_scan_notify", "<", $now)->limit(50)->get();
            echo "total Prepare expired: " . count($jobs) . " ---->";
            foreach ($jobs as $job) {
                $notification = new \App\Http\Models\Notification();
                $notification->title = "Account Expired!";
                $notification->content = "Account Expired!";
                $notification->type = 2;
                $notification->user_id = $job->id;
                $notification->create_time = $now;
                $notification->start_date = $now;
                $notification->end_date = $now + 259200;
                $notification->read_status = 0;
                $notification->save();
                $job->expired_scan = 1;
                $job->next_time_scan_notify = $now + 86400;
                $job->save();
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            echo "Process $processName locked. next time";
        }
    }

}
