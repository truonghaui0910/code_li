<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author hoabt2
 */

namespace App\Common;

use Illuminate\Support\Facades\Config;

class Utils {

    public static function randomString($length=10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    public static function encodeData($data) {
        return urlencode(base64_encode(json_encode($data)));
    }

    public static function decodeData($data) {
        return json_decode(base64_decode(urldecode($data)));
    }

    public static function log($job, $response) {
        $arrTmp = explode(";;", $job->log);
        if (count($arrTmp) > Config::get('config.max_rows_log')) {
            array_shift($arrTmp);
        }
        $arrTmp[] = $response;
        $job->log = implode(";;", $arrTmp);
    }

    public static function getUniqueKey($key) {
        $index = 0;
        $path = __DIR__ . "/index$key";
        if (!file_exists($path)) {
            file_put_contents($path, $index, LOCK_EX);
            return $index;
        }
        $fp = fopen($path, "r+");
        while (!flock($fp, LOCK_EX)) {  // acquire an exclusive lock
            usleep(100);
        }
        $indexS = fread($fp, filesize($path));
        $index = intval(trim($indexS));
        $index = $index + 1;
        fseek($fp, 0);
        fwrite($fp, "$index");
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $index;
    }

    public static function getUniqueKey_old1($key) {
        $index = 0;
        $path = __DIR__ . "\index$key";
        if (!file_exists($path)) {
            file_put_contents($path, $index, LOCK_EX);
            return $index;
        }
        $fp = fopen($path, "r+");
        while (!flock($fp, LOCK_EX)) {  // acquire an exclusive lock
            usleep(100);
        }
        $indexS = file_get_contents($path);
        $index = intval(trim($indexS));
        $index = $index + 1;
        fwrite($fp, "$index");
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $index;
    }

    public static function containString($data, $need_find) {
        if (strpos($data, $need_find) !== false) {
            return true;
        }
        return false;
    }

    //hàm check array string có nằm trong string khác không
    public static function containArrayString($bigString, $arrayNeedFind) {
        foreach ($arrayNeedFind as $needFind) {
            if (strpos(strtolower($bigString), strtolower($needFind)) !== false) {
                return true;
            }
        }
        return false;
    }

    public static function compare($value1, $value2, $operator, $isDateTime = false) {
        if (!isset($value1) || !isset($value2) || !isset($operator)) {
            return true;
        }

        $result = false;
        switch ($operator) {
            case ">":
                $result = $value1 > $value2;
                break;
            case "<":
                $result = $value1 < $value2;
                break;
            case "=":
                if ($isDateTime) {
                    if (preg_match("/20\d\d00/", $value1) || preg_match("/20\d\d00/", $value2)) {
                        $value1 = substr($value1, 0, -3);
                        $value2 = substr($value2, 0, -3);
                    }
                }
                $result = $value1 == $value2;
                break;
        }
        return $result;
    }

    public static function calcTime($totalAdded, $rate) {
        $now = time();
        $bias = Config::get('config.bias_next_time_run');
        $eachVideoTime = Config::get('config.time_each_video');
        $tmp = $totalAdded * $rate * $eachVideoTime + $bias;
        $nextTime = $now + rand(intval(0.8 * $tmp), $tmp);
        return $nextTime;
    }

    public static function calcTypeVideo($totalPriority, $totalNormal, $rate) {
        if ($totalPriority < 1 && $totalNormal < 1) {
            return 0;
        }
        if ($totalPriority < 1) {
            return 2;
        }
        if ($totalNormal < 1) {
            return 1;
        }
        if (floatval($totalPriority) / floatval($totalNormal) >= $rate) {
            return 1;
        }
        return 2;
    }

    public static function countDayLeft($date) {
        if ($date <= time()) {
            return "Đã hết hạn";
        }
        $timeMinute = floor(($date - time()) / 60);
        $timeHour = floor($timeMinute / 60);
        $timeDay = floor($timeHour / 24);
        if ($timeMinute > 0 && $timeMinute < 60) {
            return "Còn $timeMinute phút";
        } else if ($timeHour > 0 && $timeHour < 24) {
            return "Còn " . $timeHour . ($timeHour == 1 ? ' giờ' : ' giờ');
        } else if ($timeDay > 0) {
            return "Còn " . $timeDay . ($timeDay == 1 ? ' ngày' : ' ngày');
        }
    }

    public static function generateRandomString($length = 16) {
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

    public static function timeToStringGmT7($time) {
        return gmdate("Y/m/d H:i:s", $time + (7 * 3600));
    }

    public static function timeText($time) {
        if ($time == null) {
            return "N/A";
        }
        $timeMinute = floor((time() - $time) / 60);
        $timeHour = floor($timeMinute / 60);
        $timeDay = floor($timeHour / 24);
        if ($timeMinute == 0) {
            return 'Vừa xong';
        } else if ($timeMinute > 0 && $timeMinute < 60) {
            return $timeMinute . ' phút';
        } else if ($timeHour > 0 && $timeHour < 24) {
            return $timeHour . ' giờ';
        } else if ($timeDay > 0) {
            return $timeDay . ' ngày';
        }
    }

    public static function getUserIpAddr() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

    public static function slugify($text, string $divider = '-') {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function randomDigit($number) {
        $characters = '0123456789';
        $randstring = '';
        for ($i = 0; $i < $number; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    public static function uniqidReal($lenght = 13) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }

    public static function getIp($country) {
        $vn = ["14.169", "171.244", "42.117", "113.186", "113.23", "222.254", "14.161", "123.2", "113.162", "113.181", "14.177", "113.172", "58.187", "14.231", "123.16", "171.224", "14.191", "123.24", "14.241", "14.232", "14.186", "222.253", "123.21", "14.187", "113.167", "113.161", "14.226"];
        $us = ["192.228", "73.199", "75.188", "138.112", "73.16", "104.129", "72.76", "73.33", "72.9", "216.93", "71.184", "50.86", "154.27", "73.168", "173.63", "24.47", "174.3", "108.24", "174.166"];
        $gb = ["86.141", "2.3", "81.77", "109.18", "82.37", "31.205"];
        $th = ["202.28", "223.27", "223.204", "183.88", "180.183", "203.154", "14.207", "110.169", "58.136", "49.49", "159.192", "184.22"];
        $ip_a = "14.169";
        switch ($country) {
            case 'vn':
                $ip_a = $vn[array_rand($vn)];
                break;
            case 'us':
                $ip_a = $us[array_rand($us)];
                break;
            case 'gb':
                $ip_a = $gb[array_rand($gb)];
                break;
            case 'th':
                $ip_a = $th[array_rand($th)];
                break;
        }
        $ip = "$ip_a." . random_int(5, 250) . "." . random_int(5, 250);
        return $ip;
    }

    public static function getDriveID($gdriveurl) {
        // <editor-fold defaultstate="collapsed" desc="old">
//        $filter1 = preg_match('/drive\.google\.com\/open\?id\=(.*)/', $gdriveurl, $fileid1);
//        $filter2 = preg_match('/drive\.google\.com\/file\/d\/(.*?)\//', $gdriveurl, $fileid2);
//        $filter3 = preg_match('/drive\.google\.com\/uc\?id\=(.*?)\&/', $gdriveurl, $fileid3);
//        $filter4 = preg_match('/drive\.usercontent\.google\.com\/uc\?id\=(.*?)\&/', $gdriveurl, $fileid4);
//        if ($filter1) {
//            $fileid = $fileid1[1];
//        } else if ($filter2) {
//            $fileid = $fileid2[1];
//        } else if ($filter3) {
//            $fileid = $fileid3[1];
//        } else if ($filter4) {
//            $fileid = $fileid4[1];
//        } else {
//            $fileid = null;
//        }
//
//        return($fileid);
// </editor-fold>
        // <editor-fold defaultstate="collapsed" desc="new">
        $filter3 = preg_match('/id\=(.*?)\&/', $gdriveurl, $fileid3);
        $filter4 = preg_match('/\/d\/(.*?)\//', $gdriveurl, $fileid4);
        if ($filter3) {
            $fileid = $fileid3[1];
        } else if ($filter4) {
            $fileid = $fileid4[1];
        } else {
            $fileid = null;
        }

        return($fileid);

// </editor-fold>
    }

    public static function convertSecondsToTime($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        // Định dạng lại phút và giây để có đủ 2 chữ số (nếu cần thiết)
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $formattedSeconds = str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);

        if ($hours > 0) {
            $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
            return "{$formattedHours}:{$formattedMinutes}:{$formattedSeconds}";
        } else {
            return "{$formattedMinutes}:{$formattedSeconds}";
        }
    }

    public static function getNumberFromText($string_number) {
        $result = 0;
        $temp = str_replace("views", "", $string_number);
        $temp = str_replace("view", "", $temp);
        $temp = str_replace("videos", "", $temp);
        $temp = str_replace("video", "", $temp);
        $temp = str_replace("dislikes", "", $temp);
        $temp = str_replace("dislike", "", $temp);
        $temp = str_replace("likes", "", $temp);
        $temp = str_replace("like", "", $temp);
        $temp = str_replace("subscribers", "", $temp);
        $temp = str_replace(",", "", $temp);
        if (self::containString("$temp", "K") || self::containString("$temp", "M") || self::containString("$temp", "B")) {
            $temp = self::shortNumber2Number($temp);
        } else {
            $temp = str_replace(".", "", $temp);
        }
        if (is_numeric(trim($temp))) {
            $result = intval(trim($temp));
        }
        return $result;
    }

    public static function shortNumber2Number($shortNumber) {
        if (strpos(strtoupper($shortNumber), "K") != false) {
            $shortNumber = rtrim($shortNumber, "kK");
            return floatval($shortNumber) * 1000;
        } else if (strpos(strtoupper($shortNumber), "M") != false) {
            $shortNumber = rtrim($shortNumber, "mM");
            return floatval($shortNumber) * 1000000;
        } else if (strpos(strtoupper($shortNumber), "B") != false) {
            $shortNumber = rtrim($shortNumber, "bB");
            return floatval($shortNumber) * 1000000000;
        } else if (strpos(strtoupper($shortNumber), "T") != false) {
            $shortNumber = rtrim($shortNumber, "tT");
            return floatval($shortNumber) * 1000000000000;
        } else {
            return floatval($shortNumber);
        }
    }

    public static function number2ShortNumber($num) {
        if ($num > 1000) {
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('K', 'M', 'B', 'T');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;
        }
        return $num;
    }

    public static function write($fileName, $content) {
        $myfile = fopen($fileName, "w") or die("Unable to open file!");
        fwrite($myfile, $content);
        fclose($myfile);
    }

}
