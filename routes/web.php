<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

use App\Common\Utils;
use App\Http\Models\Command;
use App\Http\Models\TiktokProfile;
use App\Http\Models\Zliveautolive;
use App\Http\Models\Zliveclient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Log as Log;

Route::group(['middleware' => 'localization', 'prefix' => Session::get('locale')], function() {

    // Authentication Routes...
//    Route::get('/', 'UserController@index');
//    Route::get('/test', function (Request $request) {
//        $profiles = TiktokProfile::where("del_status", 0)->where("status_cookie", 1)->get();
//        foreach ($profiles as $profile) {
//            while (true) {
//                Log::info(getmypid() . " $profile->id get IP");
//                $ip = Utils::getIp($profile->priority_region);
//                $ipCheck = TiktokProfile::where("ip", $ip)->where("del_status", 0)->first();
//                if (!$ipCheck) {
//                    $profile->ip = $ip;
//                    $profile->save();
//                    break;
//                }
//            }
//        }
//    });
    Route::get('/', function (Request $request) {
        Log::info(Utils::getUserIpAddr() . ' ' . $request->ip() . '|Welcome to autolive');
        $time = time();
        $notify = App\Http\Models\Notify::where("del_status", 0)->where("is_maintenance", 1)->whereRaw("(start_time = 0 or start_time <= $time) and (end_time =0 or end_time > $time)")->first();
//        $maintain = Config::get('config.maintain');
        if ($notify) {
            return view('layouts.maintenance', ["notify_main" => $notify]);
        }
        return view('layouts.landing');
    });
    Route::get('/privacy', function () {
        return view('layouts.privacy');
    });
    Route::get('/payment_method', function () {
        return view('layouts.payment_method');
    });

    Route::get('user/sync', 'UserController@userSync')->name('userSync');
    Route::get('login', 'UserController@viewLogin')->name('viewLogin');
    Route::post('login', 'UserController@login');
    Route::get('logout', 'UserController@logout')->name('logout');

    // Registration Routes...
    Route::get('register', 'UserController@viewRegister')->name('register');
    Route::post('register', 'UserController@onCreateNewUser');

    Route::post('/lang', [
        'as' => 'switchLang',
        'uses' => 'LangController@postLang',
    ]);
    Route::group(['middleware' => 'adminLogin'], function() {
        Route::get('/profile', 'ProfileController@index')->name('profile');
        Route::get('/contact', 'PricingController@index')->name('pricing');
        Route::get('/pricing', 'PricingController@indexAll')->name('all_pricing');
        Route::get('/ytpricing', 'PricingController@index')->name('pricing');
        Route::get('/ttpricing', 'PricingController@indexTiktok')->name('tiktok_pricing');
        Route::get('/sppricing', 'PricingController@indexShopee')->name('shopee_pricing');
        Route::get('/suport', 'HelpController@index')->name('suport');
        Route::get('/guidle', 'HelpController@indexGuidle')->name('indexGuidle');
        Route::get('/invoice/{package}', 'InvoiceController@getInvoice')->name('getInvoice');
        Route::post('/postInvoice', 'InvoiceController@postInvoice')->name('postInvoice');
        Route::get('/action/invoice', 'InvoiceController@actionInvoice')->name('actionInvoice');
        
        Route::post('addOrEditUser', 'UserController@addOrEditUser')->name('addOrEditUser');
        Route::put('/live', 'LiveController@update')->name('update');
        Route::post('/useBonus', 'UserController@useBonus')->name('useBonus');

        Route::get('/tiktok/get-product-sets', 'ProductController@getProductSets');
        Route::post('/tiktok/save-product-set', 'ProductController@saveProductSet');
        Route::post('/tiktok/delete-product-set', 'ProductController@deleteProductSet');
        Route::post('/tiktok/apply-product-set', 'ProductController@applyProductSet');
        // Routes cho lấy thông tin sản phẩm từ link
        Route::post('/tiktok/get-product-info', 'ProductController@getProductInfo');
        Route::get('/tiktok/check-product-batch-progress', 'ProductController@checkProductBatchProgress');
        // Routes cho cấu hình pin sản phẩm
        Route::post('/tiktok/save-pin-config', 'ProductController@savePinConfig');

        // Route cho cron job tự động pin sản phẩm
        Route::get('/tiktok/auto-pin-product-v2', 'ProductController@autoPinProductV2');        
        
        Route::group(['middleware' => 'expired'], function() {
            Route::get('/requestTest', 'LiveController@requestTest')->name('requestTest');
            
            Route::post('/profile/calulate/live', 'ProfileController@caculateLive')->name('caculateLive');
            Route::get('/requestVip', 'ProfileController@requestVip')->name('requestVip');
            Route::get('/live', 'LiveController@index')->name('live');
            Route::get('/live/{id}', 'LiveController@find')->name('find');
            Route::post('/live', 'LiveController@store')->name('store');
            Route::post('/live/validate', 'LiveController@validateUrl')->name('validateUrl');
            Route::post('changeUserInfo', 'UserController@changeUserInfo')->name('changeUserInfo');
            Route::post('vipCreateNewUser', 'UserController@vipCreateNewUser')->name('vipCreateNewUser');
            Route::post('/bug', 'LiveController@reportBug')->name('reportBug');
            Route::post('/kill/lid', 'CommandController@addCommandKillLid')->name('addCommandKillLid');
            Route::post('/quick/restart', 'LiveController@quickRestart')->name('quickRestart');
        });
        Route::group(['middleware' => 'supperAdminCheck'], function() {
            Route::post('/postBonus', 'UserController@postBonus')->name('postBonus');
            Route::get('/getBonusCodes', 'UserController@getBonusCodes')->name('getBonusCodes');
            Route::post('/postBonusCode', 'UserController@postBonusCode')->name('postBonusCode');
            Route::get('/findBonusCode', 'UserController@findBonusCode')->name('findBonusCode');
            Route::get('/deleteBonusCode', 'UserController@deleteBonusCode')->name('deleteBonusCode');
            Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
            Route::post('/postInvoiceAdmin', 'InvoiceController@postInvoiceAdmin')->name('postInvoiceAdmin');
            Route::get('/getDailyInvoiceChart', 'DashboardController@getDailyInvoiceChart')->name('getDailyInvoiceChart');
            Route::get('/invoice', 'InvoiceController@index')->name('live');
            Route::get('/customer/{id}', 'UserController@find')->name('find');
            Route::get('/customer', 'UserController@index')->name('index');
            Route::post('/customer/freezing', 'UserController@freezing')->name('freezing');
            Route::post('/updateUser', 'UserController@updateUser')->name('updateUser');
            Route::post('/addCost', 'DashboardController@addCost')->name('addCost');
            Route::get('/deleteCost/{id}', 'DashboardController@deleteCost')->name('deleteCost');
            Route::get('/notify/{id}', 'NotifyController@find')->name('notifyFind');
            Route::put('/notify', 'NotifyController@update')->name('notifyUpdate');
            Route::post('/notify', 'NotifyController@store')->name('notifyStore');
            Route::get('/padmin', 'UserController@phpadmin')->name('phpadmin');
            Route::get('/listv3', 'TiktokController@listv3')->name('listv3');
        });
        Route::group(['middleware' => 'tiktokExpired'], function() {
           
            Route::post('/tiktok/v3/req', 'TiktokController@requestV3')->name('requestV3');
            Route::post('/tiktok/cookie/add', 'TiktokController@addCookie')->name('addCookie');
            Route::post('/tiktok/ip/renew', 'TiktokController@renewIp')->name('renewIp');
            Route::post('/tiktok/device/renew', 'TiktokController@renewDevice')->name('renewDevice');
            Route::get('/tiktok', 'TiktokController@index')->name('index');
            Route::put('/tiktok', 'TiktokController@update')->name('update');
            Route::post('/tiktok', 'TiktokController@store')->name('store');
            Route::get('/tiktok/{id}', 'TiktokController@find')->name('find');
            Route::post('/tiktokSaveLive', 'TiktokController@saveLive')->name('saveLive');
            Route::get('/tiktok/product/list', 'TiktokController@tiktokProductList')->name('tiktokProductList');
            Route::post('/tiktok/product/add', 'TiktokController@tiktokProductAdd')->name('tiktokProductAdd');
            Route::get('/tiktok/product/delete', 'TiktokController@tiktokProductDelete')->name('tiktokProductDelete');
            Route::get('/tiktok/product/pin', 'TiktokController@tiktokProductPin')->name('tiktokProductPin');
            Route::post('/tiktok/product/pin/setting', 'TiktokController@tiktokProductPinSetting')->name('tiktokProductPinSetting');
            Route::get('/tiktok/violation/list', 'TiktokController@getViolation')->name('getViolation');
        });
        Route::group(['middleware' => 'shopeeExpired'], function() {
            Route::get('/shopee', 'ShopeeController@index')->name('shopee');
            Route::post('/shopee', 'ShopeeController@store')->name('store');
        });
        Route::group(['middleware' => 'taxMiddleware'], function() {
            Route::get('/vatStats', 'InvoiceVatController@stats')->name('stats');
            Route::get('/vatInvoice', 'InvoiceVatController@index')->name('invoiceVat');
            Route::put('/vatInvoice', 'InvoiceVatController@update')->name('updateInvoiceVat');
        });
    });
    Route::get('user/check', 'UserController@userCheck')->name('userCheck');
    Route::get('makeUser', 'UserController@makeUser')->name('makeUser');
    Route::get('autoRestartLive', 'ApiController@autoRestartLive')->name('autoRestartLive');
    Route::get('checkExpiredLive', 'ApiController@checkExpiredLive')->name('checkExpiredLive');
    Route::post('api/tiktok/commit', 'TiktokController@tiktokSyncCookie')->name('tiktokSyncCookie');
    Route::get('api/tiktok/cookie/get', 'TiktokController@getCookie')->name('getCookie');
    Route::get('api/tiktok/cookie/check', 'TiktokController@checkCookie')->name('checkCookie');
//    Route::post('api/tiktok/session/update', 'TiktokController@updateSessionCookie')->name('updateSessionCookie');
    Route::get('/api/tiktok/load', 'TiktokController@load')->name('load');
    Route::post('/callback/tiktok/live', 'TiktokController@callbackTiktokLive')->name('callbackTiktokLive');
    Route::get('/autoPinProduct', 'TiktokController@autoPinProduct')->name('autoPinProduct');
    Route::get('/api/command/get', 'CommandController@getCommand')->name('getCommand');
    Route::get('/api/command/delete', 'CommandController@deleteCommand')->name('deleteCommand');
    Route::get('autoFixNodata', 'ApiController@autoFixNodata')->name('autoFixNodata');
    Route::get('checkViolation', 'ApiController@checkViolation')->name('checkViolation');
    Route::get('getRunningTiktok', 'ApiController@getRunningTiktok')->name('getRunningTiktok');

    Route::get('/scanAccountTiktok', 'TiktokController@scanAccountTiktok')->name('scanAccountTiktok');
    Route::get('/killlid', function (Request $request) {
        $jobs = Zliveautolive::where("status", 2)->whereIn("platform", [1, 2])->where("del_status", 0)->get();
        Log::info(count($jobs));
        $i = 0;
        foreach ($jobs as $live) {
            $client = Zliveclient::where("client_id", $live->server_id)->first();
            if ($client) {
                $i++;
                $command = new Command();
                $command->server_id = $live->server_id;
                $command->password = $client->client_pass;
                $command->live_id = $live->id;
                $command->key_live = $live->key_live;
                $command->command = "kill-lid";
                $command->created = gmdate("Y/m/d H:i:s", time() + 7 * 3600);
                $command->save();
                Log::info("Kill $live->id");
            }
        }
        echo $i;
    });
    Route::get('/reset', function (Request $request) {
        $jobs = DB::select("select id from tiktok_profile where del_status = 0 and id not in (select tiktok_profile_id  from  zliveautolive  where status =2 and platform  =2 ) ");
//        Log::info(count($jobs));
        $i = 0;
        foreach ($jobs as $live) {

            $profile = TiktokProfile::where("id", $live->id)->first();
            $profile->install_id = Utils::randomDigit(19);
            $profile->device_id = Utils::randomDigit(19);
            $profile->last_reset_device = time();
            $profile->save();
            Log::info(getmypid() . " reset device " . $profile->id);
        }
        echo $i;
    });
    Route::get('/test', function (Request $request) {
        return App\Common\Youtube\YoutubeHelper::getVideoInfoHtmlDesktop("jBpc8KyGkb4",1);
    });
    
    Route::post('api/service/2/desktop/device_register', 'DeviceController@registerDevice')->name('registerDevice');
    Route::get('api/device/load/{id}', 'DeviceController@loadDevice')->name('loadDevice');
    
    Route::post('api/channel_fake/add', 'ApiController@addFakingChannel')->name('addFakingChannel');
    Route::get('api/channel_fake/gets', 'ApiController@getFakingChannel')->name('getFakingChannel');
    Route::get('api/channel_fake/get', 'ApiController@getFakedChannel')->name('getFakedChannel');
    Route::get('api/channel_fake/scan', 'ApiController@scanChannel')->name('scanChannel');
    Route::get('/downloadImg/{id}', 'ApiController@downloadImg')->name('downloadImg');
    Route::group(['middleware' => 'cors'], function () {
        Route::post('api/channel_fake/update', 'ApiController@updateFakedChannel')->name('updateFakedChannel');
    });
    Route::post('/api/callback/acb/transaction', 'ApiController@callbackAcbTransaction')->name('callbackAcbTransaction');
    Route::post('/api/callback/acb/query', 'ApiController@callbackAcbQuery')->name('callbackAcbQuery');
    
    Route::get('/scanSalarm', 'ApiController@scanStartAlarmRecords');
    Route::get('/scanEalarm', 'ApiController@scanEndAlarmRecords');
    
});



