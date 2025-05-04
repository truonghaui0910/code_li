<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Process;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Common\Youtube\YoutubeHelper;
use App\Http\Models\Channel;
use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Common\Network\ClientHelper;

/**
 * Description of ChannelProcess
 *
 * @author hoabt2
 */
class ChannelProcess {

    public function channelScan($threadId) {
        $processName = "channel-scan-$threadId";
        $numberThread = Config::get('config.number_thread_scan_channel');
        if (ProcessUtils::isfreeProcess($processName)) {
            ProcessUtils::lockProcess($processName);
            $jobs = Channel::where(DB::raw("id % $numberThread"), $threadId)->where("status", 1)->where("next_time_scan", "<", time())->limit(50)->get();
            $arrUrlClients = ClientHelper::getAviableClients();
            $totalClient = count($arrUrlClients);
            $index = 0;
            echo "total: " . count($jobs) . " ---->";
            foreach ($jobs as $job) {
                $now = time();
                $dataInfo = YoutubeHelper::getChannelInfo($job->channel_id);
                $job->status = $dataInfo["status"];
                error_log("Scan: " . $job->channel_id . " -> " . json_encode($dataInfo));
                if ($job->status == 1) {
                    if ($dataInfo["views"] > $job->views) {
                        $job->views = $dataInfo["views"];
                    }
                    if ($dataInfo["subscribes"] > $job->subscribes) {
                        $job->subscribes = $dataInfo["subscribes"];
                    }
                }
                $index++;
                $job->next_time_scan = $now + Config::get('config.time_delay_scan_channel');
                $job->save();
            }
            ProcessUtils::unLockProcess($processName);
        } else {
            echo "Process $processName locked. next time";
        }
    }

}
