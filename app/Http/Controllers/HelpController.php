<?php

namespace App\Http\Controllers;

use App\Http\Models\Package;
use Illuminate\Http\Request;
use Log;

class HelpController extends Controller {

    public function index() {
        return view('components.help');
    }

    public function indexGuidle() {

        // PHP Array of guides data
        $guidesData = [
                [
                'category' => 'Hướng Dẫn, Thủ Thuật',
                'title' => 'Hướng dẫn kích hoạt Livestream Tiktok V3 Tự Động',
                'date' => 'Nov 19, 2024',
                'author' => 'Sáng Nguyễn',
                'content' => 'Xin chào anh em, Sau thời gian chạy thử nghiệm đạt được hiệu quả cao, livestream tiktok mắt nổ ngay khi lên live. Tool Autolive.vip chính thức update phiên bản…',
                'url' => 'https://blog.autolive.vip/huong-dan-kich-hoat-livestream-tiktok-v3-autolive-vip/'
            ],
                [
                'category' => 'Hướng Dẫn',
                'title' => 'Hướng dẫn Livestream Tiktok Studio V2',
                'date' => 'Jul 26, 2024',
                'author' => 'Sáng Nguyễn',
                'content' => 'Đây là phiên bản update mới của autolive.me để khắc phục tình trạng livestream bị dừng của một số anh em Livestream Tiktok. Bước 1: Anh em cần update Tiktok…',
                'url' => 'https://blog.autolive.vip/huong-dan-livestream-tiktok-studio-v2/'
            ],
                [
                'category' => 'Hướng Dẫn, Thủ Thuật',
                'title' => 'Hướng dẫn livestream Tiktok bằng Proxy riêng! Update Tiktok Livestream Ver 2.2289',
                'date' => 'Apr 19, 2024',
                'author' => 'Sáng Nguyễn',
                'content' => 'Hướng dẫn livestream Tiktok bằng Proxy riêng! Update Tiktok Livestream Ver 2.2289 Tool autolive.vip vừa update chức năng mới khi anh em sử dụng phần mềm autolive.vip để Livestream Tiktok:…',
                'url' => 'https://blog.autolive.vip/huong-dan-livestream-tiktok-bang-proxy/'
            ],
                [
                'category' => 'Hướng Dẫn',
                'title' => 'Hướng dẫn livestream facebook liên tục, livestream facebook không dừng bằng Autolive',
                'date' => 'Mar 27, 2024',
                'author' => 'Sáng Nguyễn',
                'content' => 'Đây là chú ý quan trọng cho anh em livestream facebook liên tục, livestream facebook không dừng. Đầu tiên anh em cần chuẩn bị một tâm hồn thật đẹp và…',
                'url' => 'https://blog.autolive.vip/huong-dan-livestream-facebook-lien-tuc/'
            ],
                [
                'category' => 'Hướng Dẫn, Thủ Thuật',
                'title' => 'Hướng Dẫn Livestream TIKTOK – Đồng Bộ Tài Khoản Tiktok Qua Cookie',
                'date' => 'Mar 4, 2024',
                'author' => 'Sáng Nguyễn',
                'content' => 'Tiktok Us đang làm rất căng khoản IP, Tiktok cũng đã update Tiktok Studio US để phát hiện ra anh em đang FAKE IP hoặc sử dụng Tiktok Studio VN…',
                'url' => 'https://blog.autolive.vip/huong-dan-livestream-tiktok-us/'
            ],
                [
                'category' => 'Hướng Dẫn',
                'title' => 'Hướng Dẫn Livestream Facebook không bị dừng',
                'date' => 'Aug 17, 2023',
                'author' => 'Sáng Nguyễn',
                'content' => 'Đây là video hướng dẫn Livestream Facebook đầy đủ chi tiết dành cho anh em nào chưa biết cách livestream Facebook nhé: Mình hướng dẫn anh em cách livestream facebook…',
                'url' => 'https://blog.autolive.vip/huong-dan-livestream-facebook-khong-bi-dung/'
            ],
                [
                'category' => 'Hướng Dẫn, Thủ Thuật',
                'title' => '[TUT] Hướng Dẫn LiveStream Youtube Hiệu Quả',
                'date' => 'Dec 7, 2022',
                'author' => 'Sáng Nguyễn',
                'content' => 'Xin chào anh em, như đã hứa thì hôm nay mình sẽ hướng dẫn anh em các Live Stream cắn Views. Đây là cách mình đã sử dụng và thành…',
                'url' => 'https://blog.autolive.vip/tut-huong-dan-livestream-youtube-hieu-qua/'
            ],
                [
                'category' => 'Hướng Dẫn',
                'title' => 'Hướng Dẫn Xử Lý Lỗi Live Stream Bị Giật Lag',
                'date' => 'Aug 17, 2022',
                'author' => 'Sáng Nguyễn',
                'content' => 'Hướng dẫn sử lý lỗi Live Stream Youtube, Live Facebook bị giật lag. Dưới đây mình sẽ hướng dẫn anh em xử lý lỗi Live Stream bị giật lag do…',
                'url' => 'https://blog.autolive.vip/huong-dan-xu-ly-loi-live-stream-bi-giat-lag/'
            ]
        ];
        // Featured guide (main content at the top)
        $featuredGuide = $guidesData[0];

        // Return view with data
        return view('components.guidle', compact('guidesData', 'featuredGuide'));
    }

}
