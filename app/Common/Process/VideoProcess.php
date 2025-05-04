<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Process;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Common\Youtube\BlogHelper;
use App\Common\Network\ClientHelper;

/**
 * Description of VideoProcess
 *
 * @author hoabt2
 */
class VideoProcess {

    public function sync($threadId) {
        $processName = "video-seo-sync-$threadId";
        $numberThread = Config::get('config.number_thread_video_seo_sync');
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $jobs = \App\Http\Models\VideoSeo::where(DB::raw("id % $numberThread"), $threadId)->where("status", 1)->where("next_time_scan", "<", time())->limit(50)->get();
            $arrUrlClients = ClientHelper::getAviableClients();
            $totalClient = count($arrUrlClients);
            $index = 0;
            $maxSyncLog = Config::get('config.max_sync_log');
            echo "total video seo: " . count($jobs) . " ---->";
            foreach ($jobs as $job) {
                $now = time();
                $res = \App\Common\Youtube\YoutubeHelper::getVideoInfo($job->video_id, $arrUrlClients[$index % $totalClient]);
                if ($res["status"] == 1) {
                    $job->status = 1;
                    $job->video_title = $res["title"];
                    $viewDetails = json_decode($job->view_detail);
                    if (count($viewDetails) > $maxSyncLog) {
                        array_shift($viewDetails);
                    }
                    $arrTmpView = array();
                    $arrTmpView["time"] = $now;
                    $arrTmpView["view"] = $res["view"];
                    $viewDetails[] = $arrTmpView;
                    $job->view_detail = json_encode($viewDetails);
                    if ($res["view"] > 0 && $res["view"] > $job->view_total) {
                        $job->view_increase = $res["view"] - $job->view_total;
                        $job->view_total = $res["view"];
                    }
                    if ($res["like"] > 0 && $res["like"] > $job->like_total) {
                        $job->like_increase = $res["like"] - $job->like_total;
                        $job->like_total = $res["like"];
                    }
                } else {
                    $job->status = 2;
                }
                $index++;
                $job->next_time_scan = $now + Config::get('config.time_delay_video_seo_sync');
                $job->save();
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            echo "Process $processName locked. next time";
        }
    }

}
