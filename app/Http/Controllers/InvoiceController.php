<?php

namespace App\Http\Controllers;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Events\Notify;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceVat;
use App\Http\Models\Package;
use App\Http\Models\Zlivecustomer;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use function event;

class InvoiceController extends Controller {

    public function __construct() {
//        $this->middleware('auth');
    }

    public function index(Request $request) {
        DB::enableQueryLog();
        $user = Auth::user();
        Log::info($user->user_name . '|InvoiceController|index=' . json_encode($request->all()));
        $datas = Invoice::where("del_status", 0);
        $queries = [];
        $colFilter = [
            'invoice_id' => 'id', 'status' => 's'
        ];
        $limit = 10;
        if (isset($request->limit)) {
            if ($request->limit <= 200 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }
        foreach ($colFilter as $key => $value) {
            if (isset($request->$value)) {
                if ($value == 's' && $request->$value == '-1') {
                    
                } else {
                    if ($value == 's' && $request->$value == '2') {
                        //nếu tìm theo hóa đơn quá hạn
                        $datas = $datas->where('due_date', '<=', time())->where('status', 0);
                    } else {
                        $datas = $datas->where($key, 'like', '%' . $request->$value . '%');
                    }
                    $queries[$value] = $request->$value;
                }
            }
        }

        if (isset($request->from) && $request->from != "") {
            $from = str_replace("/", "", $request->from);
            $datas = $datas->where('date', '>=', $from);
            $queries['from'] = $from;
        }
        if (isset($request->to) && $request->to != "") {
            $to = str_replace("/", "", $request->to);
            $datas = $datas->where('date', '<=', $to);
            $queries['to'] = $to;
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
        $listUserId = array();
        $totalMoney = 0;
//        Log::info(DB::getQueryLog());
        foreach ($datas as $data) {
            if ($data->status == 1) {
                $totalMoney += $data->payment_money;
            }
            array_push($listUserId, $data->user_id);
        }
        $dataFacebook = User::whereIn('id', $listUserId)->get();

        return view('components.invoice', [
            'datas' => $datas,
            'dataFacebook' => $dataFacebook,
            'totalMoney' => $totalMoney,
            'request' => $request,
            'status' => $this->genStatusInvoice($request),
            'limitSelectbox' => $this->genLimit($request),
            'limit' => $limit,
        ]);
    }

    public function getInvoice(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|InvoiceController|getInvoice=$request->package");

        $package = Package::where("package_code", $request->package)->where("status", 1)->where("package_type", 0)->first();
        if (!$package) {
            return redirect('/pricing')->with("message", "Bạn không thể gia hạn gói này");
        }
        $check = 0;
        if ($package->platform == 3) {
            $basePrice = 300000;
//            $rediect = "/sppricing";
            $keyLiveKey = "shopee_key_live";
            $currentPackage = $user->shopee_package;
            $currenDateEnd = $user->shopee_end_date;
            $oldPackage = Package::where("package_code", $user->shopee_package)->where("status", 1)->first();
            if ($user->shopee_package != "SHOPEETEST" || (!empty($oldPackage->package_type) && $oldPackage->package_type == 1)) {
                $check = 1;
            }
        } elseif ($package->platform == 2) {
            $basePrice = 500000;
//            $rediect = "/ttpricing";
            $keyLiveKey = "tiktok_key_live";
            $currentPackage = $user->tiktok_package;
            $currenDateEnd = $user->tiktok_end_date;
            $oldPackage = Package::where("package_code", $user->tiktok_package)->where("status", 1)->first();
            if ($user->tiktok_package != "TIKTOKTEST" || (!empty($oldPackage->package_type) && $oldPackage->package_type == 1)) {
                $check = 1;
            }
            Log::info("old package: " . json_encode($oldPackage));
        } else {
            $basePrice = 200000;
//            $rediect = "/pricing";
            $keyLiveKey = "number_key_live";
            $currentPackage = $user->package_code;
            $currenDateEnd = $user->package_end_date;
            $oldPackage = Package::where("package_code", $user->package_code)->where("status", 1)->first();
            if ($user->package_code != "LIVETEST" || (!empty($oldPackage->package_type) && $oldPackage->package_type == 1)) {
                $check = 1;
            }
        }
        $rediect = "/pricing";
        if ($check) {
            if (!$oldPackage) {
                return redirect($rediect)->with("message", "Gói của bạn không được hỗ trợ thanh toán tự động, hãy liên hệ admin để gia hạn");
            }
        }
        if ($user->is_default == 2) {
            $mainUser = User::where("customer_id", $user->customer_id)->where("is_default", 1)->where("role", "like", "%2%")->first();
            $us = $mainUser ? " $mainUser->user_name " : "";
            return redirect($rediect)->with("message", "Tài khoản của bạn là tài khoản phụ, hãy dùng tài khoản chính là: '$us' để gia hạn");
        }
        $cus = Zlivecustomer::where("customer_id", $user->customer_id)->first();
        //nếu hạ gói thì phải thông báo admin với gói chưa hết hạn
//        Log::info(json_encode($package->number_live));
//        Log::info(json_encode($cus->$keyLiveKey));
        if ($package->number_live < $cus->$keyLiveKey && $currenDateEnd > time()) {
            return redirect($rediect)->with("message", "Bạn sử dụng gói cao hơn gói vừa chọn. Vui lòng liên hệ Admin để hỗ trợ");
        }

        //check tổng số luồng live ở các account con, nếu lớn hơn số lượng key_live của package thì thông báo phải thu hồi  luồng live về tk chính
        //kiểm tra số luồng đang live so với gói đã chọn
        $isOverLive = 0;
        //số luồng live cần dừng trước khi mua gói
        $stopLive = 0;
        $count = $this->countLivingPlaying($user->user_code, $package->platform);
        if ($count > $package->number_live) {
            $isOverLive = 1;
            $stopLive = $count - $package->number_live;
            return redirect($rediect)->with("message", "Bạn có $count luồng đang live, hãy dừng " . ($count - $package->number_live) . " luồng trước khi mua gói $package->package_code hoặc liên hệ admin để được hỗ trợ");
        }
        $month = 1;

        $currentTime = time();
        $invoiceId = strtoupper("$user->user_name$package->package_code" . '' . Utils::uniqidReal(3));
//        $invoiceId = str_replace(array('@', '!', '#',' ','_'), '', $invoiceId);
        $invoiceId = preg_replace('/[^A-Za-z0-9-]/', '', $invoiceId);
        $startDate = gmdate("Y/m/d H:i:s", $currentTime + 7 * 3600);
        $duaDate = gmdate("Y/m/d H:i:s", (2 * 86400) + ($currentTime + 7 * 3600));
        $total = $month * $package->price;

        list($dateRemain, $packageEndDate) = $this->calRemainDate($user, $oldPackage, $package, $month);
        list($dateRemain3, $packageEndDate3) = $this->calRemainDate($user, $oldPackage, $package, 3);
        list($dateRemain6, $packageEndDate6) = $this->calRemainDate($user, $oldPackage, $package, 6);
        list($dateRemain12, $packageEndDate12) = $this->calRemainDate($user, $oldPackage, $package, 12);
        $subTotal = $total;
        $dis3 = $package->discount_3;
        $dis6 = $package->discount_6;
        $dis12 = $package->discount_12;
        return view('components.payment', [
            "package" => $package,
            "invoiceId" => strtoupper($invoiceId),
            "startDate" => $startDate,
            "duaDate" => $duaDate,
            "total" => $total,
            "dateRemain" => $dateRemain,
            "dateRemain3" => $dateRemain3,
            "dateRemain6" => $dateRemain6,
            "dateRemain12" => $dateRemain12,
            "packageEndDate" => $packageEndDate,
            "packageEndDate3" => $packageEndDate3,
            "packageEndDate6" => $packageEndDate6,
            "packageEndDate12" => $packageEndDate12,
            "subTotal" => $subTotal,
            "dis3" => $dis3,
            "dis6" => $dis6,
            "dis12" => $dis12,
            "isOverLive" => $isOverLive,
            "stopLive" => $stopLive,
            "numberLiving" => $count,
            "basePrice" => $basePrice,
            "currentPackage" => $currentPackage,
            "currenDateEnd" => $currenDateEnd,
        ]);
    }

    public function postInvoice(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|InvoiceController|postInvoice=" . json_encode($request->all()));
        try {
            if (!isset($request->invoiceId)) {
                return array('status' => 'error', 'message' => 'Không tìm thấy thông tin hóa đơn');
            }
            if (!isset($request->package_code)) {
                return array('status' => 'error', 'message' => 'Không tìm thấy thông tin hóa đơn');
            }
            $month = 1;
            if (isset($request->month)) {
                $month = $request->month;
            }
            $packageCode = $request->package_code;
            $currentTime = time();
            $invoiceId = $request->invoiceId;
            $duaDate = $currentTime + (2 * 86400);

            //kiểm tra thông tin gói cước truyền lên
            $package = Package::where('package_code', $packageCode)->where('status', 1)->first();
            if (!$package) {
                return array('status' => 'error', 'message' => 'Không tìm thấy thông tin gói cước');
            }

            $check = 0;
            if ($package->platform == 3) {
                $platform = 3;
                $basePrice = 300000;
//                $rediect = "/sppricing";
                $currentPackage = $user->shopee_package;
                $currenDateEnd = $user->shopee_end_date;
                $currenLive = $user->shopee_key_live;
                $oldPackage = Package::where("package_code", $user->shopee_package)->where("status", 1)->first();
                if ($user->shopee_package != "SHOPEETEST" || (!empty($oldPackage->package_type) && $oldPackage->package_type == 1)) {
                    $check = 1;
                }
            } elseif ($package->platform == 2) {
                $platform = 2;
                $basePrice = 300000;
//                $rediect = "/ttpricing";
                $currentPackage = $user->tiktok_package;
                $currenDateEnd = $user->tiktok_end_date;
                $currenLive = $user->tiktok_key_live;
                $oldPackage = Package::where("package_code", $user->tiktok_package)->where("status", 1)->first();
                if ($user->tiktok_package != "TIKTOKTEST" || (!empty($oldPackage->package_type) && $oldPackage->package_type == 1)) {
                    $check = 1;
                }
            } else {
                $platform = 1;
                $basePrice = 200000;
//                $rediect = "/pricing";
                $currentPackage = $user->package_code;
                $currenDateEnd = $user->package_end_date;
                $currenLive = $user->number_key_live;
                $oldPackage = Package::where("package_code", $user->package_code)->where("status", 1)->first();
                if ($user->package_code != "LIVETEST" || (!empty($oldPackage->package_type) && $oldPackage->package_type == 1)) {
                    $check = 1;
                }
            }
            $rediect = "/pricing";
            if ($check) {
                if (!$oldPackage) {
                    return redirect($rediect)->with("message", "Gói của bạn không được hỗ trợ thanh toán tự động, hãy liên hệ admin để gia hạn");
                }
            }

//            $oldPackage = Package::where("package_code", $user->package_code)->where("status", 1)->first();
//            if (!$oldPackage) {
//                return redirect('/pricing')->with("message", "Gói của bạn không được hỗ trợ thanh toán tự động, hãy liên hệ admin để gia hạn");
//            }
            //validate invoice đã tồn tại hay chưa
            $invoice = Invoice::where('invoice_id', $invoiceId)->get();
            if (count($invoice) > 0) {
                return array('status' => 'error', 'message' => 'Hóa đơn đã tồn tại, hãy chờ vài phút để được xử lý');
            }
            $discountMonth = 0;
//            $note = null;
            $invoiceType = 0;
            //nếu nâng cấp gói cước thì mới tính $moneyRemain, gia hạn thì thôi
            if ($currentPackage != $package->package_code) {
                $invoiceType = 1;
//                $moneyRemain = $this->calRemainMoneyCustomer($user);
//                //từ số tiền còn lại chuyển thành số ngày của gói mới
//                $moneyPerDayNewPackage = $package->price / $package->duration;
//                $dateRemain = round($moneyRemain / $moneyPerDayNewPackage);
//                $note = "Cộng $dateRemain ngày, tính từ ngày còn lại của gói cũ $user->package_code live=$user->number_key_live expire=$user->package_end_date";
            }
            if ($month > 1) {
                $dcmonth = "discount_" . $month;
                $discountMonth = $package->$dcmonth;
            }

            list($dateRemain, $packageEndDate) = $this->calRemainDate($user, $oldPackage, $package, $month);
            $note = "Cộng $dateRemain ngày, tính từ ngày còn lại của gói cũ $currentPackage live=$currenLive expire=$currenDateEnd";

            $money = $month * $package->price - $discountMonth;
            $paymentMoney = $money - ($money * $package->discount_per / 100);

            // Xử lý thông tin hóa đơn VAT
            $invoiceInfo = null;
            if ($request->needInvoice == 1) {
                $invoiceInfo = [
                    'type' => $request->invoiceType,
                    'data' => []
                ];

                if ($request->invoiceType == 'personal') {
                    // Validate thông tin cá nhân
                    if (empty($request->personal_name) || empty($request->personal_id) ||
                            empty($request->personal_phone) || empty($request->personal_email) ||
                            empty($request->personal_address)) {
                        return array('status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin cá nhân');
                    }

                    $invoiceInfo['data'] = [
                        'name' => $request->personal_name,
                        'id_number' => $request->personal_id,
                        'phone' => $request->personal_phone,
                        'email' => $request->personal_email,
                        'address' => $request->personal_address
                    ];
                } elseif ($request->invoiceType == 'business') {
                    // Validate thông tin doanh nghiệp
                    if (empty($request->business_name) || empty($request->business_tax) ||
                            empty($request->business_phone) || empty($request->business_email) ||
                            empty($request->business_address)) {
                        return array('status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin doanh nghiệp');
                    }

                    $invoiceInfo['data'] = [
                        'company_name' => $request->business_name,
                        'tax_code' => $request->business_tax,
                        'phone' => $request->business_phone,
                        'email' => $request->business_email,
                        'address' => $request->business_address
                    ];
                }
            }


            $invoiceSave = new Invoice();
            $invoiceSave->platform = $platform;
            $invoiceSave->invoice_id = $invoiceId;
            $invoiceSave->payment_money = $paymentMoney;
            $invoiceSave->user_name = $user->user_name;
            $invoiceSave->package_code = $package->package_code;
            $invoiceSave->month = $month;
            $invoiceSave->number_live = $package->number_live;
            $invoiceSave->period = gmdate("Ym", time() + 7 * 3600);
            $invoiceSave->system_create_date = time();
            $invoiceSave->system_update_date = time();
            $invoiceSave->create_date = $currentTime;
            $invoiceSave->due_date = $duaDate;
            $invoiceSave->note = $note;
            $invoiceSave->date_remain = $dateRemain;
            $invoiceSave->last_package = $currentPackage;
            $invoiceSave->last_expire = $currenDateEnd;
            $invoiceSave->last_number_live = $currenLive;
            $invoiceSave->invoice_type = $invoiceType;
            $invoiceSave->log = Utils::timeToStringGmT7(time()) . " $user->user_name created invoice";
            if ($invoiceInfo) {
                $invoiceSave->vat_invoice_info = json_encode($invoiceInfo);
                $user->vat_invoice_info = json_encode($invoiceInfo);
                $user->save();
            }
            $invoiceSave->save();
//            $app = Config::get('config.app_url');
//            $message = "[INVOICE] Tài khoản " . strtoupper($user->user_name) . " vừa tạo hóa đơn $package->package_code/$month tháng (" . number_format($money, 0, ',', '.') . ").  <a href='$app/invoice?id=$invoiceId'><b>Đi kiểm tra</b></a>.";
            $message = "[INVOICE] Tài khoản " . strtoupper($user->user_name) . " vừa tạo hóa đơn $package->package_code/$month tháng (" . number_format($paymentMoney, 0, ',', '.') . ").";
            RequestHelper::telegram(urlencode($message));
            return array('status' => 'success', 'message' => 'Gửi hóa đơn thành công, hãy chờ một vài phút để được xử lý');
        } catch (Exception2 $e) {
            Log::info("Error: " . $e->getMessage());
            return array('content' => trans('label.message.error'));
        }
    }

    public function postInvoiceAdmin(Request $request) {
        $userAdmin = Auth::user();
        Log::info("$userAdmin->user_name|InvoiceController|postInvoiceAdmin=" . json_encode($request->all()));
        try {
            if (!in_array(1, explode(",", $userAdmin->role))) {
                return array('status' => 'error', 'message' => 'Bạn không có quyền');
            }
            $customer = User::find($request->invoice_user_id);
            if (!$customer) {
                return array('status' => 'error', 'message' => 'Không tìm thấy thông tin khách hàng');
            }
            if (!isset($request->package_code)) {
                return array('status' => 'error', 'message' => 'Không tìm thấy thông tin hóa đơn');
            }
            if (!isset($request->radio_bank)) {
                return array('status' => 'error', 'message' => 'Hãy chọn bank');
            }
            $month = 1;
            if (isset($request->month)) {
                $month = $request->month;
            }
            $packageCode = $request->package_code;
            $currentTime = time();

            $duaDate = $currentTime + (2 * 86400);

            //kiểm tra thông tin gói cước truyền lên
            $package = Package::where('package_code', $packageCode)->where('status', 1)->first();
            if (!$package) {
                return array('status' => 'error', 'message' => 'Không tìm thấy thông tin gói cước');
            }

            if ($package->platform == 1) {
                $packageCodeKey = "package_code";
                $packageStartDateKey = "package_start_date";
                $packageEndDateKey = "package_end_date";
                $numberKeyLiveKey = "number_key_live";
                $selectPlanKey = "select_plan";
                $dateEndKey = "date_end";
            } else if ($package->platform == 2) {
                $packageCodeKey = "tiktok_package";
                $packageStartDateKey = "tiktok_start_date";
                $packageEndDateKey = "tiktok_end_date";
                $numberKeyLiveKey = "tiktok_key_live";
                $selectPlanKey = "tiktok_plan";
                $dateEndKey = "tiktok_end_date";
            } else if ($package->platform == 3) {
                $packageCodeKey = "shopee_package";
                $packageStartDateKey = "shopee_start_date";
                $packageEndDateKey = "shopee_end_date";
                $numberKeyLiveKey = "shopee_key_live";
                $selectPlanKey = "shopee_plan";
                $dateEndKey = "shopee_end_date";
            }

            $oldPackage = Package::where("package_code", $customer->$packageCodeKey)->where("status", 1)->first();
            if (!$oldPackage) {
                return array('status' => 'error', 'message' => "Tài khoản này đang dùng gói cước không tồn tại");
            }
            //kiểm tra nếu user có tài khoản con thì phải đếm tổng number_key_live ở các tài khoản con
            $totalChildLive = User::where("customer_id", $customer->customer_id)->where("is_default", 2)->sum($numberKeyLiveKey);
            if ($request->number_key_live - $totalChildLive < 0) {
                return array('status' => 'error', 'message' => "Tài khoản này phải " . ($request->number_key_live - $totalChildLive) . " luồng ở tài khoản con");
            }

            $invoiceId = strtoupper("$customer->user_name$package->package_code" . '' . Utils::uniqidReal(3));
            $invoiceId = preg_replace('/[^A-Za-z0-9-]/', '', $invoiceId);
            $invoiceType = 0;
            //nếu nâng cấp gói cước thì mới tính dateRemain, gia hạn thì thôi
            if (($customer->$packageCodeKey != $package->package_code)) {
                $invoiceType = 1;
            }
            list($dateRemain, $packageEndDate) = $this->calRemainDate($customer, $oldPackage, $package, $month);
            $note = "Cộng $dateRemain ngày, tính từ ngày còn lại của gói cũ " . $customer->$packageCodeKey . " live=" . $customer->$numberKeyLiveKey . " expire=" . $customer->$packageEndDateKey;

            $money = str_replace(",", "", $request->money);

            $invoiceSave = new Invoice();
            $invoiceSave->bank = $request->radio_bank;
            $invoiceSave->platform = $package->platform;
            $invoiceSave->invoice_id = $invoiceId;
            $invoiceSave->payment_money = $money;
            $invoiceSave->user_name = $customer->user_name;
            $invoiceSave->package_code = $package->package_code;
            $invoiceSave->month = $month;
            $invoiceSave->number_live = $request->number_key_live;
            $invoiceSave->period = gmdate("Ym", time() + 7 * 3600);
            $invoiceSave->system_create_date = time();
            $invoiceSave->system_update_date = time();
            $invoiceSave->create_date = $currentTime;
            $invoiceSave->due_date = $duaDate;
            $invoiceSave->date_remain = $dateRemain;
            $invoiceSave->last_package = $customer->$packageCodeKey;
            $invoiceSave->last_expire = $customer->$packageEndDateKey;
            $invoiceSave->last_number_live = $customer->$numberKeyLiveKey;
            $invoiceSave->note = $note;
            $invoiceSave->invoice_type = $invoiceType;
            $invoiceSave->log = Utils::timeToStringGmT7(time()) . " $userAdmin->user_name created invoice";
            if (isset($request->auto_approve)) {
                // <editor-fold defaultstate="collapsed" desc="old">
//                $invoiceSave->status = 1;
//                $invoiceSave->period = gmdate("Ym", time() + 7 * 3600);
//                $invoiceSave->date = gmdate("Ymd", time() + 7 * 3600);
//                $invoiceSave->user_approve = $userAdmin->user_name;
//                $invoiceSave->log = $invoiceSave->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name approved";
//
//                //kiểm tra xem là loại invoice là gia hạn hay nâng cấp
//                if ($customer->$packageCodeKey != $package->package_code) {
//                    $packageEndDate = time() + ($invoiceSave->month * $package->duration * 86400) + ($invoiceSave->date_remain * 86400);
//                } else {
//                    //gia hạn
//                    if ($customer->$packageEndDateKey > time()) {
//                        //nếu tính đến hiện tại gói cước vẫn còn
//                        $packageEndDate = ($customer->$packageEndDateKey + ($invoiceSave->month * $package->duration * 86400));
//                    } else {
//                        //nếu đã hết hạn
//                        $packageEndDate = (time() + ($invoiceSave->month * $package->duration * 86400));
//                    }
//                }
//
//
//                $customer->$numberKeyLiveKey = $request->number_key_live - $totalChildLive;
//
//                $customer->status = 1;
//                $customer->$packageCodeKey = $packageCode;
//                $customer->$packageEndDateKey = $packageEndDate;
//                $customer->number_account = $request->number_account;
//                $customer->log = $customer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name approved $packageCode/$month month";
//                $customer->save();
//                User::where("customer_id", $customer->customer_id)->where("is_default", 2)->update([$packageEndDateKey => $packageEndDate, $packageCodeKey => $packageCode]);
//                $zlivecustomer = Zlivecustomer::where("customer_id", $customer->customer_id)->first();
//                if ($zlivecustomer) {
//                    $zlivecustomer->$numberKeyLiveKey = $request->number_key_live;
//                    $zlivecustomer->number_account = $request->number_account;
//                    $zlivecustomer->$selectPlanKey = $packageCode;
//                    $zlivecustomer->$dateEndKey = $packageEndDate;
//                    $zlivecustomer->log = $zlivecustomer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name approved $packageCode/$month month";
//                    $zlivecustomer->save();
//                }
//                event(new Notify(1, [$customer->user_name], "Đơn $invoiceSave->invoice_id đã được tạo thành công"));
// </editor-fold>

                $this->processInvoice($userAdmin->user_name, 1, $invoiceSave);
            }
            $invoiceSave->save();
//            $app = Config::get('config.app_url');
            $message = "[INVOICE] $userAdmin->user_name vừa tạo hóa đơn $package->package_code/$month tháng cho " . strtoupper($customer->user_name) . " (" . number_format($money, 0, ',', '.') . ").";
            RequestHelper::telegram($message);
            return array('status' => 'success', 'message' => 'Success', 'invoice' => $invoiceSave);
        } catch (Exception2 $e) {
            Log::info("Error: " . $e->getMessage());
            return array('content' => trans('label.message.error'));
        }
    }

    public function actionInvoice(Request $request) {
        $userAdmin = Auth::user();
        Log::info($userAdmin->user_name . '|actionInvoice|invoiceId=' . json_encode($request->all()));
        if (!in_array(1, explode(",", $userAdmin->role))) {
            return array('status' => 'error', 'message' => 'Bạn không có quyền');
        }
        $status = 'success';
        $content = array();

        $invoice = Invoice::where('id', $request->id)->first();
        if (!$invoice) {
            return array('status' => "error", 'message' => "Not found Invoice");
        }
        // <editor-fold defaultstate="collapsed" desc="old">
//        if ($invoice->platform == 1) {
//            $packageCodeKey = "package_code";
//            $packageStartDateKey = "package_start_date";
//            $packageEndDateKey = "package_end_date";
//            $numberKeyLiveKey = "number_key_live";
//            $selectPlanKey = "select_plan";
//            $dateEndKey = "date_end";
//        } else if ($invoice->platform == 2) {
//            $packageCodeKey = "tiktok_package";
//            $packageStartDateKey = "tiktok_start_date";
//            $packageEndDateKey = "tiktok_end_date";
//            $numberKeyLiveKey = "tiktok_key_live";
//            $selectPlanKey = "tiktok_plan";
//            $dateEndKey = "tiktok_end_date";
//        } else if ($invoice->platform == 3) {
//            $packageCodeKey = "shopee_package";
//            $packageStartDateKey = "shopee_start_date";
//            $packageEndDateKey = "shopee_end_date";
//            $numberKeyLiveKey = "shopee_key_live";
//            $selectPlanKey = "shopee_plan";
//            $dateEndKey = "shopee_end_date";
//        }
//        $package = Package::where("package_code", $invoice->package_code)->first();
//        $customer = User::where("user_name", $invoice->user_name)->first();
//        $customer->expired_scan = 0;
//        //approve
//        if ($request->type == 1) {
//            if ($invoice->status == 1) {
//                return array('status' => "error", 'message' => "Hóa đơn này đã được xác nhận rồi");
//            }
//            //1:approve,0: rollback
//            $invoice->status = $request->type;
//            $invoice->user_approve = $userAdmin->user_name;
//            $invoice->system_update_date = time();
//            $invoice->period = gmdate("Ym", time() + 7 * 3600);
//            $invoice->date = gmdate("Ymd", time() + 7 * 3600);
//            $invoice->log = $invoice->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name approved";
//
//            $customer->$packageStartDateKey = time();
//            //kiểm tra xem là loại invoice là gia hạn hay nâng cấp
//            if ($customer->$packageCodeKey != $package->package_code) {
//                //nâng cấp
//                $packageEndDate = time() + ($invoice->month * $package->duration * 86400) + ($invoice->date_remain * 86400);
//            } else {
//                //gia hạn
//                if ($customer->$packageEndDateKey > time()) {
//                    //nếu tính đến hiện tại gói cước vẫn còn
//                    $packageEndDate = ($customer->$packageEndDateKey + ($invoice->month * $package->duration * 86400));
//                } else {
//                    //nếu đã hết hạn
//                    $packageEndDate = (time() + ($invoice->month * $package->duration * 86400));
//                }
//            }
//
//            //kiểm tra nếu user có tài khoản con thì phải đếm tổng number_key_live ở các tài khoản con
//            $totalChildLive = User::where("customer_id", $customer->customer_id)->where("is_default", 2)->sum($numberKeyLiveKey);
//
//            $customer->$numberKeyLiveKey = $package->number_live - $totalChildLive;
//            $customer->status = 1;
//            $customer->$packageCodeKey = $package->package_code;
//            $customer->$packageEndDateKey = $packageEndDate;
//            $customer->number_account = $package->number_account;
//            $customer->log = $customer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name approved $package->package_code/$invoice->month month,invoice_id=$invoice->id";
//            $customer->save();
//            User::where("customer_id", $customer->customer_id)->where("is_default", 2)->update([$packageEndDateKey => $packageEndDate]);
//            $zlivecustomer = Zlivecustomer::where("customer_id", $customer->customer_id)->first();
//            if ($zlivecustomer) {
//                $zlivecustomer->$numberKeyLiveKey = $package->number_live;
//                $zlivecustomer->number_account = $package->number_account;
//                $zlivecustomer->$selectPlanKey = $package->package_code;
//                $zlivecustomer->$dateEndKey = $packageEndDate;
//                $zlivecustomer->log = $zlivecustomer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name approved $package->package_code/$invoice->month month,invoice_id=$invoice->id";
//                $zlivecustomer->save();
//            }
//            $invoice->save();
//            $message = "[INVOICE] $userAdmin->user_name vừa xác nhận hóa đơn $invoice->invoice_id  (" . number_format($invoice->payment_money, 0, ',', '.') . ").";
//            RequestHelper::telegram($message);
//            event(new Notify(1, [$customer->user_name], "Đơn $invoice->invoice_id đã được xác nhận"));
//        } else {
//
//            //trừ thời gian của gói cước
//            $invoice->log = $invoice->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name rejected";
//            $invoice->status = $request->type;
//            $invoice->user_approve = $userAdmin->user_name;
//            $invoice->system_update_date = time();
//            $invoice->save();
//            $customer->log = $customer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name rejected $package->package_code/$invoice->month month,invoice_id=$invoice->id";
//            $customer->$packageEndDateKey = $invoice->last_expire;
//            $customer->$packageCodeKey = $invoice->last_package;
//            $customer->$numberKeyLiveKey = $invoice->last_number_live;
//            $customer->save();
//            User::where("customer_id", $customer->customer_id)->where("is_default", 2)->update([$packageEndDateKey => $invoice->last_expire]);
//            $zlivecustomer = Zlivecustomer::where("customer_id", $customer->customer_id)->first();
//            if ($zlivecustomer) {
//                $zlivecustomer->$dateEndKey = $invoice->last_expire;
//                $zlivecustomer->$selectPlanKey = $invoice->last_package;
//                $zlivecustomer->$numberKeyLiveKey = $invoice->last_number_live;
//                $zlivecustomer->log = $zlivecustomer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userAdmin->user_name rejected $package->package_code/$invoice->month month,invoice_id=$invoice->id";
//                $zlivecustomer->save();
//            }
//        }
//        return array('status' => $status, 'message' => "Success");
// </editor-fold>

        $rp = $this->processInvoice($userAdmin->user_name, $request->type, $invoice);
        return $rp;
    }

    public function postRemoveExpInvoice(Request $request) {
        DB::enableQueryLog();
        $userAdmin = Auth::user();
        Log::info($userAdmin->user_name . '|postRemoveExpInvoice');
        $status = 'danger';
        $content = array();
        try {

            $result = Invoice::where('due_date', '<=', time())->where('status', 0)->where('del_status', 0)->update(['del_status' => 1]);
            array_push($content, 'Xóa thành công ' . $result . ' hóa đơn quá hạn');
        } catch (Exception $exc) {
            Log::info($exc->getTraceAsString());
            $status = "danger";
            array_push($content, trans('label.message.error'));
        }
//        Log::info(DB::getQueryLog());
        return array('status' => $status, 'content' => $content);
    }

    public function processInvoice($userProcess, $type, $invoice) {
        if ($invoice->platform == 1) {
            $packageCodeKey = "package_code";
            $packageStartDateKey = "package_start_date";
            $packageEndDateKey = "package_end_date";
            $numberKeyLiveKey = "number_key_live";
            $selectPlanKey = "select_plan";
            $dateEndKey = "date_end";
        } else if ($invoice->platform == 2) {
            $packageCodeKey = "tiktok_package";
            $packageStartDateKey = "tiktok_start_date";
            $packageEndDateKey = "tiktok_end_date";
            $numberKeyLiveKey = "tiktok_key_live";
            $selectPlanKey = "tiktok_plan";
            $dateEndKey = "tiktok_end_date";
        } else if ($invoice->platform == 3) {
            $packageCodeKey = "shopee_package";
            $packageStartDateKey = "shopee_start_date";
            $packageEndDateKey = "shopee_end_date";
            $numberKeyLiveKey = "shopee_key_live";
            $selectPlanKey = "shopee_plan";
            $dateEndKey = "shopee_end_date";
        }
        $package = Package::where("package_code", $invoice->package_code)->first();
        $customer = User::where("user_name", $invoice->user_name)->first();
        $customer->expired_scan = 0;
        //approve
        if ($type == 1) {
            if ($invoice->status == 1) {
                return array('status' => "error", 'message' => "Hóa đơn này đã được xác nhận rồi");
            }
            //1:approve,0: rollback
            $invoice->status = 1;
            $invoice->user_approve = $userProcess;
            $invoice->system_update_date = time();
            $invoice->period = gmdate("Ym", time() + 7 * 3600);
            $invoice->date = gmdate("Ymd", time() + 7 * 3600);
            $invoice->log = $invoice->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userProcess approved";

            $customer->$packageStartDateKey = time();
            //kiểm tra xem là loại invoice là gia hạn hay nâng cấp
            if ($customer->$packageCodeKey != $package->package_code) {
                //nâng cấp
                $packageEndDate = time() + ($invoice->month * $package->duration * 86400) + ($invoice->date_remain * 86400);
            } else {
                //gia hạn
                if ($customer->$packageEndDateKey > time()) {
                    //nếu tính đến hiện tại gói cước vẫn còn
                    $packageEndDate = ($customer->$packageEndDateKey + ($invoice->month * $package->duration * 86400));
                } else {
                    //nếu đã hết hạn
                    $packageEndDate = (time() + ($invoice->month * $package->duration * 86400));
                }
            }

            //kiểm tra nếu user có tài khoản con thì phải đếm tổng number_key_live ở các tài khoản con
            $totalChildLive = User::where("customer_id", $customer->customer_id)->where("is_default", 2)->sum($numberKeyLiveKey);

//            $customer->$numberKeyLiveKey = $package->number_live - $totalChildLive;
            //dung dữ liệu của invoice
            $customer->$numberKeyLiveKey = $invoice->number_live - $totalChildLive;
            $customer->status = 1;
            $customer->$packageCodeKey = $package->package_code;
            $customer->$packageEndDateKey = $packageEndDate;
            $customer->number_account = $package->number_account;
            $customer->log = $customer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userProcess approved $package->package_code/$invoice->month month,invoice_id=$invoice->id";
            $customer->save();
            User::where("customer_id", $customer->customer_id)->where("is_default", 2)->update([$packageEndDateKey => $packageEndDate, $packageCodeKey => $package->package_code]);
            $zlivecustomer = Zlivecustomer::where("customer_id", $customer->customer_id)->first();
            if ($zlivecustomer) {
                $zlivecustomer->$numberKeyLiveKey = $invoice->number_live;
                $zlivecustomer->number_account = $package->number_account;
                $zlivecustomer->$selectPlanKey = $package->package_code;
                $zlivecustomer->$dateEndKey = $packageEndDate;
                $zlivecustomer->log = $zlivecustomer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userProcess approved $package->package_code/$invoice->month month,invoice_id=$invoice->id";
                $zlivecustomer->save();
            }
            $invoice->save();
            $message = "[INVOICE] $userProcess vừa xác nhận hóa đơn $invoice->invoice_id  (" . number_format($invoice->payment_money, 0, ',', '.') . ").";
            RequestHelper::telegram($message);
            event(new Notify(1, [$customer->user_name], "Đơn $invoice->invoice_id đã được xác nhận"));

            //thêm dữ liệu sang invoice_vat để xử lý hóa đơn vat
            if ($invoice->bank == 'moonshots') {
                $invoiceVat = InvoiceVat::find($invoice->id);
                if (!$invoiceVat) {
                    $invoiceVat = new InvoiceVat();
                }
                $invoiceVat->del_status = 0;
                $invoiceVat->id = $invoice->id;
                $invoiceVat->invoice_id = $invoice->invoice_id;
                $invoiceVat->user_name = $invoice->user_name;
                $invoiceVat->package_code = $invoice->package_code;
                $invoiceVat->number_live = $invoice->number_live;
                $invoiceVat->month = $invoice->month;
                $invoiceVat->period = $invoice->period;
                $invoiceVat->date = $invoice->date;
                $invoiceVat->system_create_date = $invoice->system_create_date;
                $invoiceVat->payment_money = $invoice->payment_money;
                $invoiceVat->user_approve = $invoice->user_approve;
                $invoiceVat->save();
                $this->createMinvoiceVATInvoice($invoice, $package, $customer);
            }
        } else {

            //trừ thời gian của gói cước
            $invoice->log = $invoice->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userProcess rejected";
            $invoice->status = $type;
            $invoice->user_approve = $userProcess;
            $invoice->system_update_date = time();
            $invoice->save();
            $customer->log = $customer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userProcess rejected $package->package_code/$invoice->month month,invoice_id=$invoice->id";
            $customer->$packageEndDateKey = $invoice->last_expire;
            $customer->$packageCodeKey = $invoice->last_package;
            $customer->$numberKeyLiveKey = $invoice->last_number_live;
            $customer->save();
            User::where("customer_id", $customer->customer_id)->where("is_default", 2)->update([$packageEndDateKey => $invoice->last_expire]);
            $zlivecustomer = Zlivecustomer::where("customer_id", $customer->customer_id)->first();
            if ($zlivecustomer) {
                $zlivecustomer->$dateEndKey = $invoice->last_expire;
                $zlivecustomer->$selectPlanKey = $invoice->last_package;
                $zlivecustomer->$numberKeyLiveKey = $invoice->last_number_live;
                $zlivecustomer->log = $zlivecustomer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userProcess rejected $package->package_code/$invoice->month month,invoice_id=$invoice->id";
                $zlivecustomer->save();
            }
            //xóa invoice vat
//            InvoiceVat::where("id", $invoice->id)->update(["del_status" => 1, "log" => Utils::timeToStringGmT7(time()) . " $userProcess rollback"]);
            $invoiceVatRecord = InvoiceVat::where("id", $invoice->id)->first();
            if ($invoiceVatRecord) {
                // Nếu có vat_id thì gọi API xóa trên minvoice
                if (!empty($invoiceVatRecord->vat_id) && empty($invoiceVatRecord->vat_code)) {
//                    $this->deleteMinvoiceVATInvoice($invoiceVatRecord->vat_id);
                }
                $invoiceVatRecord->vat_id = null;
                $invoiceVatRecord->del_status = 1;
                $invoiceVatRecord->log = $invoiceVatRecord->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $userProcess rollback";
                $invoiceVatRecord->save();
            }
        }
        return array('status' => "success", 'message' => "Success");
    }

    public function createMinvoiceVATInvoice($invoice, $package, $customer) {
        try {
            // Kiểm tra điều kiện áp dụng, có vat_invoice_info thì sẽ tạo bằng tay
            if ($customer->is_company != 0 ||
                    $invoice->bank != 'moonshots' ||
                    $invoice->vat_invoice_info != null) {
                Log::info("createMinvoiceVATInvoice: Không đáp ứng điều kiện tạo hóa đơn VAT cho invoice #{$invoice->id}");
                return;
            }

            // Xác định loại gói và thông tin tương ứng
            $isCustomOrVip = in_array($invoice->package_code, ['LIVECUSTOM', 'LIVEVIP', 'TIKTOKVIP', 'TIKTOKCUSTOM', 'SHOPEEVIP']);

            // Tính toán các giá trị cho request
            $quantity = $isCustomOrVip ? 1 : $invoice->month;
            $unitCode = $isCustomOrVip ? 'Gói' : 'Tháng';
            $unitPrice = $isCustomOrVip ? $invoice->payment_money : $package->price;
            $discountAmount = $isCustomOrVip ? 0 : ($package->{"discount_" . $invoice->month} ?? 0);

            // Tính toán tổng tiền
            $totalAmountWithoutVat = $invoice->payment_money;
            $totalAmount = $invoice->payment_money;

            // Tên sản phẩm
            $itemName = ($package->package_name_vat) . ' ' . $invoice->invoice_id;

            // Chuẩn bị dữ liệu request
            $requestData = array(
                "editmode" => 1,
                "data" => array(
                    array(
                        "inv_invoiceSeries" => "1C25MCN",
                        "inv_currencyCode" => "VND",
                        "inv_exchangeRate" => 1,
                        "inv_buyerDisplayName" => $invoice->user_name,
                        "inv_buyerAddressLine" => "Hà Nội",
                        "inv_paymentMethodName" => "TM/CK",
                        "inv_discountAmount" => $discountAmount,
                        "inv_TotalAmountWithoutVat" => $totalAmountWithoutVat,
                        "inv_vatAmount" => 0,
                        "inv_TotalAmount" => $totalAmount,
                        "key_api" => $invoice->invoice_id,
                        "details" => array(
                            array(
                                "data" => array(
                                    array(
                                        "tchat" => 1,
                                        "stt_rec0" => 1,
                                        "inv_itemCode" => $invoice->package_code,
                                        "inv_itemName" => $itemName,
                                        "inv_unitCode" => $unitCode,
                                        "inv_quantity" => $quantity,
                                        "inv_unitPrice" => $unitPrice,
                                        "inv_discountAmount" => $discountAmount,
                                        "inv_TotalAmountWithoutVat" => $totalAmountWithoutVat,
                                        "ma_thue" => -1,
                                        "inv_vatAmount" => 0,
                                        "inv_TotalAmount" => $totalAmount
                                    )
                                )
                            )
                        )
                    )
                )
            );

            // Khởi tạo cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://0110117378.minvoice.app/api/InvoiceApi78/Save',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer 72a637e6-abc6-4557-9c16-ff324ea2c814'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            // Kiểm tra lỗi cURL
            if ($curlError) {
                Log::error("createMinvoiceVATInvoice: cURL Error - " . $curlError);
                return;
            }

            // Kiểm tra HTTP status
            if ($httpCode != 200) {
                Log::error("createMinvoiceVATInvoice: HTTP Error - Code: " . $httpCode);
                return;
            }

            // Parse response
            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("createMinvoiceVATInvoice: JSON Parse Error - " . json_last_error_msg());
                return;
            }

            // Kiểm tra response thành công
            if (!isset($responseData['ok']) || $responseData['ok'] !== true) {
                $errorMsg = $responseData['message'] ?? 'Không xác định';
                Log::error("createMinvoiceVATInvoice: API Error - " . $errorMsg);
                return;
            }

            // Lấy ID từ response
            $vatId = $responseData['data']['id'] ?? null;
            if (!$vatId) {
                Log::error("createMinvoiceVATInvoice: Không tìm thấy ID trong response");
                return;
            }

            // Cập nhật vat_id vào bảng invoice_vat
            $invoiceVat = InvoiceVat::where('id', $invoice->id)->first();
            if ($invoiceVat) {
                $invoiceVat->vat_id = $vatId;
                $invoiceVat->minvoice_response = json_encode($responseData);
                $invoiceVat->save();

                Log::info("createMinvoiceVATInvoice: Đã cập nhật vat_id = {$vatId} cho invoice_vat #{$invoice->id}");
            } else {
                Log::warning("createMinvoiceVATInvoice: Không tìm thấy record invoice_vat với id = {$invoice->id}");
            }
        } catch (Exception $e) {
            Log::error("createMinvoiceVATInvoice: Exception - " . $e->getMessage());
        }
    }

    public function deleteMinvoiceVATInvoice($vatId) {
        try {
            // Kiểm tra vatId có tồn tại không
            if (empty($vatId)) {
                Log::info("deleteMinvoiceVATInvoice: Không có vatId để xóa");
                return;
            }

            // Chuẩn bị dữ liệu request cho việc xóa
            $requestData = array(
                "editmode" => 3, // 3 = delete mode
                "data" => array(
                    array(
                        "inv_invoiceSeries" => "1C25MCN",
                        "inv_invoiceAuth_Id" => $vatId
                    )
                )
            );

            // Khởi tạo cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://0110117378.minvoice.app/api/InvoiceApi78/Save',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer 72a637e6-abc6-4557-9c16-ff324ea2c814'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            // Kiểm tra lỗi cURL
            if ($curlError) {
                Log::error("deleteMinvoiceVATInvoice: cURL Error - " . $curlError);
                return;
            }

            // Kiểm tra HTTP status
            if ($httpCode != 200) {
                Log::error("deleteMinvoiceVATInvoice: HTTP Error - Code: " . $httpCode);
                return;
            }

            // Parse response
            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("deleteMinvoiceVATInvoice: JSON Parse Error - " . json_last_error_msg());
                return;
            }

            // Kiểm tra response thành công
            if (!isset($responseData['ok']) || $responseData['ok'] !== true) {
                $errorMsg = $responseData['message'] ?? 'Không xác định';
                Log::error("deleteMinvoiceVATInvoice: API Error - " . $errorMsg);
                return;
            }

            Log::info("deleteMinvoiceVATInvoice: Xóa hóa đơn VAT thành công với ID = {$vatId}");
        } catch (Exception $e) {
            Log::error("deleteMinvoiceVATInvoice: Exception - " . $e->getMessage());
        }
    }

