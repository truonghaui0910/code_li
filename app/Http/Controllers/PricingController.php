<?php

namespace App\Http\Controllers;

use App\Http\Models\Package;
use Illuminate\Http\Request;
use Log;

class PricingController extends Controller {

    public function index() {
        $datas = Package::where("package_type", 0)->where("platform", 1)->where("status", 1)->orderBy("order_package")->get();
//        Log::info(json_encode($datas));
        $current = Auth()->user()->package_code;
        $currentOrder = 1;
        foreach ($datas as $data) {
            $data->btn_text = 'Mua Ngay';
            $data->btn_class = 'btn-warning';
            if ($current == $data->package_code) {
                $data->btn_text = 'Gia Hạn';
                $data->btn_class = 'btn-violet';
                $currentOrder = $data->order_package;
            }
            if ($currentOrder != 1 && $data->order_package > $currentOrder) {
                $data->btn_text = 'Nâng Cấp';
                $data->btn_class = 'btn-danger';
            }
        }
        return view('components.pricing', ["datas" => $datas]);
    }

    public function indexTiktok() {
        $current = Auth()->user()->tiktok_package;
        $datas = Package::where("package_type", 0)->where("platform", 2)->where("status", 1)->orderBy("order_package")->get();
        $currentOrder = 1;
        foreach ($datas as $data) {
            $data->btn_text = 'Mua Ngay';
            $data->btn_class = 'btn-warning';
            if ($current == $data->package_code) {
                $data->btn_text = 'Gia Hạn';
                $data->btn_class = 'btn-violet';
                $currentOrder = $data->order_package;
            }
            if ($currentOrder != 1 && $data->order_package > $currentOrder) {
                $data->btn_text = 'Nâng Cấp';
                $data->btn_class = 'btn-danger';
            }
        }
        return view('components.tiktok_pricing', ["datas" => $datas]);
    }

    public function indexShopee() {
        $current = Auth()->user()->shopee_package;
        $datas = Package::where("package_type", 0)->where("platform", 3)->where("status", 1)->orderBy("order_package")->get();
        $currentOrder = 1;
        foreach ($datas as $data) {
            $data->btn_text = 'Mua Ngay';
            $data->btn_class = 'btn-warning';
            if ($current == $data->package_code) {
                $data->btn_text = 'Gia Hạn';
                $data->btn_class = 'btn-violet';
                $currentOrder = $data->order_package;
            }
            if ($currentOrder != 1 && $data->order_package > $currentOrder) {
                $data->btn_text = 'Nâng Cấp';
                $data->btn_class = 'btn-danger';
            }
        }
        return view('components.shopee_pricing', ["datas" => $datas]);
    }

    public function indexAll() {
        $datas = Package::where("package_type", 0)->where("platform", 1)->where("status", 1)->orderBy("order_package")->get();
//        Log::info(json_encode($datas));
        $current = Auth()->user()->package_code;
        $currentOrder = 1;
        foreach ($datas as $data) {
            $data->btn_text = 'Mua Ngay';
            $data->btn_class = 'btn-warning';
            if ($current == $data->package_code) {
                $data->btn_text = 'Gia Hạn';
                $data->btn_class = 'btn-violet';
                $currentOrder = $data->order_package;
            }
            if ($currentOrder != 1 && $data->order_package > $currentOrder) {
                $data->btn_text = 'Nâng Cấp';
                $data->btn_class = 'btn-danger';
            }
        }

        $currentTt = Auth()->user()->tiktok_package;
        $datasTt = Package::where("package_type", 0)->where("platform", 2)->where("status", 1)->orderBy("order_package")->get();
        $currentOrderTt = 1;
        foreach ($datasTt as $data) {
            $data->btn_text = 'Mua Ngay';
            $data->btn_class = 'btn-warning';
            if ($currentTt == $data->package_code) {
                $data->btn_text = 'Gia Hạn';
                $data->btn_class = 'btn-violet';
                $currentOrderTt = $data->order_package;
            }
            if ($currentOrderTt != 1 && $data->order_package > $currentOrderTt) {
                $data->btn_text = 'Nâng Cấp';
                $data->btn_class = 'btn-danger';
            }
        }

        $currentSp = Auth()->user()->shopee_package;
        $datasSp = Package::where("package_type", 0)->where("platform", 3)->where("status", 1)->orderBy("order_package")->get();
        $currentOrderSp = 1;
        foreach ($datasSp as $data) {
            $data->btn_text = 'Mua Ngay';
            $data->btn_class = 'btn-warning';
            if ($currentSp == $data->package_code) {
                $data->btn_text = 'Gia Hạn';
                $data->btn_class = 'btn-violet';
                $currentOrderSp = $data->order_package;
            }
            if ($currentOrderSp != 1 && $data->order_package > $currentOrderSp) {
                $data->btn_text = 'Nâng Cấp';
                $data->btn_class = 'btn-danger';
            }
        }
        $default = "youtube";
        if ($current != "LIVETEST") {
            $default = "youtube";
        } elseif ($currentTt != "TIKTOKTEST") {
            $default = "tiktok";
        } elseif ($currentSp != "SHOPEETEST") {
            $default = "shopee";
        }

        return view('components.pricing_all', ["datas" => $datas, "datasTt" => $datasTt, "datasSp" => $datasSp, "default" => $default]);
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        //
    }

    public function show($id) {
        //
    }

    public function edit($id) {
        //
    }

    public function update(Request $request, $id) {
        //
    }

    public function destroy($id) {
        //
    }

    public static function convertPackage($package) {
        if ($package == "AutoLive 1" || $package == "LIVE1") {
            return "LIVE1";
        }
        if ($package == "AutoLive Test" || $package == "LIVETEST") {
            return "LIVETEST";
        }
        if ($package == "AutoTest" || $package == "LIVETEST") {
            return "LIVETEST";
        }
        if ($package == "AutoLive 3" || $package == "LIVE3") {
            return "LIVE3";
        }
        if ($package == "AutoLive 5" || $package == "LIVE5") {
            return "LIVE5";
        }
        if ($package == "AutoLive 10" || $package == "LIVE10") {
            return "LIVE10";
        }
        if ($package == "AutoLive VIP" || $package == "LIVEVIP") {
            return "LIVEVIP";
        }
        if ($package == "AutoVIP" || $package == "LIVEVIP") {
            return "LIVEVIP";
        }
        if ($package == "AutoLive 20" || $package == "LIVE20") {
            return "LIVE20";
        }
        if ($package == "AutoLive 16" || $package == "LIVE16") {
            return "LIVE16";
        }
        if ($package == "AutoLive 50" || $package == "LIVE50") {
            return "LIVE50";
        }
        return "NONE";
    }

}
