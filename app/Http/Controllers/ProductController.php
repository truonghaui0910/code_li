<?php

namespace App\Http\Controllers;

use App\Common\Utils;
use App\Http\Models\TiktokProfile;
use App\Models\Zliveautolive;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class ProductController extends Controller {

    public function getProductSets(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.getProductSets");

        $productSets = isset($user->product_sets) ? json_decode($user->product_sets, true) : [];

        if (!is_array($productSets)) {
            $productSets = [];
        }

        return array("status" => "success", "productSets" => $productSets);
    }

    public function saveProductSet(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.saveProductSet|request=" . json_encode($request->all()));

        $data = json_decode($request->product_set, true);

        if (!isset($data['name']) || !isset($data['products']) || empty($data['products'])) {
            return array("status" => "error", "message" => "Thiếu thông tin bộ sản phẩm");
        }

        // Lấy danh sách bộ sản phẩm hiện có
        $productSets = isset($user->product_sets) ? json_decode($user->product_sets, true) : [];
        if (!is_array($productSets)) {
            $productSets = [];
        }

        // Kiểm tra xem là tạo mới hay cập nhật
        if (isset($data['id']) && $data['id']) {
            // Cập nhật bộ sản phẩm
            $found = false;
            foreach ($productSets as $key => $set) {
                if (isset($set['id']) && $set['id'] == $data['id']) {
                    // Giữ lại thời gian tạo nếu có
                    if (isset($productSets[$key]['created_at'])) {
                        $data['created_at'] = $productSets[$key]['created_at'];
                    }
                    $productSets[$key] = $data;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return array("status" => "error", "message" => "Không tìm thấy bộ sản phẩm");
            }
        } else {
            // Tạo mới bộ sản phẩm
            $nextId = 1;
            if (!empty($productSets)) {
                // Tìm ID lớn nhất và tăng thêm 1
                $maxId = 0;
                foreach ($productSets as $set) {
                    if (isset($set['id']) && $set['id'] > $maxId) {
                        $maxId = $set['id'];
                    }
                }
                $nextId = $maxId + 1;
            }

            $data['id'] = $nextId;
            $data['created_at'] = date('Y-m-d H:i:s');
            $productSets[] = $data;
        }

        // Thêm thời gian cập nhật
        foreach ($productSets as $key => $set) {
            if (isset($set['id']) && $set['id'] == $data['id']) {
                $productSets[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
        }

        // Lưu vào database
        $user->product_sets = json_encode($productSets);
        $user->save();

        return array("status" => "success", "message" => "Đã lưu bộ sản phẩm thành công", "id" => $data['id']);
    }

    public function deleteProductSet(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.deleteProductSet|request=" . json_encode($request->all()));

        $productSetId = $request->product_set_id;

        if (!$productSetId) {
            return array("status" => "error", "message" => "Thiếu thông tin bộ sản phẩm");
        }

        // Lấy danh sách bộ sản phẩm hiện có
        $productSets = isset($user->product_sets) ? json_decode($user->product_sets, true) : [];
        if (!is_array($productSets)) {
            $productSets = [];
        }

        // Tìm và xóa bộ sản phẩm
        $found = false;
        foreach ($productSets as $key => $set) {
            if (isset($set['id']) && $set['id'] == $productSetId) {
                unset($productSets[$key]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return array("status" => "error", "message" => "Không tìm thấy bộ sản phẩm");
        }

        // Reset keys của mảng
        $productSets = array_values($productSets);

        // Lưu vào database
        $user->product_sets = json_encode($productSets);
        $user->save();

        return array("status" => "success", "message" => "Đã xóa bộ sản phẩm thành công");
    }

    /**
     * Cập nhật thứ tự sản phẩm trong bộ
     */
    public function updateProductSetOrder(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.updateProductSetOrder|request=" . json_encode($request->all()));

        $productSetId = $request->product_set_id;
        $newOrder = json_decode($request->new_order, true);

        if (!$productSetId || !$newOrder) {
            return array("status" => "error", "message" => "Thiếu thông tin bộ sản phẩm hoặc thứ tự mới");
        }

        // Lấy danh sách bộ sản phẩm hiện có
        $productSets = isset($user->product_sets) ? json_decode($user->product_sets, true) : [];
        if (!is_array($productSets)) {
            $productSets = [];
        }

        // Tìm bộ sản phẩm cần cập nhật
        $found = false;
        foreach ($productSets as $key => $set) {
            if (isset($set['id']) && $set['id'] == $productSetId) {
                // Tạo một bản đồ từ product_id sang sản phẩm đầy đủ
                $productMap = [];
                foreach ($set['products'] as $product) {
                    if (isset($product['product_id'])) {
                        $productMap[$product['product_id']] = $product;
                    }
                }

                // Tạo mảng sản phẩm mới theo thứ tự mới
                $newProducts = [];
                foreach ($newOrder as $productId) {
                    if (isset($productMap[$productId])) {
                        $newProducts[] = $productMap[$productId];
                    }
                }

                // Cập nhật danh sách sản phẩm
                $productSets[$key]['products'] = $newProducts;
                $productSets[$key]['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }

        if (!$found) {
            return array("status" => "error", "message" => "Không tìm thấy bộ sản phẩm");
        }

        // Lưu vào database
        $user->product_sets = json_encode($productSets);
        $user->save();

        return array("status" => "success", "message" => "Đã cập nhật thứ tự sản phẩm thành công");
    }

    /**
     * Áp dụng bộ sản phẩm cho livestream
     */
    public function applyProductSet(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.applyProductSet|request=" . json_encode($request->all()));

        $live = Zliveautolive::where("id", $request->live_id)->first();
        if (!$live) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin livestream");
        }

        if (!$request->isAdmin) {
            if ($user->user_code != $live->user_id) {
                return array("status" => "error", "message" => "Không tìm thấy thông tin livestream trên tài khoản của bạn");
            }
        }

        // Lấy danh sách bộ sản phẩm
        $productSets = isset($user->product_sets) ? json_decode($user->product_sets, true) : [];
        if (!is_array($productSets)) {
            $productSets = [];
            return array("status" => "error", "message" => "Không có bộ sản phẩm nào");
        }

        // Tìm bộ sản phẩm được chọn
        $productSetId = $request->product_set_id;
        $selectedSet = null;

        foreach ($productSets as $set) {
            if (isset($set['id']) && $set['id'] == $productSetId) {
                $selectedSet = $set;
                break;
            }
        }

        if (!$selectedSet) {
            return array("status" => "error", "message" => "Không tìm thấy bộ sản phẩm");
        }

        if (!isset($selectedSet['products']) || empty($selectedSet['products'])) {
            return array("status" => "error", "message" => "Bộ sản phẩm không có sản phẩm nào");
        }

        $products = $selectedSet['products'];
        $count = 0;
        $results = [];

        foreach ($products as $product) {
            if (!isset($product['product_id'])) {
                continue;
            }

            // Thêm sản phẩm vào livestream
            $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_6_capt.py product_add $live->tiktok_profile_id $live->room_id \"https://shop.tiktok.com/view/product/{$product['product_id']}?region=VN&local=en\"";
            Log::info("$user->user_name|applyProductSet|cmd:" . $cmd);
            $tmp = shell_exec($cmd);
            Log::info("$user->user_name|applyProductSet|tmp:" . $tmp);
            $shell = trim($tmp);
            $status = "error";

            if ($shell != null && $shell != "") {
                if (Utils::containString($shell, "permission")) {
                    return array("status" => "error", "message" => "Tài khoản của bạn cần kích hoạt Tiktok Shop! Xin Cảm Ơn", "result" => $results);
                }

                $pro = json_decode($shell);
                if (isset($pro->code) && $pro->code == 0 && isset($pro->message) && $pro->message == 'success') {
                    $count++;
                    $status = "success";
                }
            }

            $results[] = (object) [
                        "product_id" => $product['product_id'],
                        "name" => isset($product['name']) ? $product['name'] : 'Sản phẩm',
                        "status" => $status
            ];
        }

        return array(
            "status" => "success",
            "message" => "Đã thêm $count/" . count($products) . " sản phẩm vào livestream",
            "result" => $results
        );
    }

    public function getProductInfo(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.getProductInfo|request=" . json_encode($request->all()));

        // Lấy danh sách link sản phẩm
        $links = explode("\n", $request->product_links);
        $links = array_map('trim', $links);
        $links = array_filter($links, function($link) {
            return !empty($link);
        });

        if (empty($links)) {
            return array("status" => "error", "message" => "Không có link sản phẩm nào");
        }

        // Lấy profile ID của TikTok Shop (có thể lấy từ request hoặc mặc định từ người dùng)
        $profileId = 26186;



        // Xử lý link đầu tiên (để có phản hồi nhanh)
        $firstLink = array_shift($links);
        $products = [];
        $product = $this->processProductLink($firstLink, $profileId, $user);

        if ($product) {
            $product['original_link'] = $firstLink;
            $products[] = $product;
        }

        // Lưu các link còn lại để xử lý bất đồng bộ
        if (!empty($links)) {
            $batchId = uniqid('prod_');

            // Lưu thông tin batch vào cache thay vì session
            $batchData = [
                'links' => $links,
                'profile_id' => $profileId,
                'processed' => 0,
                'total' => count($links),
                'products' => $products,
                'user_id' => $user->user_code,
                'original_links' => [$firstLink]
            ];

            // Lưu vào cache với thời gian sống là 1 giờ
            Cache::put("product_batch_$batchId", $batchData, 3600);

            // Trả về thông tin batch để frontend có thể kiểm tra tiến độ
            return array(
                "status" => "processing",
                "message" => "Đang xử lý " . count($links) . " link còn lại...",
                "batch_id" => $batchId,
                "products" => $products,
                "processed" => 1,
                "total" => count($links) + 1
            );
        }

        if (empty($products)) {
            return array("status" => "error", "message" => "Không thể lấy thông tin sản phẩm từ các link đã nhập");
        }

        return array("status" => "success", "products" => $products);
    }

    /**
     * Xử lý một link sản phẩm
     */
    private function processProductLink($link, $profileId, $user) {
        // Sử dụng câu lệnh để kiểm tra sản phẩm
        $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_7_capt_test4.py product_check $profileId \"$link\"";

        Log::info("$user->user_name|processProductLink|cmd:" . $cmd);
        $tmp = shell_exec($cmd);
        Log::info("$user->user_name|processProductLink|response:" . $tmp);

        $shell = trim($tmp);

        // Kiểm tra nếu kết quả là None hoặc rỗng
        if ($shell == "None" || $shell == null || $shell == "") {
            return null;
        }

        // Xử lý dữ liệu Python Dictionary thành JSON
        if (substr($shell, 0, 1) == '{' && substr($shell, -1) == '}') {
            // Thay thế nháy đơn thành nháy kép
            $shell = str_replace("'", '"', $shell);
            // Đảm bảo các key JSON được bọc trong dấu ngoặc kép
            $shell = preg_replace('/([{,])\s*([a-zA-Z0-9_]+)\s*:/', '$1"$2":', $shell);
        }

        try {
            $productData = json_decode($shell, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("$user->user_name|processProductLink|JSON parse error: " . json_last_error_msg());
                Log::error("$user->user_name|processProductLink|Raw data: " . $shell);
                return null;
            }

            // Xử lý trường price (loại bỏ ký tự đơn vị tiền tệ nếu có)
            $price = isset($productData['price']) ? $productData['price'] : '0';
            $price = preg_replace('/[^\d.,]/', '', $price); // Chỉ giữ lại số, dấu phẩy và dấu chấm
            $price = str_replace(',', '.', $price); // Chuyển dấu phẩy thành dấu chấm

            $productInfo = [
                'product_id' => $productData['product_id'] ?? '',
                'name' => $productData['title'] ?? '',
                'price' => $price,
                'description' => $productData['title'] ?? '', // Sử dụng title làm description nếu không có mô tả
                'image' => $productData['thumbnail_url'] ?? '',
                // Thông tin bổ sung
                'main_plan_id' => $productData['main_plan_id'] ?? '',
                'product_type' => $productData['product_type'] ?? 0,
                'source' => $productData['source'] ?? '',
                'source_from' => $productData['source_from'] ?? 0,
                'store_name' => $productData['store_name'] ?? '',
                'stock_num' => $productData['stock_num'] ?? 0,
                'commission' => $productData['commission'] ?? ''
            ];

            return $productInfo;
        } catch (Exception $e) {
            Log::error("$user->user_name|processProductLink|Exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra tiến độ xử lý các link sản phẩm
     */
    public function checkProductBatchProgress(Request $request) {
        $batchId = $request->batch_id;

        if (!$batchId) {
            return array("status" => "error", "message" => "Thiếu thông tin batch ID");
        }

        $batchData = Cache::get("product_batch_$batchId");

        if (!$batchData) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin batch hoặc đã hết hạn");
        }

        // Kiểm tra quyền truy cập
        $user = Auth::user();
        if ($batchData['user_id'] != $user->user_code) {
            return array("status" => "error", "message" => "Bạn không có quyền truy cập batch này");
        }

        // Xử lý thêm một số link nếu còn
        $maxProcessPerRequest = 2; // Số lượng link xử lý tối đa mỗi lần kiểm tra
        $newProducts = [];
        $processedLinks = [];

        for ($i = 0; $i < $maxProcessPerRequest && !empty($batchData['links']); $i++) {
            $link = array_shift($batchData['links']);
            $processedLinks[] = $link;
            $product = $this->processProductLink($link, $batchData['profile_id'], $user);

            if ($product) {
                $product['original_link'] = $link;
                $newProducts[] = $product;
            }

            $batchData['processed'] ++;
        }

        // Cập nhật danh sách link đã xử lý
        if (!isset($batchData['original_links'])) {
            $batchData['original_links'] = [];
        }
        $batchData['original_links'] = array_merge($batchData['original_links'], $processedLinks);        
        
        // Cập nhật danh sách sản phẩm
        $batchData['products'] = array_merge($batchData['products'], $newProducts);

        // Kiểm tra xem đã hoàn thành chưa
        if (empty($batchData['links'])) {
            // Đã xử lý xong tất cả các link
            Cache::forget("product_batch_$batchId");

            if (empty($batchData['products'])) {
                return array("status" => "error", "message" => "Không thể lấy thông tin sản phẩm từ các link đã nhập");
            }

            return array(
                "status" => "success",
                "products" => $batchData['products']
            );
        }

        // Lưu lại trạng thái batch vào cache
        Cache::put("product_batch_$batchId", $batchData, 3600);

        // Vẫn đang xử lý
        return array(
            "status" => "processing",
            "message" => "Đang xử lý " . count($batchData['links']) . " link còn lại...",
            "batch_id" => $batchId,
            "products" => $batchData['products'],
            "processed" => $batchData['processed'],
            "total" => $batchData['total']
        );
    }

    public function savePinConfig(Request $request) {
        $user = Auth::user();
        Log::info("$user->user_name|TiktokController.savePinConfig|request=" . json_encode($request->all()));

        // Lấy thông tin profile
        $profileId = $request->profile_id;

        if (!$profileId) {
            return array("status" => "error", "message" => "Thiếu thông tin profile ID");
        }

        $profile = TiktokProfile::where('id', $profileId)
                ->where('username', $user->user_name)
                ->first();

        if (!$profile) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin profile TikTok");
        }

        // Lấy cấu hình pin từ request
        $pinType = $request->pin_type; // 'interval' hoặc 'specific'
        // Tạo cấu hình pin
        $pinConfig = [
            'pin_type' => $pinType,
            'updated_at' => time()
        ];

        if ($pinType == 'interval') {
            // Kiểu 1: Khoảng thời gian
            $interval = intval($request->interval);

            if ($interval < 30) {
                return array("status" => "error", "message" => "Khoảng thời gian pin phải lớn hơn hoặc bằng 30 giây");
            }

            $pinConfig['interval'] = $interval;

            // Lấy danh sách sản phẩm từ user
            $productSetId = $request->product_set_id;
            $productSets = isset($user->product_sets) ? json_decode($user->product_sets, true) : [];

            // Tìm bộ sản phẩm được chọn
            $selectedSet = null;
            foreach ($productSets as $set) {
                if ($set['id'] == $productSetId) {
                    $selectedSet = $set;
                    break;
                }
            }

            if (!$selectedSet) {
                return array("status" => "error", "message" => "Không tìm thấy bộ sản phẩm");
            }

            // Lưu danh sách ID sản phẩm
            $pinConfig['product_set_id'] = $productSetId;
            $pinConfig['product_ids'] = array_map(function($product) {
                return $product['product_id'];
            }, $selectedSet['products']);
        } else {
            // Kiểu 2: Thời điểm cụ thể
            $products = json_decode($request->products, true);

            if (empty($products)) {
                return array("status" => "error", "message" => "Thiếu thông tin thời gian pin cho sản phẩm");
            }

            // Sắp xếp sản phẩm theo thời gian pin
            usort($products, function($a, $b) {
                return $a['pin_time'] - $b['pin_time'];
            });

            $pinConfig['products'] = $products;
        }

        // Lưu cấu hình vào profile
        $profile->product_pin_config = json_encode($pinConfig);
        $profile->save();

        return array("status" => "success", "message" => "Đã lưu cấu hình pin sản phẩm thành công");
    }

    /**
     * Lấy cấu hình pin sản phẩm
     */
    public function getPinConfig(Request $request) {
        $user = Auth::user();

        // Lấy thông tin profile
        $profileId = $request->profile_id;

        if (!$profileId) {
            return array("status" => "error", "message" => "Thiếu thông tin profile ID");
        }

        $profile = TiktokProfile::where('id', $profileId)
                ->where('username', $user->user_name)
                ->first();

        if (!$profile) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin profile TikTok");
        }

        // Lấy cấu hình pin
        $pinConfig = isset($profile->product_pin_config) ? json_decode($profile->product_pin_config, true) : null;

        if (!$pinConfig) {
            return array("status" => "success", "pin_config" => null);
        }

        // Nếu cấu hình có product_set_id, lấy thông tin bộ sản phẩm từ user
        if (isset($pinConfig['product_set_id'])) {
            $productSets = isset($user->product_sets) ? json_decode($user->product_sets, true) : [];

            foreach ($productSets as $set) {
                if ($set['id'] == $pinConfig['product_set_id']) {
                    $pinConfig['product_set'] = $set;
                    break;
                }
            }
        }

        return array("status" => "success", "pin_config" => $pinConfig);
    }

    /**
     * Xóa cấu hình pin sản phẩm
     */
    public function deletePinConfig(Request $request) {
        $user = Auth::user();

        // Lấy thông tin profile
        $profileId = $request->profile_id;

        if (!$profileId) {
            return array("status" => "error", "message" => "Thiếu thông tin profile ID");
        }

        $profile = TiktokProfile::where('id', $profileId)
                ->where('user_id', $user->user_code)
                ->first();

        if (!$profile) {
            return array("status" => "error", "message" => "Không tìm thấy thông tin profile TikTok");
        }

        // Xóa cấu hình pin
        $profile->product_pin_config = null;
        $profile->save();

        return array("status" => "success", "message" => "Đã xóa cấu hình pin sản phẩm thành công");
    }

    /**
     * Tự động pin sản phẩm
     * Hàm này sẽ chạy định kỳ qua cron job
     */
    public function autoPinProducts() {
        // Lấy tất cả các live đang chạy
        $runningLives = Zliveautolive::where('status', 2)->get();

        foreach ($runningLives as $live) {
            // Kiểm tra xem có profile không
            if (!$live->tiktok_profile_id) {
                continue;
            }

            // Lấy thông tin profile
            $profile = TiktokProfile::where('id', $live->tiktok_profile_id)->first();
            if (!$profile || !$profile->product_pin_config) {
                continue;
            }

            // Parse cấu hình pin
            $pinConfig = json_decode($profile->product_pin_config, true);
            if (!$pinConfig || !isset($pinConfig['pin_type'])) {
                continue;
            }

            // Lấy danh sách sản phẩm đã pin
            $pinnedProducts = isset($live->pinned_products) ? json_decode($live->pinned_products, true) : [];
            if (!$pinnedProducts) {
                $pinnedProducts = [];
            }

            // Xử lý theo loại pin
            if ($pinConfig['pin_type'] == 'interval') {
                $this->processIntervalPin($live, $profile, $pinConfig, $pinnedProducts);
            } else {
                $this->processSpecificTimePin($live, $profile, $pinConfig, $pinnedProducts);
            }
        }
    }

    /**
     * Xử lý pin theo khoảng thời gian
     */
    private function processIntervalPin($live, $profile, $pinConfig, $pinnedProducts) {
        // Kiểm tra xem có cần pin sản phẩm không
        $nextPinTime = isset($live->next_pin_time) ? $live->next_pin_time : 0;
        if ($nextPinTime > time()) {
            return;
        }

        // Lấy danh sách sản phẩm cần pin
        $productIds = $pinConfig['product_ids'] ?? [];
        if (empty($productIds)) {
            return;
        }

        // Lọc ra các sản phẩm chưa pin
        if (!empty($pinnedProducts)) {
            $remainingProducts = array_diff($productIds, $pinnedProducts);

            // Nếu tất cả sản phẩm đã được pin, bắt đầu lại từ đầu
            if (empty($remainingProducts)) {
                $remainingProducts = $productIds;
                $pinnedProducts = [];
            }
        } else {
            $remainingProducts = $productIds;
        }

        // Lấy sản phẩm tiếp theo cần pin
        $nextProductId = reset($remainingProducts);

        // Thực hiện pin sản phẩm
        $pinResult = $this->pinProduct($live, $nextProductId);

        // Nếu pin thành công, cập nhật trạng thái
        if ($pinResult) {
            // Thêm sản phẩm vào danh sách đã pin
            $pinnedProducts[] = $nextProductId;

            // Cập nhật vào live
            $live->pinned_products = json_encode($pinnedProducts);

            // Cập nhật thời gian pin tiếp theo
            $live->next_pin_time = time() + $pinConfig['interval'];

            // Lưu vào database
            $live->save();

            Log::info("Đã pin sản phẩm $nextProductId cho live {$live->id}, sản phẩm tiếp theo sẽ được pin lúc " . date('Y-m-d H:i:s', $live->next_pin_time));
        }
    }

    /**
     * Xử lý pin theo thời điểm cụ thể
     */
    private function processSpecificTimePin($live, $profile, $pinConfig, $pinnedProducts) {
        // Kiểm tra thời gian bắt đầu live
        if (!$live->started_time) {
            return;
        }

        // Lấy danh sách sản phẩm và thời gian pin
        $products = $pinConfig['products'] ?? [];
        if (empty($products)) {
            return;
        }

        // Tính thời gian đã trôi qua từ khi bắt đầu live (phút)
        $liveStartedTime = strtotime($live->started_time);
        $elapsedMinutes = floor((time() - $liveStartedTime) / 60);

        Log::info("Live {$live->id} đã chạy được $elapsedMinutes phút");

        // Tìm các sản phẩm cần pin tại thời điểm hiện tại
        foreach ($products as $product) {
            $productId = $product['product_id'];
            $pinTime = $product['pin_time'];

            // Kiểm tra xem sản phẩm đã được pin chưa
            if (in_array($productId, $pinnedProducts)) {
                continue;
            }

            // Kiểm tra xem đã đến thời gian pin chưa
            if ($elapsedMinutes >= $pinTime) {
                // Thực hiện pin sản phẩm
                $pinResult = $this->pinProduct($live, $productId);

                // Nếu pin thành công, cập nhật trạng thái
                if ($pinResult) {
                    // Thêm sản phẩm vào danh sách đã pin
                    $pinnedProducts[] = $productId;

                    // Cập nhật vào live
                    $live->pinned_products = json_encode($pinnedProducts);
                    $live->save();

                    Log::info("Đã pin sản phẩm $productId cho live {$live->id} tại phút thứ $pinTime");
                }
            }
        }
    }

    /**
     * Thực hiện pin sản phẩm
     */
    private function pinProduct($live, $productId) {
        // Sử dụng câu lệnh để pin sản phẩm
        $cmd = "/home/tiktok_tools/env/bin/python /home/tiktok_tools/tiktok_helper_6_capt.py product_pin $live->tiktok_profile_id $live->room_id $productId";
        Log::info("pinProduct cmd:" . $cmd);

        $tmp = shell_exec($cmd);
        $shell = trim($tmp);

        if ($shell == null || $shell == "") {
            return false;
        }

        $pro = json_decode($shell);
        return (!empty($pro->code) && $pro->code == 0);
    }

}