    public function downloadVATInvoice($invoiceId) {
        $user = Auth::user();
        Log::info("$user->user_name|InvoiceController|downloadVATInvoice=$invoiceId");

        try {
            // Tìm hóa đơn
            $invoice = Invoice::where('id', $invoiceId)->first();
            if (!$invoice) {
                return response()->json(['error' => 'Không tìm thấy hóa đơn'], 404);
            }

            // Kiểm tra quyền (chỉ admin hoặc chủ hóa đơn)
            if (!in_array(1, explode(",", $user->role)) && $invoice->user_name != $user->user_name) {
                return response()->json(['error' => 'Không có quyền truy cập'], 403);
            }

            // Lấy thông tin VAT
            $invoiceVat = \App\Http\Models\InvoiceVat::where('id', $invoice->id)->first();
            if (!$invoiceVat || !$invoiceVat->vat_id) {
                return response()->json(['error' => 'Hóa đơn VAT không tồn tại'], 404);
            }

            // Tạo tên file
            $fileName = "hoadon_vat_{$invoice->invoice_id}.pdf";

            // Download PDF
            return $this->downloadMinvoiceVATInvoice($invoiceVat->vat_id, $fileName);
        } catch (Exception $e) {
            Log::error("downloadVATInvoice: Error - " . $e->getMessage());
            return response()->json(['error' => 'Lỗi hệ thống'], 500);
        }
    }

