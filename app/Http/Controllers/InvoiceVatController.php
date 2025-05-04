<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Http\Models\InvoiceVat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function GuzzleHttp\json_encode;
use function view;

class InvoiceVatController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|InvoiceVatController|index=' . json_encode($request->all()));
        $datas = InvoiceVat::where("del_status", 0);
        $queries = [];

        $limit = 20;
        if (isset($request->limit)) {
            if ($request->limit <= 200 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        if (isset($request->status_vat) && $request->status_vat != -1) {
            if ($request->status_vat == "0") {
                $datas = $datas->whereNull("vat_code");
            } elseif ($request->status_vat == "1") {
                $datas = $datas->whereNotNull("vat_code");
            }
            $queries['status_vat'] = $request->status_vat;
        }
        if (isset($request->filter_data) && $request->filter_data != '') {
            $datas = $datas->where(function($q) use ($request) {
                $q->where('user_name', 'like', '%' . $request->filter_data . '%')->orWhere('id', $request->filter_data)->orWhere('invoice_id', 'like', '%' . $request->filter_data . '%');
                if (Utils::containString($request->filter_data, ",")) {
                    $c1 = explode(',', $request->filter_data);
                    $arrayChannel = [];
                    foreach ($c1 as $arr) {
                        if ($arr != "") {
                            $arrayChannel[] = trim($arr);
                        }
                    }
                    $q->orWhereIn("id", $arrayChannel)->orWhereIn("invoice_id", $arrayChannel)->orWhereIn("user_name", $arrayChannel);
                }
            });
            $queries['filter_data'] = $request->filter_data;
        }

        if (isset($request->sort)) {
            $queries['sort'] = $request->sort;
            if (isset($request->order)) {
                $queries['order'] = $request->order;
            }
        } else {
            //set mặc định sẽ search theo last_number_view_playlist desc
            $request['sort'] = 'id';
            $request['order'] = 'desc';
            $queries['sort'] = 'id';
            $queries['order'] = 'desc';
        }
        $datas = $datas->sortable()->paginate($limit)->appends($queries);

        foreach ($datas as $data) {
            
        }
        $summary = DB::select("SELECT SUM(CASE WHEN vat_code IS NULL THEN 1 ELSE 0 END) AS NOT_VAT,
                                            SUM(CASE WHEN vat_code IS NOT NULL THEN 1 ELSE 0 END) AS VAT
                                            FROM 
                                            invoice_vat where del_status =0");
//        Log::info(DB::getQueryLog());
//        Log::info(json_encode($summary));
        return view('components.invoice_vat', [
            'datas' => $datas,
            'request' => $request,
            'status_vat' => $this->genStatusInvoiceVat($request),
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
            'summary' => $summary,
        ]);
    }

    public function update(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|InvoiceVatController.update|request=" . json_encode($request->all()));
        if (!$request->isAdmin && !$request->isTax) {
            return array("status" => "error", "message" => "Bạn không có quyền");
        }
        if (isset($request->id)) {
            $invoice = InvoiceVat::find($request->id);
            if (!$invoice) {
                return array("status" => "error", "message" => "Not found $request->id");
            }
            if ($request->action == "delete") {
                $invoice->del_status = 1;
                $invoice->system_update_date = time();
            }
            if (isset($request->vat_code)) {
                $invoice->vat_code = trim($request->vat_code);
                $invoice->system_update_date = time();
            }
            $invoice->save();
            return array("status" => "success", "message" => "Success");
        }
        return array("status" => "error", "message" => "Not found $request->id");
    }

    public function stats(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|InvoiceVatController.stats|request=" . json_encode($request->all()));
        $totalMoon = DB::select("SELECT 
                                    CASE 
                                        WHEN bank IS NULL THEN 'total'
                                        ELSE bank
                                    END AS bank_name,
                                    COUNT(*) AS total_count,
                                    SUM(payment_money) AS total_payment_money
                                FROM 
                                    invoice
                                WHERE status =1 and del_status=0 and period = $request->period
                                GROUP BY 
                                    bank WITH ROLLUP;");
        return response()->json($totalMoon);
    }

}
