<?php

namespace App\Http\Controllers;

use App\Common\Network\RequestHelper;
use App\Common\Utils;
use App\Events\Notify;
use App\Http\Models\Bonus;
use App\Http\Models\BonusHistory;
use App\Http\Models\Notify as Notify2;
use App\Http\Models\Zliveaccount;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zlivecustomer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Log;
use function bcrypt;
use function event;
use function GuzzleHttp\json_encode;
use function Monolog\Handler\error_log;
use function redirect;
use function trans;
use function view;

class UserController extends Controller {

    public function index(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|LiveController.index|request=' . json_encode($request->all()));
        $datas = User::whereRaw("1=1");
        $queries = [];

        $limit = 30;
        if (isset($request->limit)) {
            if ($request->limit <= 2000 && $request->limit > 0) {
                $limit = $request->limit;
                $queries['limit'] = $request->limit;
            }
        }

        if (isset($request->username)) {
            $datas = $datas->where("user_name", "like", "%$request->username%");
            $queries['username'] = $request->username;
        }
        if (isset($request->s) && $request->s != -1) {
            $datas = $datas->where("status", $request->s);
            $queries['s'] = $request->s;
        }

        if (isset($request->ip)) {
            $datas = $datas->where("ip", $request->ip);
            $queries['ip'] = $request->ip;
        }
        if (isset($request->customer_id)) {
            $datas = $datas->where("customer_id", $request->customer_id);
            $queries['customer_id'] = $request->customer_id;
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
        $ips = DB::select("select ip,count(*) as total from users where ip is not null group by ip");
        $datas = $datas->sortable()->paginate($limit)->appends($queries);
        $listUs = [];
        $listCus = [];
        foreach ($datas as $data) {
            $data->group_user = "<a href='/customer?customer_id=$data->customer_id' data-toggle='tooltip' data-placement='top' title='View all user by customer_id'>$data->user_name " . ($data->is_default == 1 ? "<i class='color-red ion-android-star'></i>" : "") . "</a>";

            $data->end_color = "badge badge-success";
            $data->tiktok_end_color = "badge badge-success";
            $data->shopee_end_color = "badge badge-success";
            $listUs[] = $data->user_code;
            $listCus[] = $data->customer_id;
            foreach ($ips as $ip) {
                if ($data->ip == $ip->ip) {
                    $data->ip_count = "<a href='/customer?ip=$ip->ip'>$ip->total</a>";
                    break;
                }
            }
            if ($data->package_end_date < time()) {
                $data->end_color = "badge badge-danger";
            } else if ($data->package_end_date - time() <= 3 * 86400) {
                $data->end_color = "badge badge-warning";
            }
            if ($data->tiktok_end_date < time()) {
                $data->tiktok_end_color = "badge badge-danger";
            } else if ($data->tiktok_end_date - time() <= 3 * 86400) {
                $data->tiktok_end_color = "badge badge-warning";
            }
            if ($data->shopee_end_date < time()) {
                $data->shopee_end_color = "badge badge-danger";
            } else if ($data->tiktok_end_date - time() <= 3 * 86400) {
                $data->shopee_end_color = "badge badge-warning";
            }
            $data->old_pkg = 'LIVE1';
            $data->old_money = '200000';
            $data->old_key_live = 1;
            $data->old_month = 1;
            $invoice = \App\Http\Models\Invoice::where("user_name", $data->user_name)->where("status", 1)->orderBy('id', 'desc')->first();
            if ($invoice) {
                $data->old_pkg = $invoice->package_code;
                $data->old_money = $invoice->payment_money;
                $data->old_key_live = $invoice->number_live;
                $data->old_month  = $invoice->month;
            }
        }
        $livings = DB::select("select user_id,platform,count(*) as total from zliveautolive where status in(2,1,4) group by user_id,platform");
        $livingByCus = DB::select("select cus_id,platform,count(*) as total from zliveautolive where status in(2,1,4) group by cus_id,platform");
        $totalLiveByCus = Zlivecustomer::whereIn("customer_id", $listCus)->get();
        $notifys = Notify2::where("del_status", 0)->get();
        foreach ($notifys as $notify) {
            if ($notify->start_time == 0) {
                $notify->start_time_text = "Hiện tại";
            } else {
                $notify->start_time_text = gmdate("Y/m/d H:i:s", $notify->start_time + 7 * 3600);
            }
            if ($notify->end_time == 0) {
                $notify->end_time_text = "Vĩnh viễn";
            } else {
                $notify->end_time_text = gmdate("Y/m/d H:i:s", $notify->end_time + 7 * 3600);
            }
            $notify->type = "Thông báo";
            if ($notify->is_maintenance) {
                $notify->type = "Bảo trì";
            }
        }
        return view('components.customer', [
            "datas" => $datas,
            'request' => $request,
            'status' => $this->genStatusUser($request),
            'packages' => $this->loadPackage($request),
            'limitSelectbox' => $this->genLimit($request),
            'role' => $this->genRole($request),
            'limit' => $limit,
            'ips' => $ips,
            'livings' => $livings,
            'livingByCus' => $livingByCus,
            'totalLiveByCus' => $totalLiveByCus,
            'notify' => $notifys,
        ]);
    }

    public function viewLogin(Request $request) {
//        Log::info('viewLogin|request=' . json_encode($request->all()));
//        $maintain = Config::get('config.maintain');
//        if ($maintain) {
//            return view('layouts.maintenance');
//        }
        //quick login
        if (isset($request->u) && isset($request->p)) {
            if (Auth::check()) {
                Auth::logout();
            }
            if (Auth::attempt(['user_name' => $request->u, 'password' => $request->p])) {
                if (in_array("1", explode(",", Auth::user()->role))) {
                    return redirect('dashboard');
                } else {
                    $u = Auth::user();
                    if ($u->ip == null) {
                        $u->ip = Utils::getUserIpAddr();
                        $u->save();
                    }
                    $redirect = "live";
                    if ($u->shopee_package != "SHOPEE_TEST" && $u->shopee_end_date > time()) {
                        $redirect = "shopee";
                    }
                    if ($u->tiktok_package != "TIKTOK_TEST" && $u->tiktok_end_date > time()) {
                        $redirect = "tiktok";
                    }
                    return redirect($redirect);
                }
            }
        } else {
            if (Auth::check()) {
                $u = Auth::user();
                if (in_array("1", explode(",", $u->role))) {
                    return redirect('dashboard');
                } else {
                    $redirect = "live";
                    if ($u->shopee_package != "SHOPEE_TEST" && $u->shopee_end_date > time()) {
                        $redirect = "shopee";
                    }
                    if ($u->tiktok_package != "TIKTOK_TEST" && $u->tiktok_end_date > time()) {
                        $redirect = "tiktok";
                    }
                    return redirect($redirect);
                }
            } else {

                return view("layouts.login", ["request" => $request]);
            }
        }


//        if (Auth::check()) {
//            if (in_array("1", explode(",", Auth::user()->role))) {
//                return redirect('dashboard');
//            } else {
//                return redirect('live');
//            }
//        } else {
//            return view('layouts.login');
//        }
    }

    public function login(Request $request) {
//        Log::info('onLogin|request=' . json_encode($req->all()));
        $username = $request->user_name;
        $password = $request->password;
        $validator = Validator::make($request->all(), [
                    'user_name' => 'required',
                    'password' => 'required|min:3|max:32',
                    'g-recaptcha-response' => 'required|captcha'
                        ], [
                    'user_name.required' => 'Bạn phải nhập tài khoản',
                    'password.required' => 'TBạn phải nhập mật khẩu',
                    'password.min' => trans('label.validate.password.min', ['values' => '3']),
                    'password.max' => trans('label.validate.password.max', ['values' => '32']),
                    'g-recaptcha-response.required' => "Bạn phải nhập captcha",
                    'g-recaptcha-response.captcha' => "Bạn phải nhập captcha"
        ]);
        if ($validator->fails()) {
            return redirect('login')
                            ->withErrors($validator)
                            ->withInput();
        }
        if (Auth::attempt(['user_name' => $username, 'password' => $password])) {
//            if (Auth::user()->status == 1) {
            if (in_array("1", explode(",", Auth::user()->role))) {
                return redirect('dashboard');
            } else {
                $u = Auth::user();
                if ($u->ip == null) {
                    $u->ip = Utils::getUserIpAddr();
                    $u->save();
                }
                $redirect = "live";
                if ($u->shopee_package != "SHOPEE_TEST" && $u->shopee_end_date > time()) {
                    $redirect = "shopee";
                }
                if ($u->tiktok_package != "TIKTOK_TEST" && $u->tiktok_end_date > time()) {
                    $redirect = "tiktok";
                }
                return redirect($redirect);
            }
//                return redirect()->back(); 
//            } else {
//                Auth::logout();
//                return redirect('login')->with("message", "Tài khoản <b>$username</b> của bạn chưa được kích hoạt, hãy liên hệ admin để được kích hoạt.<br><a class='color-white' target='_blank' href='https://www.facebook.com/messages/t/100002470941874'><u>Liên hệ ngay</u></a>");
//            }
        }
        return redirect('login')->with("message", 'Sai tài khoản hoặc mật khẩu');
    }

    public function logout() {
        Auth::logout();
        return redirect('login');
    }

    public function viewRegister(Request $request) {

        return view('layouts.register', ["ref" => $request->ref]);
    }

    public function onCreateNewUser(Request $request) {
        Log::info('onCreateNewUser|request=' . json_encode($request->all()));
        $u_name = $request->user_name;
        $validator = Validator::make($request->all(), [
                    'user_name' => 'required|string|unique:users,user_name,' . $u_name . '|regex:/^([a-zA-Z0-9]+[\ \-]?)+[a-zA-Z0-9]+$/im',
                    'user_name' => 'required|string|unique:zliveaccount,user_name,' . $u_name . '|regex:/^([a-zA-Z0-9]+[\ \-]?)+[a-zA-Z0-9]+$/im',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'required|regex:/^[0-9]{10,15}$/|unique:users,phone',
                    'password' => 'required|min:3|max:32',
                    'g-recaptcha-response' => 'required|captcha',
                    'password_confirmation' => 'required|same:password'], [
                    'user_name.required' => trans('label.validate.user_name.required'),
                    'user_name.unique' => trans('label.validate.user_name.unique'),
                    'user_name.regex' => trans('label.validate.user_name.regex'),
                    'email.required' => 'Bạn phải nhập email',
                    'email.email' => 'Email không đúng định dạng',
                    'email.unique' => 'Email đã được sử dụng',
                    'phone.required' => 'Bạn phải nhập số điện thoại',
                    'phone.regex' => 'Số điện thoại phải từ 10-15 chữ số',
                    'phone.unique' => 'Số điện thoại đã được sử dụng',
                    'password.required' => trans('label.validate.password.required'),
                    'password.min' => trans('label.validate.password.min', ['values' => '3']),
                    'password.max' => trans('label.validate.password.max', ['values' => '32']),
                    'password_confirmation.same' => trans('label.validate.math_pass'),
                    'g-recaptcha-response.required' => trans('label.validate.g-recaptcha-response.required'),
                    'g-recaptcha-response.captcha' => trans('label.validate.g-recaptcha-response.captcha')
        ]);


        //nếu validate fail thì trả về kết quả
        if ($validator->fails()) {
//            return redirect('register')
//                            ->withErrors($validator)
//                            ->withInput();
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $check2 = Zliveaccount::where("user_name", $u_name)->first();
        if ($check2) {
            return redirect('register')->withInput()->with('message', "Tài khoản $u_name đã tồn tại");
        }
        if (strpos(trim($u_name), " ") !== false) {
            return redirect('register')->withInput()->with('message', 'Tài khoản không được có khoảng trống');
        }
        if ($request->facebook != "" && !Utils::containString($request->facebook, "facebook.com")) {
            return redirect('register')->withInput()->with('message', 'Facebook không đúng định dạng');
        }
        $customerId = Utils::generateRandomString();
        $userName = strtolower(trim($request->user_name));
        $userCode = $userName . '_' . time();
        //lưu thông tin user
        $user = new User();
        $refInfo = "";
        if (isset($request->ref)) {
            $user->ref = $request->ref;
            $refInfo = " với ref $request->ref";
        }
        $user->customer_id = $customerId;
        $user->facebook = $request->facebook;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = bcrypt(trim($request->password));
        $user->password_plaintext = trim($request->password);
        $user->user_name = $userName;
        $user->user_code = $userCode;
        $user->is_default = 1;
        $user->role = 0;
        $user->package_code = 'LIVETEST';
        $user->tiktok_package = 'TIKTOKTEST';
        $user->shopee_package = 'SHOPEETEST';
        $user->package_start_date = time();
        $user->tiktok_start_date = time();
        $user->shopee_start_date = time();
        $user->package_end_date = time() + 2 * 86400;
        $user->tiktok_end_date = time() + 2 * 86400;
        $user->shopee_end_date = time() + 2 * 86400;
        $user->created = time();
        $user->log = Utils::timeToStringGmT7(time()) . " created by cusomter";
        $ip = Utils::getUserIpAddr();
        $user->ip = $ip;
        $user->status = 0;
        $user->tiktok_status = 0;
        $check = $user::where("ip", $ip)->first();
        if ($check) {
            $user->status = 0;
            $user->tiktok_status = 0;
        } else {
            $user->status = 1;
            $user->tiktok_status = 1;
        }
        $user->save();

        $account = new Zliveaccount();
        $account->user_code = $userCode;
        $account->user_name = $userName;
        $account->pass_word = trim($request->password);
        $account->create_time = time();
        $account->status = 0;
        $account->user_id = $customerId;
        $account->active_admin = 0;
        $account->active_vip1 = 0;
        $account->active_vip2 = 0;
        $account->save();

        $customer = new Zlivecustomer();
        $customer->customer_id = $customerId;
        $customer->customer_face = $request->facebook;
        $customer->customer_phone = $request->phone;
        $customer->select_plan = 'LIVETEST';
        $customer->tiktok_plan = 'TIKTOKTEST';
        $customer->shopee_plan = 'SHOPEETEST';
        $customer->date_create = time();
        $customer->date_end = time() + 2 * 86400;
        $customer->number_key_live = 1;
        $customer->tiktok_key_live = 1;
        $customer->shopee_key_live = 1;
        $customer->number_account = 1;
        $customer->is_active = 0;
        $customer->is_vip = 0;
        $customer->save();
        Auth::guard()->login($user);

        $message = "[USER] Tài khoản $userName vừa đăng ký $refInfo";
//        $url = Config::get('config.telegram').$message;
        RequestHelper::telegram($message);
        return redirect('login');
    }

    public function vipCreateNewUser(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|vipCreateNewUser|request=" . json_encode($request->all()));
        //kiểm tra xem đã tạo mấy user rồi
        $countUsers = User::where("customer_id", $request->customer_id)->count();
        if ($countUsers >= $user->number_account) {
            return array("status" => "error", "message" => "Bạn chỉ được tạo tối đa $user->number_account tài khoản");
        }

        $userName = strtolower(trim($request->user_name));
        $userCode = $userName . '_' . time();
        if ($request->user_name == null || $request->user_name == "") {
            return array('status' => "error", 'message' => "Bạn phải nhập Tài khoản");
        }
        if (strlen($request->user_name) <= 3 || strlen($request->user_name) >= 32) {
            return array('status' => "error", 'message' => "Bạn phải nhập tài khoản lớn hơn 3 ký tự và nhỏ hơn 32 ký tự");
        }
        if (strpos(trim($request->user_name), " ") !== false) {
            return array('status' => "error", 'message' => "Tài khoản không đúng định dạng");
        }
        $pattern = '/[^0-9a-z-_]/';
        if (preg_match($pattern, trim($request->user_name))) {
            return array("status" => "error", "message" => "Tài khoản không đúng định dạng");
        }
        if ($request->password == null || $request->password == "" || strlen($request->password) <= 3 || strlen($request->password) >= 32) {
            return array('status' => "error", 'message' => "Bạn phải nhập mật khẩu lớn hơn 3 ký tự và nhỏ hơn 32 ký tự");
        }
        $check = User::where("user_name", $userName)->first();

        if ($check) {
            return array('status' => "error", 'message' => "Tài khoản $userName đã tồn tại");
        }



        //lưu thông tin user
        $userInsert = new User();
        $userInsert->customer_id = $user->customer_id;
        $userInsert->facebook = $user->facebook;
        $userInsert->phone = $user->phone;
        $userInsert->password = bcrypt(trim($request->password));
        $userInsert->password_plaintext = trim($request->password);
        $userInsert->user_name = $userName;
        $userInsert->user_code = $userCode;
        $userInsert->is_default = 2;
        $userInsert->role = 0;
        $userInsert->status = 1;
        $userInsert->created = time();
        $userInsert->package_code = $user->package_code;
        $userInsert->package_start_date = $user->package_start_date;
        $userInsert->package_end_date = $user->package_end_date;
        $userInsert->number_key_live = 0;
        $userInsert->tiktok_package = $user->tiktok_package;
        $userInsert->tiktok_start_date = $user->tiktok_start_date;
        $userInsert->tiktok_end_date = $user->tiktok_end_date;
        $userInsert->tiktok_key_live = 0;
        $userInsert->shopee_package = $user->shopee_package;
        $userInsert->shopee_start_date = $user->shopee_start_date;
        $userInsert->shopee_end_date = $user->shopee_end_date;
        $userInsert->shopee_key_live = 0;
        $userInsert->log = Utils::timeToStringGmT7(time()) . " created by vip customer";
        $userInsert->save();

        $account = new Zliveaccount();
        $account->user_code = $userCode;
        $account->user_name = $userName;
        $account->pass_word = trim($request->password);
        $account->create_time = time();
        $account->status = 1;
        $account->user_id = $user->customer_id;
        $account->active_admin = 0;
        $account->active_vip1 = 0;
        $account->active_vip2 = 0;
        $account->save();


        $message = "[USER] Tài khoản $user->user_name vừa tạo tài khoản con $userName";
        RequestHelper::telegram($message);
        return array('status' => "success", 'message' => "Tạo tài khoản thành công");
    }

    //chức năng bảo lưu tài khoản
    public function freezing(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|freezing=" . json_encode($request->all()));
        $u = User::find($request->user_id);
        if ($u) {
            if ($u->freezing == null) {
                $freezing = (object) [
                            "number_key_live" => null,
                            "tiktok_key_live" => null,
                            "shopee_key_live" => null,
                            "package_end_date" => null,
                            "tiktok_end_date" => null,
                            "shopee_end_date" => null,
                ];
            } else {
                $freezing = json_decode($u->freezing);
            }
            if ($request->platform == 1) {
                $packageCodeKey = "package_code";
                $numberKeyLiveKey = "number_key_live";
                $dateEndKey = "package_end_date";
                $platform = "youtube";
            } else if ($request->platform == 2) {
                $packageCodeKey = "tiktok_package";
                $numberKeyLiveKey = "tiktok_key_live";
                $dateEndKey = "tiktok_end_date";
                $platform = "tiktok";
            } else if ($request->platform == 3) {
                $packageCodeKey = "shopee_package";
                $numberKeyLiveKey = "shopee_key_live";
                $dateEndKey = "shopee_end_date";
                $platform = "shopee";
            }


            if ($request->value == 1) {
                //đóng băng
                $freezing->$numberKeyLiveKey = $u->$numberKeyLiveKey;
                $freezing->$dateEndKey = $u->$dateEndKey - time();
                $u->freezing = json_encode($freezing);
                $u->log = $u->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name freezing $u->user_name $packageCodeKey=" .
                        $u->$packageCodeKey . ",$numberKeyLiveKey=" . $u->$numberKeyLiveKey . ",$dateEndKey=" . $u->$dateEndKey;
                $u->$dateEndKey = time();
                $u->save();
            } elseif ($request->value == 0) {
                //gỡ băng
                $u->$dateEndKey = time() + $freezing->$dateEndKey;
                $freezing->$numberKeyLiveKey = null;
                $freezing->$dateEndKey = null;
                $u->freezing = json_encode($freezing);
                $u->log = $u->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name unfreezing $platform $u->user_name";
                $u->save();
            }
            return array('status' => "success", 'message' => "Success");
        }
    }

    public function addOrEditUser(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|addOrEditUser=" . json_encode($request->all()));


        if ($request->password == "") {
            return array('status' => "error", 'message' => "Password is invalid");
        }

//        if (!is_int($request->number_live)) {
//            return array('status' => "error", 'message' => "Number Live is invalid");
//        }
//        if (!is_int($request->number_account)) {
//            return array('status' => "error", 'message' => "Number Account Live is invalid");
//        }
        $customerId = Utils::generateRandomString();
        $phone = $request->phone;
        $facebook = trim($request->facebook);
        $password = trim($request->password);
        $packageCode = "LIVETEST";
        $tiktokPackage = 'TIKTOKTEST';
        $shopeePackage = 'SHOPEETEST';
        $numberLive = 1;
        $numberAccount = 1;
        $description = $request->des;
        $isDefault = 1;
        $expire = time() + 3 * 86400;
        if (isset($request->expire)) {
            $expire = strtotime($request->expire);
        }
        $role = 0;
        if (count($request->role) > 0) {
            $role = implode(",", $request->role);
        }
        $log = Utils::timeToStringGmT7(time()) . " $user->user_name created";
        if (in_array(1, explode(",", $user->role))) {
            //edit user
            if ($request->user_id != null) {
                $u = User::find($request->user_id);
                if (!$u) {
                    return array('status' => "error", 'message' => "Not found info $request->user_id");
                }
                $u->facebook = $facebook;
                $u->phone = $phone;
                $u->password = bcrypt(trim($password));
                $u->password_plaintext = trim($password);
                $u->role = $role;
                $u->description = $description;
                $packageEndDate = strtotime("$request->date_end GMT+07:00");
//                Log::info("$request->date_end ".strtotime("$request->date_end GMT+07:00"));
                $u->package_end_date = $packageEndDate;
                $u->save();

                Zliveaccount::where("user_name", $u->user_name)->update(["pass_word" => trim($password)]);
                Zlivecustomer::where("customer_id", $u->customer_id)->update(["date_end" => $packageEndDate]);
                return array('status' => "success", 'message' => "Success");
            }
        } else if (in_array(2, explode(",", $user->role))) {
            $expire = $user->package_end_date;
            $isDefault = 2;
            $numberAccount = 1;
            $packageCode = $user->package_code;
            $customerId = $user->customer_id;
            $numberLive = $request->number_live;
            //kiểm tra xem có bao nhiêu luồng live đang được customer_id này sử dụng
            $living = $this->countLiving($user->customer_id);
            if ($numberLive > $user->number_key_live - $living) {
                return array('status' => "error", 'message' => "Number Live vượt quá số lượng cho phép " . ($user->number_key_live - $living));
            }
            $user->number_key_live = $user->number_key_live - $numberLive;
            $user->save();
        } else {
            return array('status' => "error", 'message' => "You do not have permission");
        }
        if ($request->user_name == "") {
            return array('status' => "error", 'message' => "Username is invalid");
        }
        $userName = trim($request->user_name);
        $check = User::where("user_name", $userName)->first();
        if ($check) {
            return array('status' => "error", 'message' => "User $userName is already exists");
        }
        $check2 = Zliveaccount::where("user_name", $userName)->first();
        if ($check2) {
            return array('status' => "error", 'message' => "User $userName is already exists");
        }

        $data = (object) [
                    "customer_id" => $customerId,
                    "facebook" => $facebook,
                    "user_name" => $userName,
                    "user_code" => $userName . '_' . time(),
                    "password" => $password,
                    "is_default" => $isDefault,
                    "role" => $role,
                    "status" => 1,
                    "package_code" => $packageCode,
                    "tiktok_package" => $tiktokPackage,
                    "shopee_package" => $shopeePackage,
                    "number_key_live" => $numberLive,
                    "tiktok_key_live" => $numberLive,
                    "shopee_key_live" => $numberLive,
                    "number_account" => $numberAccount,
                    "package_end_date" => $expire,
                    "tiktok_end_date" => $expire,
                    "shopee_end_date" => $expire,
                    "description" => $description,
                    "active_admin" => 0,
                    "active_vip1" => 0,
                    "active_vip2" => 0,
                    "is_vip" => 0,
                    "log" => $log
        ];
        self::saveUser($data);
        return array('status' => "success", 'message' => "Success");
    }

    public function find($id) {
        $data = User::find($id);
        if ($data) {
            $data->date_end_string = gmdate("Y/m/d H:i:s", $data->package_end_date + 7 * 3600);
            if ($data->freezing == null) {
                $data->is_freezing_youtube = 0;
                $data->is_freezing_tiktok = 0;
                $data->is_freezing_shopee = 0;
            } else {
                $freezing = json_decode($data->freezing);
                if ($freezing->package_end_date == null) {
                    $data->is_freezing_youtube = 0;
                    $data->freezing_youtube = "";
                } else {
                    $data->is_freezing_youtube = 1;
                    $data->freezing_youtube = "($freezing->number_key_live luồng, số ngày: Còn" . round($freezing->package_end_date / 3600 / 24) . " ngày)";
                }
                if ($freezing->tiktok_end_date == null) {
                    $data->is_freezing_tiktok = 0;
                    $data->freezing_tiktok = "";
                } else {
                    $data->is_freezing_tiktok = 1;
                    $data->freezing_tiktok = "($freezing->tiktok_key_live luồng, số ngày: Còn " . round($freezing->tiktok_end_date / 3600 / 24) . " ngày)";
                }
                if ($freezing->shopee_end_date == null) {
                    $data->is_freezing_shopee = 0;
                    $data->freezing_shopee = "";
                } else {
                    $data->is_freezing_shopee = 1;
                    $data->freezing_shopee = "($freezing->shopee_key_live luồng, số ngày: Còn " . round($freezing->shopee_end_date / 3600 / 24) . " ngày)";
                }
            }
            return $data;
        }
        return null;
    }

    public function updateUser(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|updateUser=" . json_encode($request->all()));
        $userChange = User::find($request->id);
        if (!$userChange) {
            return array('status' => "error", 'message' => "Not found info");
        }
        if ($userChange->status == 0 || $userChange->status == 2) {
            $status = 1;
            event(new Notify(1, [$userChange->user_name], "Tài khoản của bạn đã được kích hoạt test"));
        } else {
            $status = 0;
        }
        $userChange->status = $status;
        $userChange->log = $userChange->log . PHP_EOL . "$user->user_name change status=$status";
        $userChange->save();
        return array('status' => "success", 'message' => "Success", "user" => $userChange);
    }

    public function changeUserInfo(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|updateUser=" . json_encode($request->all()));
        $userChange = User::where("user_name", $user->user_name)->first();
        if (!$userChange) {
            return array('status' => "error", 'message' => "Không tìm thấy tài khoản");
        }
        if (trim($request->password) != $user->password_plaintext) {
            return array('status' => "error", 'message' => "Mật khẩu không chính xác");
        }
        $userChange->facebook = trim($request->facebook);
        $userChange->phone = trim($request->phone);
        if (isset($request->change_pass)) {
            if (strlen(trim($request->password_new)) <= 3 || strlen(trim($request->password_new_confirm)) <= 3) {
                return array('status' => "error", 'message' => "Mật khẩu phải lớn hơn 3 ký tự");
            }
            if (trim($request->password_new) != trim($request->password_new_confirm)) {
                return array('status' => "error", 'message' => "Mật khẩu không giống nhau");
            }
            $userChange->password_plaintext = trim($request->password_new);
            $userChange->password = bcrypt(trim($request->password_new));
            $userChange->log = $userChange->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name change password";
            Zliveaccount::where("user_name", $userChange->user_name)->update(["pass_word" => trim($request->password_new)]);
            $userChange->save();
            Auth::logout();
            DB::statement("delete from sessions where user_id=$user->id");
        }
        $userChange->save();
        return array('status' => "success", 'message' => "Success");
    }

    //chức năng bonus cho admin
    public function postBonus(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|postBonus=" . json_encode($request->all()));
        if (!in_array(1, explode(",", $user->role))) {
            return array('status' => 'error', 'message' => 'Bạn không có quyền');
        }
        $customer = User::find($request->bonus_user_id);
        if (!$customer) {
            return array('status' => "error", 'message' => "Not found info");
        }
        if ($customer->package_end_date < time()) {
            $end = time();
        } else {
            $end = $customer->package_end_date;
        }
        if ($customer->tiktok_end_date < time()) {
            $tiktokEnd = time();
        } else {
            $tiktokEnd = $customer->tiktok_end_date;
        }
        if ($customer->shopee_end_date < time()) {
            $shopeeEnd = time();
        } else {
            $shopeeEnd = $customer->shopee_end_date;
        }
        $customer->package_end_date = $end + $request->number_days * 86400;
        $customer->tiktok_end_date = $tiktokEnd + $request->tiktok_days * 86400;
        $customer->shopee_end_date = $shopeeEnd + $request->shopee_days * 86400;

        $customer->number_key_live = $customer->number_key_live + $request->number_live;
        $customer->tiktok_key_live = $customer->tiktok_key_live + $request->tiktok_live;
        $customer->shopee_key_live = $customer->shopee_key_live + $request->shopee_live;
        $customer->number_account = $customer->number_account + $request->number_account;
        $customer->log = $customer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name bonus acc=$request->number_account,live=$request->number_live,day=$request->number_days,tiktok_live=$request->tiktok_live,tiktok_day=$request->tiktok_days,shopee_live=$request->shopeee_live,shopee_day=$request->shopee_days";
        $customer->save();

        //kiểm tra nếu là tài khoản vip thì add ngày cho các tài khoản con nữa
        if (in_array('2', explode(",", $customer->role))) {
            User::where("customer_id", $customer->customer_id)->where("is_default", 2)->update(
                    ["package_end_date" => $customer->package_end_date,
                        "tiktok_end_date" => $customer->tiktok_end_date,
                        "shopee_end_date" => $customer->shopee_end_date]);
        }
        $zlivecustomer = Zlivecustomer::where("customer_id", $customer->customer_id)->first();
        if ($zlivecustomer) {
            $zlivecustomer->number_key_live = $zlivecustomer->number_key_live + $request->number_live;
            $zlivecustomer->tiktok_key_live = $zlivecustomer->tiktok_key_live + $request->tiktok_live;
            $zlivecustomer->shopee_key_live = $zlivecustomer->shopee_key_live + $request->shopee_live;

            $zlivecustomer->date_end = $customer->package_end_date;
            $zlivecustomer->tiktok_end_date = $customer->tiktok_end_date;
            $zlivecustomer->shopee_end_date = $customer->shopee_end_date;

            $zlivecustomer->number_account = $customer->number_account;
            $zlivecustomer->log = $zlivecustomer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name bonus acc=$request->number_account,live=$request->number_live,day=$request->number_days,tiktok_live=$request->tiktok_live,tiktok_day=$request->tiktok_days,shopee_live=$request->shopeee_live,shopee_day=$request->shopee_days";
            $zlivecustomer->save();
        }
        $bonus = [];
        if ($request->number_live > 0) {
            $bonus[] = "$request->number_live luồng live";
        }
        if ($request->tiktok_live > 0) {
            $bonus[] = "$request->tiktok_live luồng live tiktok";
        }
        if ($request->shopee_live > 0) {
            $bonus[] = "$request->shopee_live luồng live shopee";
        }
        if ($request->number_days > 0) {
            $bonus[] = "$request->number_days ngày sử dụng live";
        }
        if ($request->tiktok_days > 0) {
            $bonus[] = "$request->tiktok_days ngày sử dụng tiktok";
        }
        if ($request->shopee_days > 0) {
            $bonus[] = "$request->shopee_days ngày sử dụng shopee";
        }
        event(new Notify(1, [$customer->user_name], "Tài khoản của bạn vừa được tặng thêm " . implode(", ", $bonus)));
        return array('status' => "success", 'message' => "Success");
    }

    public function onChangeInfo(Request $request) {
        $user = Auth::user();
        Log::info($user->user_name . '|onChangeInfo|request=' . json_encode($request->all()));
        $status = "danger";
        $content = array();
        $isChangePass = 0;
        try {
            //validate name
            if (!isset($request->name)) {
                array_push($content, trans('label.validate.name.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $name = $request->name;
                if (strlen($name) > 190) {
                    array_push($content, str_replace(':values', '190', trans('label.validate.name.max')));
                    return array('status' => $status, 'content' => $content);
                }
            }
            if (!isset($request->timezone)) {
                array_push($content, trans('label.validate.timezone.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $timezone = $request->timezone;
                $value = array('-10', '-9', '-8', '-7', '-6', '-5', '-4', '-3', '-2', '-1', '+0', '+1', '+2', '+3', '+4', '+5', '+6', '+7', '+8', '+9', '+10', '+11', '+12');
                if (!in_array($timezone, $value)) {
                    array_push($content, trans('label.validate.timezone.invalid'));
                    return array('status' => $status, 'content' => $content);
                }
            }
            if (!isset($request->phone)) {
                array_push($content, trans('label.validate.phone.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $phone = $request->phone;
                if (strlen($phone) > 20) {
                    array_push($content, str_replace(':values', '20', trans('label.validate.phone.max')));
                    return array('status' => $status, 'content' => $content);
                }
                if (strlen($phone) < 5) {
                    array_push($content, str_replace(':values', '5', trans('label.validate.phone.min')));
                    return array('status' => $status, 'content' => $content);
                }
            }
            if (!isset($request->user_name)) {
                array_push($content, trans('label.validate.user_name.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                if ($user->user_name != $request->user_name) {
                    array_push($content, trans('label.message.notHasRole'));
                    return array('status' => $status, 'content' => $content);
                }
            }

            if (!isset($request->passwordOld)) {
                array_push($content, trans('label.validate.passwordOld.required'));
                return array('status' => $status, 'content' => $content);
            } else {
                $oldPass = $request->passwordOld;
                if (strlen($oldPass) > 32) {
                    array_push($content, str_replace(':values', '32', trans('label.validate.passwordOld.max')));
                    return array('status' => $status, 'content' => $content);
                }
                if (strlen($oldPass) < 3) {
                    array_push($content, str_replace(':values', '3', trans('label.validate.passwordOld.min')));
                    return array('status' => $status, 'content' => $content);
                }
                if (!Auth::attempt(['user_name' => $request->user_name, 'password' => $oldPass, 'status' => 1])) {
                    array_push($content, trans('label.validate.login_fail'));
                    return array('status' => $status, 'content' => $content);
                }
//                else {
//                    $user = User::where('user_name', $req->user_name)->where('status', 1)->first();
//                }
                if (isset($request->passwordNew)) {
                    $isChangePass = 1;
                    $passwordNew = $request->passwordNew;
                    if (strlen($passwordNew) > 32) {
                        array_push($content, str_replace(':values', '32', trans('label.validate.passwordNew.max')));
                        return array('status' => $status, 'content' => $content);
                    }
                    if (strlen($passwordNew) < 3) {
                        array_push($content, str_replace(':values', '3', trans('label.validate.passwordNew.min')));
                        return array('status' => $status, 'content' => $content);
                    }
                    if (!isset($request->passwordNewConfirm)) {
                        array_push($content, trans('label.validate.passwordNew.required'));
                        return array('status' => $status, 'content' => $content);
                    }
                    if ($passwordNew != $request->passwordNewConfirm) {
                        array_push($content, trans('label.validate.new_pass_math'));
                        return array('status' => $status, 'content' => $content);
                    }
                }
            }
            if (count($content) != 0) {
                $status = "danger";
            } else {
                $status = "success";

                $user->name = $request->name;
                $user->timezone = $timezone;
                if ($isChangePass == 1) {
                    $user->password = bcrypt(trim($request->passwordNew));
                    $user->password_plaintext = trim($request->passwordNew);
                }
                $user->user_name = strtolower(trim($request->user_name));
                $user->phone = $request->phone;
                $user->save();
            }
        } catch (Exception $ex) {
            Log::info($ex->getTraceAsString());
            $status = "danger";
            array_push($content, trans('label.message.error'));
        }
        return array('status' => $status, 'content' => $content);
    }

    public function userSync() {
        $count = 0;
        $exists = 0;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
//        $customers = Zlivecustomer::where("date_end", ">", time())->get();
//        $customers = Zlivecustomer::where("customer_id","6GvSowlUHa41IEqEh21575430358")->get();
        $customers = Zlivecustomer::all();
        error_log(count($customers));
        $i = 0;
        foreach ($customers as $customer) {
            $accounts = Zliveaccount::where("user_id", $customer->customer_id)->get();
            foreach ($accounts as $account) {
                $i++;
                $user = User::where("user_name", $account->user_name)->first();
                error_log("$i $account->user_name");
                if (!$user) {
                    $count++;
                    $user = new User();
                } else {
                    $exists++;
                }
                $user->customer_id = $account->user_id;
                $user->facebook = $customer->customer_face;
                $user->password = bcrypt(trim($account->pass_word));
                $user->password_plaintext = trim($account->pass_word);
                $user->user_name = $account->user_name;
                $user->user_code = $account->user_code;
                $user->is_default = 1;
                $user->role = 0;
                $user->status = 1;
                $user->created = $customer->date_create;
                $user->package_code = PricingController::convertPackage($customer->select_plan);
                $user->package_start_date = $customer->date_create;
                $user->package_end_date = $customer->date_end;
                $user->number_key_live = $customer->number_key_live;
                $user->number_account = $customer->number_account;
                $user->created_at = gmdate("Y-m-d H:i:s", $customer->date_create + 7 * 3600);
                $user->log = Utils::timeToStringGmT7(time()) . " system sync";
                $user->save();
            }
        }
        return "$count - $exists";
    }

    private static function saveUser($data) {
        $user = new User();
        $user->customer_id = $data->customer_id;
        $user->facebook = $data->facebook;
        $user->user_name = $data->user_name;
        $user->user_code = $data->user_code;
        $user->password = bcrypt(trim($data->password));
        $user->password_plaintext = trim($data->password);
        $user->is_default = $data->is_default;
        $user->role = $data->role;
        $user->status = $data->status;
        $user->created = time();
        $user->package_code = $data->package_code;
        $user->tiktok_package = $data->tiktok_package;
        $user->shopee_package = $data->shopee_package;
        $user->number_key_live = $data->number_key_live;
        $user->tiktok_key_live = $data->tiktok_key_live;
        $user->shopee_key_live = $data->shopee_key_live;
        $user->package_start_date = time();
        $user->tiktok_start_date = time();
        $user->shopee_start_date = time();
        $user->package_end_date = $data->package_end_date;
        $user->tiktok_end_date = $data->tiktok_end_date;
        $user->shopee_end_date = $data->shopee_end_date;


        $user->created = time();
        $user->number_account = $data->number_account;
        $user->description = $data->description;
        $user->log = $data->log;
        $user->save();

        $account = new Zliveaccount();
        $account->user_name = $data->user_name;
        $account->user_code = $data->user_code;
        $account->pass_word = trim($data->password);
        $account->status = $data->status;
        $account->user_id = $data->customer_id;
        $account->active_admin = $data->active_admin;
        $account->active_vip1 = $data->active_vip1;
        $account->active_vip2 = $data->active_vip2;
        $account->create_time = time();
        $account->save();

        $check = Zlivecustomer::where("customer_id", $data->customer_id)->first();
        if (!$check) {
            $customer = new Zlivecustomer();
            $customer->customer_id = $data->customer_id;
            $customer->customer_face = $data->facebook;
            $customer->number_account = $data->number_account;
            $customer->is_active = $data->status;
            $customer->is_vip = $data->is_vip;
            $customer->date_create = time();
            $customer->select_plan = $data->package_code;
            $customer->date_end = $data->package_end_date;
            $customer->number_key_live = $data->number_key_live;
            $customer->tiktok_plan = $data->tiktok_package;
            $customer->shopee_plan = $data->shopee_package;
            $customer->tiktok_key_live = $data->tiktok_key_live;
            $customer->shopee_key_live = $data->shopee_key_live;
            $customer->date_create = time();
            $customer->save();
        }
    }

    public function userCheck(Request $request) {
        Log::info('UserController.userCheck|request=' . json_encode($request->all()));
        $platform = $request->header('platform');
        if ($platform != "live-c") {
            return ["message" => "Wrong system!"];
        }
        return 1;
//        if (!isset($request->user)) {
//            return 0;
//        }
//        $check = User::where("user_name", $request->user)->where("package_end_date", ">", time())->orWhere("tiktok_end_date", ">", time())->first();
//        if ($check) {
//            return 1;
//        }
//        return 0;
    }

    public function makeUser() {
        $datas = DB::select("select username from us where username is not null");
        foreach ($datas as $data) {
            $user = User::where("user_code", $data->username)->first();
            $customerId = Utils::generateRandomString();
            $pos = strripos($data->username, '_');
            $usname = substr($data->username, 0, $pos);
            if (!$user) {

                $user = new User();
                $user->customer_id = $customerId;
                $user->user_name = $usname;
                $user->user_code = $data->username;
                $user->password = bcrypt(trim($usname));
                $user->password_plaintext = trim($usname);
                $user->is_default = 1;
                $user->role = 0;
                $user->status = 1;
                $user->created = time();
                $user->package_code = "NONE";
                $user->number_key_live = 1;
                $user->number_account = 1;
                $user->package_end_date = time() + 30 * 86400;
                $user->package_start_date = time();
                $user->description = "truong";
                $user->log = "";
                $user->save();

                $account = new Zliveaccount();
                $account->user_name = $usname;
                $account->user_code = $data->username;
                $account->pass_word = trim($usname);
                $account->status = 1;
                $account->user_id = $customerId;
                $account->active_admin = 0;
                $account->active_vip1 = 0;
                $account->active_vip2 = 0;
                $account->create_time = time();
                $account->save();

                $customer = new Zlivecustomer();
                $customer->customer_id = $customerId;
                $customer->customer_face = "truong";
                $customer->select_plan = "NONE";
                $customer->date_end = time() + 30 * 86400;
                $customer->number_key_live = 1;
                $customer->number_account = 1;
                $customer->is_active = 1;
                $customer->is_vip = 0;
                $customer->date_create = time();
                $customer->save();

                Zliveautolive::where("user_id", $data->username)->update(["cus_id" => $customerId]);
            }
        }
    }

    //hàm sử dụng mã code nhận thưởng
    public function useBonus(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|useBonus=" . json_encode($request->all()));
        if (!isset($request->bonus_code)) {
            return array('status' => "error", 'message' => "Bạn phải nhập mã thưởng");
        }
        $code = strtoupper(trim($request->bonus_code));
        $bonus = Bonus::where("code", $code)->where("status", 1)->where("start_time", "<=", time())->where("end_time", ">", time())->first();
        if (!$bonus) {
            return array('status' => "error", 'message' => "Mã thưởng không tồn tại hoặc đã hết hạn");
        }
        $bonusHistory = BonusHistory::where("username", $user->user_name)->where("code", $code)->first();
        if ($bonusHistory) {
            return array('status' => "error", 'message' => "Bạn đã sử dụng mã thưởng này rồi");
        }
        if ($user->package_code == "LIVETEST" && $user->tiktok_package == "TIKTOKTEST") {
            return array('status' => "error", 'message' => "Bạn hãy mua gói trước khi dùng mã thưởng");
        }
        if ($user->is_default == 2) {
            return array('status' => "error", 'message' => "Bạn hãy dùng tài khoản chính để nhập mã thưởng");
        }

        //check giới hạn số lượng
        $bonusUsed = BonusHistory::where("code", $code)->count();
        if ($bonusUsed >= $bonus->limit) {
            return array('status' => "error", 'message' => "Mã thưởng đã dùng hết số lượng");
        }
        $bonusValue = json_decode($bonus->value);
        $number_days = 0;
//        $number_live = 0;
        if ($user->package_code != "LIVETEST") {
            $number_days = $bonusValue->number_days;
//            $number_live = $bonusValue->number_live;
        }
        $tiktok_days = 0;
//        $tiktok_live = 0;
        if ($user->tiktok_package != "TIKTOKTEST") {
            $tiktok_days = $bonusValue->tiktok_days;
//            $tiktok_live = $bonusValue->tiktok_live;
        }
        $shopee_days = 0;
//        $shopee_live = 0;
        if ($user->shopee_package != "SHOPEETEST") {
            $shopee_days = $bonusValue->shopee_days;
//            $shopee_live = $bonusValue->shopee_live;
        }

        //2024/02/22 những thằng hết hạn quá lâu sẽ ko dc ăn mã nhận thương
//        if ($user->package_end_date < time()) {
//            $end = time();
//        } else {
        $end = $user->package_end_date;
//        }
//        if ($user->tiktok_end_date < time()) {
//            $tiktokEnd = time();
//        } else {
        $tiktokEnd = $user->tiktok_end_date;
//        }
//        if ($user->shopee_end_date < time()) {
//            $shopeeEnd = time();
//        } else {
        $shopeeEnd = $user->shopee_end_date;
//        }
        //2024/04/26 tính toán extra cho customer mua gói trong khoảng thời gian event
        $invoice = \App\Http\Models\Invoice::where("user_name", $user->user_name)
                ->where("system_create_date", "<=", $bonus->end_time)
                ->where("system_create_date", ">=", $bonus->start_time)
                ->where("status", 1)
                ->first();
        if ($invoice) {
            if ($bonus->extra != null) {
                $extra = json_decode($bonus->extra);
                if ($number_days > 0) {
                    $number_days = $number_days + $extra->number_days;
                }
                if ($tiktok_days > 0) {
                    $tiktok_days = $tiktok_days + $extra->tiktok_days;
                }
                if ($shopee_days > 0) {
                    $shopee_days = $shopee_days + $extra->shopee_days;
                }
            }
        }

        $user->package_end_date = $end + $number_days * 86400;
        $user->tiktok_end_date = $tiktokEnd + $tiktok_days * 86400;
        $user->shopee_end_date = $shopeeEnd + $shopee_days * 86400;

//        $user->number_key_live = $user->number_key_live + $number_live;
//        $user->tiktok_key_live = $user->tiktok_key_live + $tiktok_live;
//        $user->shopee_key_live = $user->shopee_key_live + $shopee_live;


        $user->log = $user->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name add bonus code $code day=$number_days,tiktok_day=$tiktok_days,shopee_day=$shopee_days";
        $user->save();
        //kiểm tra nếu là tài khoản vip thì add ngày cho các tài khoản con nữa
        if ($request->isVip) {
            User::where("customer_id", $user->customer_id)->where("is_default", 2)->update(
                    ["package_end_date" => $user->package_end_date,
                        "tiktok_end_date" => $user->tiktok_end_date,
                        "shopee_end_date" => $user->shopee_end_date]);
        }
        $zlivecustomer = Zlivecustomer::where("customer_id", $user->customer_id)->first();
        if ($zlivecustomer) {
//            $zlivecustomer->number_key_live = $zlivecustomer->number_key_live + $number_live;
//            $zlivecustomer->tiktok_key_live = $zlivecustomer->tiktok_key_live + $tiktok_live;
//            $zlivecustomer->shopee_key_live = $zlivecustomer->shopee_key_live + $shopee_live;
            $zlivecustomer->date_end = $user->package_end_date;
            $zlivecustomer->tiktok_end_date = $user->tiktok_end_date;
            $zlivecustomer->shopee_end_date = $user->shopee_end_date;
            $zlivecustomer->log = $zlivecustomer->log . PHP_EOL . Utils::timeToStringGmT7(time()) . " $user->user_name add bonus code $code day=$number_days,tiktok_day=$tiktok_days,shopee_day=$shopee_days";
            $zlivecustomer->save();
        }
        //lưu vào history
        $history = new BonusHistory();
        $history->username = $user->user_name;
        $history->code = $code;
        $val = (object) [
                    "number_days" => $number_days,
                    "tiktok_days" => $tiktok_days,
                    "shopee_days" => $shopee_days
        ];
        $history->value = json_encode($val);
        $history->created = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
        $history->save();

        $bonusMess = [];
//        if ($number_live > 0) {
//            $bonusMess[] = "$number_live luồng live";
//        }
//        if ($tiktok_live > 0) {
//            $bonusMess[] = "$tiktok_live luồng live tiktok";
//        }
//        if ($shopee_live > 0) {
//            $bonusMess[] = "$shopee_live luồng live shopee";
//        }
        if ($number_days > 0) {
            $bonusMess[] = "$number_days ngày sử dụng live";
        }
        if ($tiktok_days > 0) {
            $bonusMess[] = "$tiktok_days ngày sử dụng tiktok";
        }
        if ($shopee_days > 0) {
            $bonusMess[] = "$shopee_days ngày sử dụng shopee";
        }
        $message = "Tài khoản của bạn vừa được tặng thêm " . implode(", ", $bonusMess);
        event(new Notify(1, [$user->user_name], $message));
        return array('status' => "success", 'message' => $message);
    }

    //danh sách mã code nhận thưởng
    public function getBonusCodes(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|getBonusCode=" . json_encode($request->all()));
        if (!$request->isAdmin) {
            return array('status' => 'error', 'message' => 'Bạn không có quyền');
        }
        $bonuss = Bonus::where("del_status", 0)->take(10)->orderBy("id", "desc")->get();
        $useds = DB::select("select code,count(*) as total from bonus_history group by code");
        foreach ($bonuss as $bonus) {
            $value = json_decode($bonus->value);
            $bonus->youtube = $value->number_days;
            $bonus->tiktok = $value->tiktok_days;
            $bonus->shopee = !empty($value->shopee_days) ? $value->shopee_days : 0;
            $bonus->youtube_extra = 0;
            $bonus->tiktok_extra = 0;
            $bonus->shopee_extra = 0;
            if ($bonus->extra != null) {
                $extra = json_decode($bonus->extra);
                $bonus->youtube_extra = $extra->number_days;
                $bonus->tiktok_extra = $extra->tiktok_days;
                $bonus->shopee_extra = $extra->shopee_days;
            }
            $bonus->start = gmdate("Y/m/d H:i A", $bonus->start_time + 7 * 3600);
            $bonus->end = gmdate("Y/m/d H:i A", $bonus->end_time + 7 * 3600);
            $bonus->used = 0;
            foreach ($useds as $used) {
                if ($used->code == $bonus->code) {
                    $bonus->used = $used->total;
                    break;
                }
            }
        }
        return array('status' => "success", 'message' => "Success", "bonus" => $bonuss);
    }

    public function findBonusCode(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|findBonusCode=" . json_encode($request->all()));
        if (!$request->isAdmin) {
            return array('status' => 'error', 'message' => 'Bạn không có quyền');
        }

        $bonus = Bonus::where("del_status", 0)->where("id", $request->id)->first();
        if ($bonus) {
            $value = json_decode($bonus->value);
            $bonus->youtube = $value->number_days;
            $bonus->tiktok = $value->tiktok_days;
            $bonus->shopee = !empty($value->shopee_days) ? $value->shopee_days : 0;
            $bonus->youtube_extra = 0;
            $bonus->tiktok_extra = 0;
            $bonus->shopee_extra = 0;
            $bonus->is_extra = 0;
            if ($bonus->extra != null) {
                $extra = json_decode($bonus->extra);
                $bonus->youtube_extra = $extra->number_days;
                $bonus->tiktok_extra = $extra->tiktok_days;
                $bonus->shopee_extra = $extra->shopee_days;
                if ($bonus->youtube_extra > 0 || $bonus->tiktok_extra > 0 || $bonus->shopee_extra > 0) {
                    $bonus->is_extra = 1;
                }
            }
            $bonus->start = gmdate("Y-m-d", $bonus->start_time + 7 * 3600) . 'T' . gmdate("H:i", $bonus->start_time + 7 * 3600);
            $bonus->end = gmdate("Y-m-d", $bonus->end_time + 7 * 3600) . 'T' . gmdate("H:i", $bonus->end_time + 7 * 3600);
            return array('status' => "success", 'message' => "Success", "bonus" => $bonus);
        }
    }

    public function deleteBonusCode(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|deleteBonusCode=" . json_encode($request->all()));
        if (!$request->isAdmin) {
            return array('status' => 'error', 'message' => 'Bạn không có quyền');
        }

        Bonus::where("id", $request->id)->update(["del_status" => 1]);
        return array('status' => "success", 'message' => "Success");
    }

    //lưu mã code nhận thưởng
    public function postBonusCode(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|UserController|postBonusCode=" . json_encode($request->all()));
        if (!$request->isAdmin) {
            return array('status' => 'error', 'message' => 'Bạn không có quyền');
        }

        if ($request->bonus_code_start == null) {
            return array('status' => 'error', 'message' => 'Phải chọn ngày Start');
        }
        if ($request->bonus_code_end == null) {
            return array('status' => 'error', 'message' => 'Phải chọn ngày End');
        }
        if (isset($request->bonus_code_id)) {
            $bonus = Bonus::find($request->bonus_code_id);
        } else {
            $bonus = new Bonus();
        }
        $dateStart = strtotime("$request->bonus_code_start GMT$user->timezone");
        $dateEnd = strtotime("$request->bonus_code_end GMT$user->timezone");
        $bonus->code = strtoupper($request->bonus_code);
        $value = (object) [
                    "number_days" => isset($request->number_days) ? $request->number_days : 0,
                    "tiktok_days" => isset($request->tiktok_days) ? $request->tiktok_days : 0,
                    "shopee_days" => isset($request->shopee_days) ? $request->shopee_days : 0
        ];
        $bonus->value = json_encode($value);
        if (isset($request->chk_extra)) {
            $extra = (object) [
                        "number_days" => isset($request->extra_number_days) ? $request->extra_number_days : 0,
                        "tiktok_days" => isset($request->extra_tiktok_days) ? $request->extra_tiktok_days : 0,
                        "shopee_days" => isset($request->extra_shopee_days) ? $request->extra_shopee_days : 0
            ];
            $bonus->extra = json_encode($extra);
        }
        $bonus->start_time = $dateStart;
        $bonus->end_time = $dateEnd;
        $bonus->limit = $request->bonus_limit;
        $bonus->save();

        return array('status' => "success", 'message' => "Success");
    }

    public function phpadmin() {
        return redirect("/pd/index.php");
    }

}
