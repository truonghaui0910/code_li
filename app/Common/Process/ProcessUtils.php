<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Process;

use App\Http\Models\LockProcess;
use App\Http\Models\Z1DownloadVideo;
use Log;
use Illuminate\Http\Request;

/**
 * Description of ProcessUtils
 *
 * @author hoabt2
 */
class ProcessUtils {

    public static function isfreeProcess($processName) {
        $lockProcess = LockProcess::where("process", $processName)->first();
        if ($lockProcess) {
            if ($lockProcess->status == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    //put your code here
    public static function lockProcess($processName) {
        $lockProcess = LockProcess::where("process", $processName)->first();
        $now = time();
        if ($lockProcess) {
            $lockProcess->status = 1;
            $lockProcess->time = $now;
//            $lockProcess->time_gmt7 = gmdate("Y/m/d H:i:s", $now + 3600 * (7 + date("I")));
        } else {
            $lockProcess = new LockProcess();
            $lockProcess->process = $processName;
            $lockProcess->status = 1;
            $lockProcess->time = $now;
//            $lockProcess->time_gmt7 = gmdate("Y/m/d H:i:s", $now + 3600 * (7 + date("I")));
        }
        $lockProcess->save();
    }

    public static function unLockProcess($processName) {
        $now = time();
        $lockProcess = LockProcess::where("process", $processName)->first();
        if ($lockProcess) {
            $lockProcess->status = 0;
//            $lockProcess->execute_time = $now - $lockProcess->time;
            $lockProcess->time = $now;
//            $lockProcess->time_gmt7 = gmdate("Y/m/d H:i:s", $now + 3600 * (7 + date("I")));
        } else {
            $lockProcess = new LockProcess();
            $lockProcess->process = $processName;
            $lockProcess->status = 0;
//            $lockProcess->execute_time = $now - $lockProcess->time;
            $lockProcess->time = $now;
//            $lockProcess->time_gmt7 = gmdate("Y/m/d H:i:s", $now + 3600 * (7 + date("I")));
        }
        $lockProcess->save();
    }

//    public static function getListDownload($status) {
//        try {
//            if (!isset($status)) {
//                $status = 0;
//            }
//            $listVideo = Z1DownloadVideo::where('status', $status)->limit(2)->orderBy('id', 'asc')->get();
//            return json_encode($listVideo);
//        } catch (Exception $exc) {
//            Log::info($exc->getTraceAsString());
//        }
//    }

//    public static function updateDownload(Request $request) {
//        Log::info('updateDownload|request=' . json_encode($request->all()));
//        try {
//            if (!isset($request->id)) {
//                return 0;
//            }
//            $video = Z1DownloadVideo::find($request['id']);
//            if ($video) {
//                if (isset($request['status'])) {
//                    $video->status = $request['status'];
//                    $video->update_time = time();
//                }
//
//                if (isset($request['downloaded_url'])) {
//                    $video->downloaded_url = $request['downloaded_url'];
//                }
//
//                if (isset($request['video_title'])) {
//                    $video->video_title = $request['video_title'];
//                }
//
//                if (isset($request['video_des'])) {
//                    $video->video_des = $request['video_des'];
//                }
//
//                if (isset($request['video_tag'])) {
//                    $video->video_tag = $request['video_tag'];
//                }
//                if (isset($request['video_thumb'])) {
//                    $video->video_thumb = $request['video_thumb'];
//                }
//                if (isset($request['video_size'])) {
//                    $video->video_size = $request['video_size'];
//                }
//                if (isset($request['video_duration'])) {
//                    $video->video_duration = $request['video_duration'];
//                }
//
//                $video->save();
//                return 1;
//            }
//        } catch (Exception $exc) {
//            Log::info($exc->getTraceAsString());
//            return 0;
//        }
//        return 0;
//    }

    public static function convertTime($sencondInput, $micros = '0') {
        $hour = floor($sencondInput / 3600);
        $minute = floor(($sencondInput - $hour * 60 * 60) / 60);
        $second = $sencondInput - $hour * 60 * 60 - $minute * 60;
        if ($sencondInput < 60) {
            return $sencondInput;
        } else if ($sencondInput >= 60 && $sencondInput < 3600) {
            return "$minute:$second";
        } else {
            return "$hour:$minute:$second";
        }
//        if ($micros == '0') {
//            return "$hour:$minute:$second.00";
//        } else {
//            list($hourX, $minuteX, $secondX, $titacX) = split('[:.]', $micros);
//            $hour += intval($hourX);
//            $minute += intval($minuteX);
//            $second += intval($secondX);
//            return "$hour:$minute:$second.$titacX";
//        }
    }

    public static function convertSize($byte) {
        if ($byte == 0 || $byte == null) {
            return 0;
        }
        $kb = $byte / 1024;
        $mb = $kb / 1024;
        $gb = $mb / 1024;
        if ($kb < 1024) {
            return ceil($kb) . ' KB';
        } else if ($kb >= 1024 && $kb < (1024 * 1024)) {
            return ceil($mb) . ' MB';
        } else {
            return ceil($gb) . ' GB';
        }
    }

}