    public function previewVATInvoice($invoiceId) {
        $user = Auth::user();
        Log::info("$user->user_name|InvoiceController|previewVATInvoice=$invoiceId");

        try {
            // Tìm hóa đơn
            $invoice = Invoice::where('id', $invoiceId)->first();
            if (!$invoice) {
                return response()->json(['error' => 'Không tìm thấy hóa đơn'], 404);
            }

            // Kiểm tra quyền
            if (!in_array(1, explode(",", $user->role)) && $invoice->user_name != $user->user_name) {
                return response()->json(['error' => 'Không có quyền truy cập'], 403);
            }

            // Lấy thông tin VAT
            $invoiceVat = \App\Http\Models\InvoiceVat::where('id', $invoice->id)->first();
            if (!$invoiceVat || !$invoiceVat->vat_id) {
                return response()->json(['error' => 'Hóa đơn VAT không tồn tại'], 404);
            }

            // Preview PDF
            return $this->previewMinvoiceVATInvoice($invoiceVat->vat_id);
        } catch (Exception $e) {
            Log::error("previewVATInvoice: Error - " . $e->getMessage());
            return response()->json(['error' => 'Lỗi hệ thống'], 500);
        }
    }

    private function viewMinvoiceVATInvoice($vatId) {
        try {
            // Kiểm tra vatId có tồn tại không
            if (empty($vatId)) {
                Log::error("viewMinvoiceVATInvoice: Không có vatId để xem");
                return null;
            }

            Log::info("viewMinvoiceVATInvoice: Bắt đầu lấy PDF hóa đơn VAT với ID = {$vatId}");

            // Khởi tạo cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://0110117378.minvoice.app/api/InvoiceApi78/PrintInvoice?id={$vatId}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer 72a637e6-abc6-4557-9c16-ff324ea2c814',
                    'TaxCode: 0110117378'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
            $curlError = curl_error($curl);
            curl_close($curl);

            Log::info("viewMinvoiceVATInvoice: HTTP Code = " . $httpCode);
            Log::info("viewMinvoiceVATInvoice: Content Type = " . $contentType);

            // Kiểm tra lỗi cURL
            if ($curlError) {
                Log::error("viewMinvoiceVATInvoice: cURL Error - " . $curlError);
                return null;
            }

            // Kiểm tra HTTP status
            if ($httpCode != 200) {
                Log::error("viewMinvoiceVATInvoice: HTTP Error - Code: " . $httpCode);
                Log::error("viewMinvoiceVATInvoice: Response: " . substr($response, 0, 500));
                return null;
            }

            // Kiểm tra content type
            if (strpos($contentType, 'application/pdf') === false) {
                // Nếu không phải PDF, có thể là JSON error response
                $responseData = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::error("viewMinvoiceVATInvoice: API Error Response - " . json_encode($responseData));
                } else {
                    Log::error("viewMinvoiceVATInvoice: Unexpected content type - " . $contentType);
                    Log::error("viewMinvoiceVATInvoice: Response: " . substr($response, 0, 500));
                }
                return null;
            }

            Log::info("viewMinvoiceVATInvoice: Lấy PDF thành công với ID = {$vatId}");

            return array(
                'pdf_data' => $response,
                'content_type' => $contentType,
                'file_size' => strlen($response)
            );
        } catch (Exception $e) {
            Log::error("viewMinvoiceVATInvoice: Exception - " . $e->getMessage());
            return null;
        }
    }

    private function downloadMinvoiceVATInvoice($vatId, $fileName = null) {
        $pdfData = $this->viewMinvoiceVATInvoice($vatId);

        if (!$pdfData) {
            return response()->json(['error' => 'Không thể lấy hóa đơn VAT'], 404);
        }

        // Tạo tên file nếu không được cung cấp
        if (!$fileName) {
            $fileName = "invoice_vat_{$vatId}.pdf";
        }

        return response($pdfData['pdf_data'], 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                        ->header('Content-Length', $pdfData['file_size']);
    }

    private function previewMinvoiceVATInvoice($vatId) {
        $pdfData = $this->viewMinvoiceVATInvoice($vatId);

        if (!$pdfData) {
            return response()->json(['error' => 'Không thể lấy hóa đơn VAT'], 404);
        }

        return response($pdfData['pdf_data'], 200)
                        ->header('Content-Type', 'application/pdf')
                        ->header('Content-Disposition', 'inline')
                        ->header('Content-Length', $pdfData['file_size']);
    }

    public function getMinvoiceInvoices($start = 0, $count = 100) {
        try {

            // Tính toán ngày
            $currentDate = date('Y-m-d'); // Ngày hiện tại
            $fromDate = date('Y-m-d', strtotime('-3 days')); // 3 ngày trước
            // Chuẩn bị dữ liệu request
            $requestData = array(
                "tuNgay" => $fromDate,
                "denngay" => $currentDate,
                "khieu" => "1C25MCN",
                "start" => $start,
                "count" => $count,
                "coChiTiet" => true
            );

            // Khởi tạo cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://0110117378.minvoice.app/api/InvoiceApi78/GetInvoices',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($requestData),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer 72a637e6-abc6-4557-9c16-ff324ea2c814'
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            Log::info("getMinvoiceInvoices: Request data = " . json_encode($requestData));
            Log::info("getMinvoiceInvoices: HTTP Code = " . $httpCode);

            // Kiểm tra lỗi cURL
            if ($curlError) {
                Log::error("getMinvoiceInvoices: cURL Error - " . $curlError);
                return null;
            }

            // Kiểm tra HTTP status
            if ($httpCode != 200) {
                Log::error("getMinvoiceInvoices: HTTP Error - Code: " . $httpCode);
                Log::error("getMinvoiceInvoices: Response: " . substr($response, 0, 500));
                return null;
            }

            // Parse response
            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("getMinvoiceInvoices: JSON Parse Error - " . json_last_error_msg());
                return null;
            }

            // Kiểm tra response thành công
            if (!isset($responseData['ok']) || $responseData['ok'] !== true) {
                $errorMsg = $responseData['message'] ?? 'Không xác định';
                Log::error("getMinvoiceInvoices: API Error - " . $errorMsg);
                return null;
            }

            $total = $responseData['total'] ?? 0;
            $invoices = $responseData['data'] ?? [];

            Log::info("getMinvoiceInvoices: Lấy thành công {$total} hóa đơn từ ngày {$fromDate} đến {$currentDate}");

            return array(
                'total' => $total,
                'invoices' => $invoices,
                'from_date' => $fromDate,
                'to_date' => $currentDate,
                'start' => $start,
                'count' => count($invoices)
            );
        } catch (Exception $e) {
            Log::error("getMinvoiceInvoices: Exception - " . $e->getMessage());
            return null;
        }
    }

    public function syncMinvoiceData() {
        try {
            Log::info("syncMinvoiceData: Bắt đầu đồng bộ mã bảo mật từ minvoice");

            $invoicesData = $this->getMinvoiceInvoices(0, 100);
            if (!$invoicesData) {
                Log::error("syncMinvoiceData: Không thể lấy danh sách hóa đơn từ minvoice");
                return array('status' => 'error', 'message' => 'Không thể lấy dữ liệu từ minvoice');
            }

            $updated = 0;
            $notFound = 0;
            $skipped = 0;

            foreach ($invoicesData['invoices'] as $minvoiceInvoice) {
                // Kiểm tra sobaomat có tồn tại và không null
                if (empty($minvoiceInvoice['sobaomat'])) {
                    $skipped++;
                    Log::info("syncMinvoiceData: Bỏ qua hóa đơn {$minvoiceInvoice['id']} - không có mã bảo mật");
                    continue;
                }

                // Tìm invoice_vat trong database theo vat_id
                $invoiceVat = \App\Http\Models\InvoiceVat::where('vat_id', $minvoiceInvoice['id'])->whereNull("vat_code")->first();

                if ($invoiceVat) {
                    // Cập nhật vat_code = sobaomat
                    $invoiceVat->vat_code = $minvoiceInvoice['sobaomat'];
                    $invoiceVat->save();

                    $updated++;
                    Log::info("syncMinvoiceData: Cập nhật vat_code = {$minvoiceInvoice['sobaomat']} cho invoice_vat #{$invoiceVat->id}");
                } else {
                    $notFound++;
                    Log::warning("syncMinvoiceData: Không tìm thấy invoice_vat với vat_id = {$minvoiceInvoice['id']}");
                }
            }

            Log::info("syncMinvoiceData: Hoàn thành đồng bộ. Updated: {$updated}, Not found: {$notFound}, Skipped: {$skipped}");

            return array(
                'status' => 'success',
                'message' => "Đồng bộ thành công. Cập nhật: {$updated}, Không tìm thấy: {$notFound}, Bỏ qua: {$skipped}",
                'updated' => $updated,
                'not_found' => $notFound,
                'skipped' => $skipped,
                'total_minvoice' => $invoicesData['total']
            );
        } catch (Exception $e) {
            Log::error("syncMinvoiceData: Exception - " . $e->getMessage());
            return array('status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

}
