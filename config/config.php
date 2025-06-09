<?php

return [
//key mã hóa fb_id để gửi xuống client
    'encrypt_key_fb' => 'autoseo69_encrypt_key_fb@12345',
//    giá trị max keyword được phép nhập
    'max_keyword_input' => '3',
    'separate_text' => '@;@',
    //số thread để chạy init playlist( lấy video)
    'number_thread_init' => 10,
    //số thread để chạy tạo playlist
    'number_thread_create_pll' => 10,
    //số thread để chạy thêm video vào playlist
    'number_thread_run' => 30,
    //số thread để đồng bộ lại vị trí của video x
    'number_thread_re_index' => 20,
    //số thread để theo dõi video từ search hoặc channel pll
    'number_thread_subscribe' => 20,
    //số thread để theo dõi video và số lượng video của pll
    'number_thread_sync' => 20,
    //số thread để check thêm video x vào pll
    'number_thread_insert_x' => 10,
    //số thread để xóa video x từ pll
    'number_thread_del_x' => 10,
    //số thread để xóa video x từ pll
    'number_thread_del_die' => 10,
    //số thread để scan channel
    'number_thread_scan_channel' => 10,
    //số thread để post bài blog
    'number_thread_post_blog' => 10,
    //số thread để đồng bộ video seo
    'number_thread_video_seo_sync' => 10,
    //số thread để quét user quá hạn
    'number_thread_scan_expired_user' => 1,
    //số thread để quét user quá hạn
    'number_thread_scan_prepare_expired_user' => 1,
    // phần trăm danh sách vip được trích ra
    'percent_vip_list' => 30,
    //max page search để lấy video mỗi lần
    'max_search_page' => 10,
    //max page playlist để lấy video mỗi lần
    'max_pll_page' => 100,
    // url redirect để xác thực quyền youtube.
    'url_redirect' => 'http://admin.autoreup.win/admin/cbyt',
    // url redirect sau khi xác thực quyền youtube.
    'url_after_authen_youtube' => 'http://admin.autoreup.win/admin/channel',
    // url redirect sau khi xác thực quyền blog.
    'url_after_authen_blog' => 'http://admin.autoreup.win/admin/blogManage',
    //max dòng log cho phép của bản playlist details, vượt quá tự động cắt.
    'max_rows_log' => 10,
    //max số lần lấy dữ liệu views và videos của pll
    'max_sync_log' => 60,
    //độ lệch time tối thiểu cho lần tới chạy (20p)
    'bias_next_time_run' => 1200,
    //thời gian mỗi video được tăng (10p)
    'time_each_video' => 600,
    //thời gian giãn cách đồng bộ lại vị trí video (12h)
    'time_delay_reindex' => 43200,
    //thời gian giãn cách theo dõi kênh video (24h)
    'time_delay_subscribe' => 86400,
    //thời gian giãn cách đồng bộ view + video của pll (12h)
    'time_delay_sync' => 43200,
    //thời gian giãn cách đồng bộ view + video của pll (12h)
    'time_delay_del_die' => 43200,
    //thời gian giãn cách scan channel
    'time_delay_scan_channel' => 43200,
    //thời gian giãn cách scan channel(24h)
    'time_delay_post_blog' => 86400,
    //thời gian giãn cách đồng bộ video seo(24h)
    'time_delay_video_seo_sync' => 86400,
    //thời gian min nextime_run trong playlist_detail (s)
    'min_next_time_run' => 60,
    //thời gian max nextime_run trong playlist_detail (s)
    'max_next_time_run' => 100,
    'telegram' => 'https://api.telegram.org/bot5488201944:AAFCkwyWPX8iowijIqL5beiiEyLEHcPFyio/sendMessage?chat_id=-668807125&text=',
    'livelog' => 'https://api.telegram.org/bot5626129297:AAHNsaEHqwvrB_LZ5FAiQ1eivRXS303QKyY/sendMessage?chat_id=-653705791&text=',
    'app_url' => 'http://v2.autolive.win',
    'maintain' => 0,
    'python_path' => '/home/tiktok_tools/env/bin/python /home/v21.autolive.vip/public_html/python/python.py'
];
