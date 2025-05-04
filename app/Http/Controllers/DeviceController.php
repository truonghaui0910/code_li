<?php

namespace App\Http\Controllers;

use App\Http\Models\TiktokDevicePc;
use Illuminate\Http\Request;
use Log;

class DeviceController extends Controller {

    public function registerDevice(Request $request) {
        Log::info('|DeviceController.registerDevice|request=' . json_encode($request->all()));


        $check = TiktokDevicePc::where("md5", $request->md5)->first();
        if (!$check) {
            $insert = new TiktokDevicePc();
            $insert->md5 = $request->md5;
            $insert->hex_data = $request->hex;
            $insert->pc_uuid = $request->pc_uuid;
            $insert->save();
            return response()->json(["status" => "success", "message" => "Success"], 200);
        }
        return response()->json(["status" => "error", "message" => "Exists"], 200);
    }

    public function loadDevice($id) {
        Log::info("|DeviceController.loadDevice|request=id=$id");
        $result = TiktokDevicePc::where("id", $id)->first();
        return response()->json(["status" => "success", "data" => $result], 200);
    }

}
