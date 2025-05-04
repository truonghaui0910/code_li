<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Network;

use Log;

class ProxyHelper {

//    public $AGENT_MOBILE = 'Nokia6300/2.0 (05.00) Profile/MIDP-2.0 Configuration/CLDC-1.1 nokia6300 UNTRUSTED/1.0';
//    public $AGENT_DESKTOP = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';
//    public $AGENT_GOOGLE_BOT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
//    public $AGENT_NOTHING_TYPE = 0;
//    public $AGENT_MOBILE_TYPE = 1;
//    public $AGENT_DESKTOP_TYPE = 2;
//    public $AGENT_GOOGLE_BOT_TYPE = 3;
    //put your code here
    public static function get($url, $typeUserAgent = 1) {
//        error_log("ProxyHelper $url");
        ini_set('max_execution_time', 40);
        $userAgent = "";
        $cookie_file = "";
        //$cookie_file = Utils::download_cookie();
//        $proxy_port = 9000;
//        $proxy_ip = "usa.rotating.proxyrack.net";
//        $loginpassw = "dunndealpr:0ddf2c-02b7b2-7c80d6-6958a3-468cdb";
        $proxy_port = 80;
        $proxy_ip = "p.webshare.io";
        $loginpassw = "jcqpvqte-rotate:tedzuonzv900";
        switch ($typeUserAgent) {
            case 0:
                $userAgent = "";
                break;
            case 1:
                $userAgent = 'Nokia6300/2.0 (05.00) Profile/MIDP-2.0 Configuration/CLDC-1.1 nokia6300 UNTRUSTED/1.0';
                break;
            case 2:
                $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
                break;
            case 3:
                $userAgent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
                break;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Accept-Language: en-US",
                "CONSENT: PENDING+932"
            ),
//            CURLOPT_COOKIEFILE => $cookie_file,
            CURLOPT_USERAGENT => $userAgent,
        ));
        curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($curl, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, $loginpassw);
        try {
            $response = curl_exec($curl);
//            Log::info($response);
            if ($error_number = curl_errno($curl)) {
                if (in_array($error_number, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
                    error_log("curl timed out");
                }
            }
        } catch (Exception $ex) {
            error_log("ProxyHelper error");
        }
//        error_log($response);

        $encot = false;
//        unset($charset);
        $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);
//        unlink($cookie_file);
        /* 1: HTTP Content-Type: header */
        preg_match('@([\w/+]+)(;\s*charset=(\S+))?@i', $content_type, $matches);
        if (isset($matches[3]))
            $charset = $matches[3];

        /* 2: <meta> element in the page */
        if (!isset($charset)) {
            preg_match('@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s*charset=([^\s"]+))?@i', $response, $matches);
            if (isset($matches[3]))
                $charset = $matches[3];
        }

        /* 3: <xml> element in the page */
        if (!isset($charset)) {
            preg_match('@<\?xml.+encoding="([^\s"]+)@si', $response, $matches);
            if (isset($matches[1]))
                $charset = $matches[1];
        }

        /* 4: PHP's heuristic detection */
        if (!isset($charset)) {
            $encoding = mb_detect_encoding($response);
            if ($encoding)
                $charset = $encoding;
        }

        /* 5: Default for HTML */
        if (!isset($charset)) {
            if (strstr($content_type, "text/html") === 0)
                $charset = "ISO 8859-1";
        }
        /* Convert it if it is anything but UTF-8 */
        /* You can change "UTF-8"  to "UTF-8//IGNORE" to 
          ignore conversion errors and still output something reasonable */
        if (isset($charset) && strtoupper($charset) != "UTF-8")
            $response = iconv($charset, 'UTF-8', $response);
        return ($response);
    }

    public static function youtube($data) {
//        error_log("ProxyHelper $url");
        ini_set('max_execution_time', 40);
        $proxy_port = 80;
        $proxy_ip = "p.webshare.io";
        $loginpassw = "jcqpvqte-rotate:tedzuonzv900";
        $headers = array(
//                "cache-control: no-cache",
            "Accept-Language: vi-VN,vi;q=0.8,en-US;q=0.5,en;q=0.3",
            "CONSENT: PENDING+932",
            "Accept-Encoding: gzip, deflate, br",
            "Content-type: application/json",
//                "Content-Length: ",
//                "Host: https://www.youtube.com",
            "Accept: */*",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0",
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.youtube.com/youtubei/v1/browse?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8&prettyPrint=false",
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
//                "cache-control: no-cache",
//                "Accept-Language: vi-VN,vi;q=0.8,en-US;q=0.5,en;q=0.3",
                "CONSENT: PENDING+932",
//                "Accept-Encoding: gzip, deflate, br",
                "Content-type: application/json",
//                "Content-Length: ",
//                "Host: https://www.youtube.com",
//                "Accept: */*",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0",
            )
//            ,
//            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0",          
        ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

//        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
////        curl_setopt($curl, CURLOPT_HEADER, false);
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
//        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 40000);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($curl, CURLOPT_POST, true);
//        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
//
//        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0");
        curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($curl, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, $loginpassw);
        try {
            $response = curl_exec($curl);
            error_log("response: $response");
            if ($error_number = curl_errno($curl)) {
                if (in_array($error_number, array(CURLE_OPERATION_TIMEDOUT, CURLE_OPERATION_TIMEOUTED))) {
                    error_log("curl timed out");
                }
            }
        } catch (Exception $ex) {
            error_log("ProxyHelper error");
        }
//        error_log($response);

        $encot = false;
//        unset($charset);
        $content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        curl_close($curl);
//        unlink($cookie_file);
        /* 1: HTTP Content-Type: header */
        preg_match('@([\w/+]+)(;\s*charset=(\S+))?@i', $content_type, $matches);
        if (isset($matches[3]))
            $charset = $matches[3];

        /* 2: <meta> element in the page */
        if (!isset($charset)) {
            preg_match('@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s*charset=([^\s"]+))?@i', $response, $matches);
            if (isset($matches[3]))
                $charset = $matches[3];
        }

        /* 3: <xml> element in the page */
        if (!isset($charset)) {
            preg_match('@<\?xml.+encoding="([^\s"]+)@si', $response, $matches);
            if (isset($matches[1]))
                $charset = $matches[1];
        }

        /* 4: PHP's heuristic detection */
        if (!isset($charset)) {
            $encoding = mb_detect_encoding($response);
            if ($encoding)
                $charset = $encoding;
        }

        /* 5: Default for HTML */
        if (!isset($charset)) {
            if (strstr($content_type, "text/html") === 0)
                $charset = "ISO 8859-1";
        }
        /* Convert it if it is anything but UTF-8 */
        /* You can change "UTF-8"  to "UTF-8//IGNORE" to 
          ignore conversion errors and still output something reasonable */
        if (isset($charset) && strtoupper($charset) != "UTF-8")
            $response = iconv($charset, 'UTF-8', $response);
        return ($response);
    }

    public static function bitly($token, $url, $method = "GET", $data = null) {
        $proxy_port = 80;
        $proxy_ip = "p.webshare.io";
        $loginpassw = "jcqpvqte-rotate:tedzuonzv900";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 100000);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        $header = array("Authorization: Bearer $token","Content-type: application/json");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        switch ($method) {
            case "GET":
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
//        curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
//        curl_setopt($curl, CURLOPT_PROXYTYPE, 'HTTP');
//        curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);
//        curl_setopt($curl, CURLOPT_PROXYUSERPWD, $loginpassw);
        $response = curl_exec($curl);
//        Log::info($response);

        curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $datas = json_decode($response);
        return $datas;
    }

}
