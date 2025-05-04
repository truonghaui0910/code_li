<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Common\Network;

use App\Common\Utils;
use Illuminate\Support\Facades\DB;

/**
 * Description of ClientHelper
 *
 * @author hoabt2
 */
class ClientHelper {

    //put your code here

    public static function getUniqueClient() {
        $keyIndexName = "RequestHelper";
        $clients = DB::select("select url_client from client where status>0 and health=0 order by status asc");
        $urlClient = null;
        if (isset($clients) && count($clients) > 0) {
            $keyIndex = Utils::getUniqueKey($keyIndexName);
            $urlClient = $clients[$keyIndex % count($clients)]->url_client;
        }
        return $urlClient;
    }

    public static function getAviableClients() {
        $keyIndexName = "RequestHelper";
        $clients = DB::select("select url_client from client where status>0 and health=0 order by status asc");
        $urlClient = null;
        $arrUrls = array();
        foreach ($clients as $client) {
            $arrUrls[] = $client->url_client;
        }
        return $arrUrls;
    }

}
