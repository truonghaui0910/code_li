<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Network;

use App\Common\Network\ClientHelper;
use Illuminate\Support\Facades\Config;
use TheSeer\Tokenizer\Exception;
use Log;

set_time_limit(0);

class RequestHelper {

    public static function postClient($urlClient, $data) {

        $json_response = null;
        try {
            $content = json_encode($data);
            $curl = curl_init($urlClient);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, 400);
            $json_response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
        } catch (Exception $ex) {
            
        }
        return $json_response;
    }

    public static function get($url, $UAType = 1) {
        /* $UAType
         * 0: nothing
         * 1: mobile
         * 2: desktop
         * 3: google bot
         */
        $urlClient = ClientHelper::getUniqueClient();
        $res = null;
        if (isset($urlClient)) {
            $urlClient .= "/api/proxy/get/";
            $data = array();
            $data["url"] = $url;
            $data["ua_type"] = $UAType;
            $res = self::postClient($urlClient, $data);
        }
        return $res;
    }

    public static function callAPI($method, $url, $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 10000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));

        switch ($method) {
            case "GET":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        $response = curl_exec($curl);
//        Log::info($response);
        $datas = json_decode($response);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Check the HTTP Status code
        switch ($httpCode) {
            case 200:
                $error_status = "200: Success";
                return ($datas);
            case 201:
                $error_status = "201: Success";
                return ($datas);
            case 401:
                $error_status = "401: Success";
                return ($datas);
            case 404:
                $error_status = "404: API Not found";
                break;
            case 500:
                $error_status = "500: servers replied with an error.";
                break;
            case 502:
                $error_status = "502: servers may be down or being upgraded. Hopefully they'll be OK soon!";
                break;
            case 503:
                $error_status = "503: service unavailable. Hopefully they'll be OK soon!";
                break;
            default:
                $error_status = "Undocumented error: " . $httpCode . " : " . curl_error($curl);
                break;
        }
        curl_close($curl);

        die;
    }

    public static function telegram($message) {
        $url = Config::get('config.telegram') . $message . '&parse_mode=html';
        self::callAPI("GET", $url, []);
    }

    public static function liveLog($message) {
        $url = Config::get('config.livelog') . $message . '&parse_mode=html';
        self::callAPI("GET", $url, []);
    }

    public static function getRequest($url) {
//        $content = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 600000);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, false);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200 && $status != 201) {
            Log::info("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }
        curl_close($curl);
        //$response = json_decode($json_response, true);
        return $json_response;
    }

    public static function checkProxy($proxy, $proxyauth = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://google.com");
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        if ($proxyauth != null) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
        }
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        //200 success
        return $httpcode;
    }

}
