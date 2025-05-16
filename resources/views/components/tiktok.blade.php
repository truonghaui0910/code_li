@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-6 col-xl-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                <div class="bg-icon bg-icon-violet pull-left">
                    <i class="ti-package text-info"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark m-t-10"><b class="counter">{{ $user_login->tiktok_package }}</b>
                    </h3>
                    <p class="text-muted mb-0">Gói cước của bạn là</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                <div class="bg-icon bg-icon-violet pull-left">
                    <i class="ti-alarm-clock text-info"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark m-t-10"><b
                            class="counter">{{ \App\Common\Utils::countDayLeft($user_login->tiktok_end_date) }}</b></h3>
                    <p class="text-muted mb-0">Thời hạn sử dụng
                        {{ gmdate('Y/m/d', $user_login->tiktok_end_date + 7 * 3600) }}</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                <div class="bg-icon bg-icon-violet pull-left">
                    <i class="ti-archive text-info"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark m-t-10"><b class="counter"><span
                                id="count_total">{{ count($tiktokProfile) }}</span>/{{ $user_login->tiktok_key_live * 10 }}
                            tài khoản</b></h3>
                    <p class="text-muted mb-0">Số tài khoản đã thêm</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                <div class="bg-icon bg-icon-violet pull-left">
                    <i class="ti-pulse text-info"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark m-t-10"><b class="counter count_run"><span
                                id="count_run">{{ $countRun }}</span>/{{ $user_login->tiktok_key_live }} luồng</b>
                    </h3>
                    <p class="text-muted mb-0">Số luồng live đang chạy</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="row animated fadeInDown">
        <div class="col-lg-12">
            <div class="card-box">
                <!--<h4 class="header-title m-t-0 color-red">Thông báo</h4>-->
                <div class="pa-20 color-red" style="font-weight: 500;">
                    <p>Mời các bạn tham gia nhóm Zalo 68 Học Viện Livestream để nhận CODE quà tặng, giảm giá. Được tư vấn,
                        hỗ trợ nhiệt tình!Link tham gia nhóm
                        <a target="_blank" href="https://zalo.me/g/msuihg308">https://zalo.me/g/msuihg308</a>
                        Hoặc scan QR code <img width="200px" src="/images/zalo_group_1.png">

                    </p>
                </div>
            </div>
        </div>
    </div>
    @if ($user_login->tiktok_end_date < time() + 3 * 86400 && $user_login->tiktok_package != 'TIKTOKTEST')
        <div class="row animated fadeInDown">
            <div class="col-lg-12">
                <div class="card-box">
                    <h4 class="header-title m-t-0 color-red">Thông báo</h4>
                    <div class="pa-20 color-red" style="font-weight: 500;">
                        <p>Tài khoản live tiktok của bạn sắp hết hạn, hãy gia hạn sớm để tránh làm gián đoạn luồng live</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (isset($notify))
        <div class="row animated fadeInDown">
            <div class="col-lg-12">
                <div class="card-box">
                    <h4 class="header-title m-t-0 color-red">Thông báo</h4>
                    <div class="pa-20 color-red" style="font-weight: 500;">
                        {!! $notify->content !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div id="edit_slideup" class="row animated fadeInDown">
        <div class="col-lg-12">
            <div class="card-box">
                <table style="width: 100%">
                    <tr>
                        <td>
                            <h4 class="header-title m-t-0">Cấu hình TikTok</h4>
                        </td>
                        <td>
                            <h4 class="header-title m-t-0 m-b-30 pull-right"><a data-toggle="tooltip" data-placement="top"
                                    data-html="true"
                                    title="<p style='text-align:left'>
                                                                            B1: Download tool về.<br>
                                                                            B2: Giải nén file.<br>
                                                                            B3: Chạy file setup_tiktoksync_real.exe (run as administrator).<br>
                                                                            </p>"
                                    target="_blank"
                                    href="https://drive.google.com/file/d/1aLI629ElwmTSKuM8RDC2P5s6cmNxavl0/view?usp=share_link">Tool
                                    Live Tiktok</a></h4>
                        </td>
                    </tr>
                </table>
<!--                <div class="alert alert-danger">
                    <strong>Chú ý!</strong> Bạn phải download Tool Live Tiktok trước khi thêm tài khoản tiktok. <a
                        data-toggle="tooltip" data-placement="top" data-html="true"
                        title="<p style='text-align:left'>
                                                                            B1: Download tool về.<br>
                                                                            B2: Giải nén file.<br>
                                                                            B3: Chạy file setup_tiktoksync_real.exe (run as administrator).<br>
                                                                            </p>"
                        target="_blank"
                        href="https://drive.google.com/file/d/1aLI629ElwmTSKuM8RDC2P5s6cmNxavl0/view?usp=share_link">
                        Download</a>
                </div>-->
                <div class="pa-20">
                    <form id="formLive" class="form-horizontal" role="form">
                        {{ csrf_field() }}
                        <input type="hidden" name="edit_id" id="edit_id" />

                        <!--                        <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <table class="w-100">
                                            <td>
                                                <label class="col-6 col-form-label">Tài khoản tiktok</label>
                                                <div class="col-12">
                                                    <select id="tiktok_account" class="form-control" name="tiktok_account">
                                                        {!! $tiktokAccount !!}
                                                    </select>
                                                </div>
                                            </td>
                                            <td style="vertical-align: bottom">
                                                <button type="button" class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-add-tiktok" data-toggle="tooltip" data-placement="top" title="Thêm tài khoản tiktok"><i class="fa fa-plus cur-point"></i></button>
                                                <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-add-tiktok"
                                                    style="border-radius:5px;width: 165px"
                                                    data-type="studio"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Thêm tài khoản Tiktok sử dụng Titok Live Studio">
                                                    <i class="ion-plus cur-point"></i> Thêm TK bằng Studio</button>
                                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-add-tiktok"
                                                    style="border-radius:5px;width: 155px"
                                                    data-type="web"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Thêm tài khoản Tiktok sử dụng cookie trên Website">
                                                    <i class="ion-plus cur-point"></i> Thêm TK bằng Web</button>
                                            </td>
                          
                                        </table>

                                    </div>
                                </div>
                            </div>-->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tiktok_account">Tài khoản tiktok</label>
                                    <select id="tiktok_account" class="form-control" name="tiktok_account">
                                        {!! $tiktokAccount !!}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label><br>
                                    <div class="d-flex justify-content-lg-start w-100">
                                        <button
                                            class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-add-tiktok m-r-10"
                                            style="border-radius:5px;width: 165px" data-type="studio"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Thêm tài khoản Tiktok sử dụng Titok Live Studio">
                                            <i class="ion-plus cur-point"></i> Thêm TK bằng Studio</button>
                                        <button
                                            class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-add-tiktok"
                                            style="border-radius:5px;width: 155px" data-type="web" data-toggle="tooltip"
                                            data-placement="top"
                                            title="Thêm tài khoản Tiktok sử dụng cookie trên Website">
                                            <i class="ion-plus cur-point"></i> Thêm TK bằng Web</button>

                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Tiêu đề</label>
                                    <div class="col-12">
                                        <input type="text" class="form-control" name="title" id="title"
                                            placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Chủ đề</label>
                                    <div class="col-12">
                                        <select id="topic" class="form-control" name="topic">
                                            {!! $tiktokTopic !!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Kiểu live</label>
                                    <div class="col-12">

                                        <button type="button" data-type="live_studio"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15 pricing-box-active"
                                            value="live_studio">
                                            <i class="" data-toggle="tooltip" data-placement="top"
                                                title="Live Studio">Live Studio</i></button>
                                        <button type="button" data-type="live_mobile"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15"
                                            value="live_mobile">
                                            <i class="" data-toggle="tooltip" data-placement="top"
                                                title="Live Mobile">Live Mobile</i></button>
                                        <button type="button" data-type="live_studio_v2"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15"
                                            value="live_studio_v2">
                                            <i class="" data-toggle="tooltip" data-placement="top" data-html="true"
                                               title="Live Studio V2<br>Chỉ chạy khi add tài khoản bằng studio">Studio V2
                                            </i>
                                        </button>
                                        <button id="live_studio_v3" type="button" data-type="live_studio_v3"
                                                class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15 " style="display: none"
                                            value="live_studio_v3">
                                            <i class="" data-toggle="tooltip" data-placement="top" data-html="true"
                                               title="Live Studio V2<br>Chỉ chạy khi add tài khoản bằng studio">Studio V3
                                            </i>
                                        </button>
                                        <input id="live_type" type="hidden" name="live_type" value="live_studio" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Link nguồn <span class="load-check-url"><i
                                                class="ion-load-c fa-spin"></i> Đang kiểm tra...</span></label>
                                    <div class="col-12 position-relative">
                                        <textarea id="url_source" class="form-control resize-ta" name="url_source" rows="8"
                                            onchange="validateSource()" spellcheck="false"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <ul>
                                            <li>
                                                <span class="font-13 font-weight-normal font-italic">Sử dụng 1 link Google
                                                    Drive đạt hiệu năng tốt nhất,link youtube có hiện tượng lag</span>
                                            </li>
                                            <li>
                                                <span class="font-13 font-weight-normal font-italic">Thông số video chuẩn:
                                                    định dạng: .mp4 , mã hóa: h264 , chất lượng âm thanh: 128kbs 44100
                                                    Hz</span>
                                            </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <div id="result_check" class="col-md-6" style="display: none">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">&nbsp;</label>
                                    <div class="col-12 position-relative">

                                        <textarea id="url_source_check" class="form-control resize-ta" rows="8" spellcheck="false"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-6 col-form-label">Thứ tự live</label>
                                    <div class="col-12 p-t-5 p-l-16">
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="radio_by" value="0" name="radio_by"
                                                checked="true">
                                            <label for="radio_by"> Lần lượt </label>
                                        </div>
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="radio_random" value="1" name="radio_by">
                                            <label for="radio_random"> Ngẫu nhiên </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="checkbox">
                                            <input id="live_repeat" type="checkbox" name="live_repeat" value="0"
                                                checked>
                                            <label for="live_repeat">
                                                Live vĩnh viễn
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div_live_repeat m-l-15 animated wow">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="checkbox">
                                                <input id="infinite_loop" type="checkbox" name="infinite_loop"
                                                    value="1" checked>
                                                <label for="infinite_loop">
                                                    Tiến trình vô hạn
                                                </label>
                                            </div>
                                            <span class="font-13"><i>Tiến trình được duy trì ổn định mãi mãi, phù hợp cho
                                                    luồng live 1 file</i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="checkbox">
                                            <input id="radio_time" type="checkbox" name="radio_time" checked>
                                            <label for="radio_time">
                                                Live ngay bây giờ
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div_radio_time m-l-15 disp-none">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-6 col-form-label">Cấu hình thời gian</label>
                                        <div class="col-12">
                                            <input type="text" id="date_start" class="form-control" name="date_start"
                                                placeholder="Thời gian bắt đầu">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="checkbox">
                                                <input id="chk_date_end" type="checkbox" name="chk_date_end">
                                                <label for="chk_date_end">
                                                    Kết thúc
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row div_chk_date_end disp-none">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <input type="text" id="date_end" class="form-control" name="date_end"
                                                placeholder="Thời gian kết thúc">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        @if($isTiktokMulti)
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="checkbox">
                                                <input id="is_multi_live" type="checkbox" name="is_multi_live">
                                                <label for="is_multi_live">
                                                    Multi Command
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row div_is_multi_live disp-none">
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <input type="text" id="duration" class="form-control" name="duration"
                                                placeholder="Video duration (minute)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <input type="text" id="delay" class="form-control" name="delay"
                                                placeholder="Delay time (minute)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <button type="button" class="btn btn-outline-violet waves-effect waves-primary btn-save-live "
                            <?php echo $user_login->status == 0 ? 'disabled' : ''; ?>>Auto live</button>
                        @if ($user_login->status == 0)
                            <span id="rq-test"><a onclick="requestTest('rq-test')" href="javascript:void()">Yêu cầu
                                    dùng thử</a> hoặc </span><span>Liên hệ <a target="_blnk"
                                    href="https://www.facebook.com/messages/t/100002470941874"> <b>Admin</b> </a> để được
                                kích hoạt dùng thử</span>
                        @endif
                        @if($user_login->tiktok_package!="TIKTOKTEST" && $user_login->tiktok_end_date >time() && $user_login->tiktok_end_date - 2 * 86400 < time())
                            <p class="font-20 m-t-10">Gói cước {{$user_login->tiktok_package}} sắp hết hạn, hãy gia hạn sớm <a href="/invoice/{{$user_login->tiktok_package}}" class=""><b>GIA HẠN</b></a></p>
                        @endif
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div class="row animated fadeInDown">
        <div class="col-lg-6">
            <div class="card-box" style="min-height: 152px;">
                <h4 class="header-title m-t-0"><i class="fa fa-filter"></i> Lọc dữ liệu</h4>
                <div class="col-md-12 col-sm-6 col-xs-12">

                    <form id="formFilter" class="form-label-left" action="/tiktok" method="GET">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Tiêu đề</label>
                                    <div class="col-12">
                                        <input id="note" class="form-control" type="text" name="note"
                                            value="{{ $request->note }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Trạng thái</label>
                                    <div class="col-12">
                                        <select id="cbbStatus" name="s" class="form-control">
                                            {!! $statusLive !!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">&nbsp;</label>
                                    <div class="col-12">
                                        <button id="btnSearch" type="submit" class="btn btn-dark btn-micro"><i
                                                class="fa fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<!--        <div class="col-lg-6">
            <button id="start-play">Start</button>
            <video id="rtc_media_player" width="320" autoplay="" muted="" style="display: inline-block;"></video>
        </div>-->

    </div>
    <div class="row animated fadeInDown">
        <div class="col-lg-12">
            <div class="card-box">
                <!--<h4 class="m-t-0 header-title">Danh sách cấu hình</h4>-->
                <table style="width: 100%">
                    <tr>
                        <td>
                            <h4 class="header-title m-t-0 m-b-30">Danh sách cấu hình <i style="font-size: 13px"
                                    class="color-green">(Nếu bị lỗi thì copy ID (chữ số màu đỏ) gửi cho admin)</i></h4>
                        </td>
                        <td>
                            <h4 class="header-title m-t-0 m-b-30 pull-right"><a data-toggle="tooltip"
                                    data-placement="top" data-html="true"
                                    title="<p style='text-align:left'>Tool này dùng để fix nguồn bị sai bit-rate,encode...<br>
                                                                            Bước 1: Download tool về,sau đó giải nén.<br>
                                                                            Bước 2: Mở file live-tool.exe và nhập username như yêu cầu<br>
                                                                            Bước 3: Chọn file nguồn sau đó tool sẽ tự động fix lỗi.<br>
                                                                            Bước 4: Upload file kết quả lên drive và sử dụng làm file nguồn để live</p>"
                                    target="_blank"
                                    href="https://drive.google.com/file/d/1MPMRxNhqYoUug-t-IXDBPa3Sgy9-J1F8/view?usp=sharing">Tool
                                    Fix Live</a></h4>
                        </td>
                    </tr>
                </table>
                <br>
                @if (count($datas) > 0)
                    <div class="table-responsive">
                        <table id="table-live" class="table hover-button-table">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-left w-10">Tiêu đề</th>
                                    <th class="text-center">Tiktok</th>
                                    <th class="text-center">Kiểu Live</th>
                                    <th class="text-center">Thời gian bắt đầu</th>
                                    <th class="text-center">Thời gian kết thúc</th>
                                    <th class="text-center">Thời gian live</th>
                                    <th class="w-15">Log</th>
                                    <th class="w-15 text-center">Speed</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-right w-15">Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $data)
                                    <?php
                                    $red = '';
                                    $tip = '';
                                    if ($data->log != null) {
                                        if (\App\Common\Utils::containString($data->log, 'is not support') || \App\Common\Utils::containString($data->log, 'is too hight')) {
                                            $red = 'color-red';
                                            $tip = 'Lệnh của bạn đang bị lỗi, hãy dùng tool fix live để xử lý.Click để xem hướng dẫn chi tiết';
                                            $log = "Video của bạn bị lỗi Render. Bạn cần Render lại video định dạng .mp4, codec = h264, audio bitrate = 44100. Xem hướng dẫn sửa lỗi bằng cách bấm vào nút 'Hướng dẫn sửa lỗi' ở bên cạnh !";
                                        } elseif (\App\Common\Utils::containString($data->log, 'not found') || \App\Common\Utils::containString($data->log, 'Link sai định dạng') || \App\Common\Utils::containString($data->log, 'cho phép download')) {
                                            $red = 'color-red';
                                            $tip = 'Link nguồn của bạn có vấn đề, vui lòng kiểm tra lại.';
                                            $log = 'Link nguồn của bạn có vấn đề, vui lòng kiểm tra lại.';
                                        } elseif (\App\Common\Utils::containString($data->log, 'Video < 1MB')) {
                                            $red = 'color-red';
                                            $tip = 'Không thể download được video, vui lòng kiểm tra lại link. Bạn cần sử dụng link Google Drive chia sẻ công khai';
                                            $log = 'Không thể download được video, vui lòng kiểm tra lại link. Bạn cần sử dụng link Google Drive chia sẻ công khai';
                                        } else {
                                            $log = substr_replace($data->log, '<span class="dots">...</span><span class="more">', 30, 0) . '</span><div class="view-more color-violet cur-point" onclick="">Xem thêm</div>';
                                        }
                                    } else {
                                        $log = '';
                                    }
                                    ?>
                                    <tr class="{{ $red }}">
                                        <td class="text-center color-violet"><b>{{ $data->id }}</b></td>
                                        <td class="text-left">{{ $data->note }}</td>
                                        <td class="text-center">{{ $data->tiktok_profile_id }}:
                                            {{ $data->tiktok_profile_name }}</td>
                                        <td class="text-center">
                                            @if ($data->command == 'live_mobile')
                                                Mobile
                                            @elseif($data->command=='live_studio_v2')
                                                Studio V2
                                            @elseif($data->command=='live_studio_v3')
                                                 Studio V3
                                            @else
                                                Studio
                                            @endif
                                        </td>
                                        <td class="text-center ur-status">
                                            @if ($data->start_alarm == 0)
                                                Chạy ngay
                                            @else
                                                {{ gmdate('m/d/Y H:i', $data->start_alarm + $user_login->timezone * 3600) }}
                                            @endif
                                        </td>
                                        <td class="text-center ur-status">
                                            @if ($data->end_alarm != 0)
                                                {{ gmdate('m/d/Y H:i', $data->end_alarm + $user_login->timezone * 3600) }}
                                            @elseif($data->repeat == 0)
                                                Vĩnh viễn
                                            @else
                                                Hết video
                                            @endif
                                        </td>
                                        <td class="text-center">{{ \App\Common\Utils::timeText($data->started_time) }}
                                        </td>
                                        <td>
                                            {!! $log !!}
                                        </td>
                                        <td>
                                            <div><canvas id="speed{{ $data->id }}"></canvas></div>
                                        </td>
                                        <td class="text-center ur-status">
                                            @if ($data->status == 0)
                                                @if ($red == '')
                                                    <span class="badge badge-success">Mới</span>
                                                @else
                                                    <a target="_blank"
                                                        href="https://blog.autolive.vip/huong-dan-xu-ly-loi-live-stream-bi-giat-lag/"><span
                                                            class="badge badge-danger" data-toggle="tooltip"
                                                            data-placement="top" title="{{ $tip }}">Hướng dẫn
                                                            sửa lỗi</span></a>
                                                @endif
                                            @elseif($data->status == 1)
                                                <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang
                                                    đợi live</span>
                                            @elseif($data->status == 2)
                                                <span class="badge badge-violet">Đang live</span>
                                            @elseif($data->status == 3)
                                                <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang
                                                    đợi dừng</span>
                                            @elseif($data->status == 4)
                                                <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang
                                                    xử lý</span> {!! $data->estimate !!}
                                            @elseif($data->status == 5)
                                                <span class="badge badge-danger">Đã dừng</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($data->status == 0)
                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-quick-restart"
                                                    data-id="{{ $data->id }}" data-status='1' data-toggle="tooltip"
                                                    data-placement="top" title="Fix tự dừng luồng"><i
                                                        class="fa fa-wrench cur-point"></i></button>
                                            @endif
                                            @if ($data->violations_count > 0)
                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-violations-log"
                                                    onclick="viewViolationsLog({{ $data->id }})"
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Xem log violations"><i
                                                        class="fa fa-exclamation-triangle cur-point"></i></button>
                                            @endif
                                            @if ($data->status == 2 && $data->repeat == 0)
                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-restart-live"
                                                    style="border-radius:5px;width: 85px" data-id="{{ $data->id }}"
                                                    data-toggle="tooltip" data-placement="top" title="Fix Nodata">Fix
                                                    Nodata</button>
                                            @endif
                                            @if ($data->is_report == 0)
                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-report-bug"
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Báo lỗi live"><i
                                                        class="fa fa-bug cur-point"></i></button>
                                            @endif
                                            @if ($isAdmin)
<!--                                                <button
                                                    id="open-modal"
                                                    class=" btn btn-circle btn-dark btn-sm waves-effect waves-light btn-action-log"
                                                    
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Config"><i
                                                        class="fa fa-phone cur-point"></i></button>-->

                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-action-log"
                                                    onclick="viewActionLog({{ $data->id }})"
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Xem log action"><i
                                                        class="fa fa-info-circle cur-point"></i></button>
                                            @endif
                                            @if ($data->status == 0 || $data->status == 5)
                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-status-live"
                                                    data-id="{{ $data->id }}" data-status='1' data-toggle="tooltip"
                                                    data-placement="top" title="Bắt đầu live"><i
                                                        class="fa fa-play cur-point"></i></button>
                                            @elseif($data->status == 2 || ($data->status == 4 && $data->estimate_time_run < time()) || $data->status == 1)
                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-status-live"
                                                    data-id="{{ $data->id }}" data-status='3' data-toggle="tooltip"
                                                    data-placement="top" title="Dừng live"><i
                                                        class="fa fa-stop cur-point"></i></button>
                                            @endif
                                            @if ($data->status == 2)
                                                <button
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-dialog-product"
                                                    onclick="viewProduct({{ $data->id }})"
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Thêm sản phẩm"><i
                                                        class="fa fa-shopping-cart cur-point"></i></button>
                                            @endif
                                            <button
                                                class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-edit-live"
                                                data-id="{{ $data->id }}" data-toggle="tooltip" data-placement="top"
                                                title="Sửa thông tin"><i
                                                    class="fa fa-pencil-square-o cur-point"></i></button>
                                            <button
                                                class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-status-live"
                                                data-id="{{ $data->id }}" data-status='-1' data-toggle="tooltip"
                                                data-placement="top" title="Xóa"><i
                                                    class="fa fa-times-circle cur-point"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <center>Không có dữ liệu</center>
                @endif

            </div>
        </div>
    </div>


    @include('dialog.add_tiktok')
    @include('dialog.add_product')
    @include('dialog.tiktok_pin_setting')
    @include('dialog.cookie')
    @include('dialog.violation')
    @include('dialog.camera')
    @if ($isAdmin)
        @include('dialog.log')
    @endif
    @include('dialog.product_manage')


@endsection

@section('script')
    <script type="text/javascript">

        $("#tiktok_account").change(function(){
            var selectedOption = $(this).find('option:selected');
            if (selectedOption.data('type') === 'v3') {
                $("#live_studio_v3").show();
            }else{
                $("#live_studio_v3").hide();
            }
        });
        // Lấy danh sách thiết bị đầu vào
//        navigator.mediaDevices.enumerateDevices()
//            .then(function(devices) {
//                devices.forEach(function(device) {
//                    alert(device.kind + ": " + device.label + " id = " + device.deviceId);
//                });
//            })
//            .catch(function(err) {
//               alert(err.name + ": " + err.message);
//            });
            $("#start-play").click(function(){
                alert("start");
                startPublish();
            });
    var sdk = null; // Global handler to do cleanup when republishing.
    var urlRtmp = `https://139.59.112.231:443/rtc/v1/whip/?app=live&stream=livestream&secret=a5cb70703ae942888e217fd9871dd8bf`;
//play             https://139.59.112.231:443/rtc/v1/whep/?app=live&stream=livestream&secret=a5cb70703ae942888e217fd9871dd8bf
    var startPublish = function() {
        $('#rtc_media_player').show();

        // Close PC when user replay.
        if (sdk) {
            sdk.close();
        }
        sdk = new SrsRtcWhipWhepAsync();

        // User should set the stream when publish is done, @see https://webrtc.org/getting-started/media-devices
        // However SRS SDK provides a consist API like https://webrtc.org/getting-started/remote-streams
        $('#rtc_media_player').prop('srcObject', sdk.stream);
        // Optional callback, SDK will add track to stream.
        // sdk.ontrack = function (event) { console.log('Got track', event); sdk.stream.addTrack(event.track); };

        // https://developer.mozilla.org/en-US/docs/Web/Media/Formats/WebRTC_codecs#getting_the_supported_codecs
        sdk.pc.onicegatheringstatechange = function (event) {
            if (sdk.pc.iceGatheringState === "complete") {
                $('#acodecs').html(SrsRtcFormatSenders(sdk.pc.getSenders(), "audio"));
                $('#vcodecs').html(SrsRtcFormatSenders(sdk.pc.getSenders(), "video"));
            }
        };

        // For example: webrtc://r.ossrs.net/live/livestream
//        var url = $("#txt_url").val();
        sdk.publish(urlRtmp).then(function(session){
            $('#sessionid').html(session.sessionid);
            $('#simulator-drop').attr('href', session.simulator + '?drop=1&username=' + session.sessionid);
        }).catch(function (reason) {
            // Throw by sdk.
            if (reason instanceof SrsError) {
                if (reason.name === 'HttpsRequiredError') {
                    alert(`WebRTC localhost：${reason.name} ${reason.message}`);
                } else {
                    alert(`${reason.name} ${reason.message}`);
                }
            }
            // See https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia#exceptions
            if (reason instanceof DOMException) {
                if (reason.name === 'NotFoundError') {
                    alert(`getUserMedia ${reason.name} ${reason.message}`);
                } else if (reason.name === 'NotAllowedError') {
                    alert(`getUserMedia ${reason.name} ${reason.message}`);
                } else if (['AbortError', 'NotAllowedError', 'NotFoundError', 'NotReadableError', 'OverconstrainedError', 'SecurityError', 'TypeError'].includes(reason.name)) {
                    alert(`getUserMedia ${reason.name} ${reason.message}`);
                }
            }

            sdk.close();
//            $('#rtc_media_player').hide();
            console.error(reason);
            alert(reason);
        });
    };
    
        $('.div_scroll_50').slimScroll({
            height: '100vh',
            position: 'right',
            size: "5px",
            color: '#98a6ad',
            wheelStep: 30
        });

        function dialogCookie(id) {
            $("#cookie_content").val("");
            $("#profile_cookie_id").val(id);
            $('#dialog_cookie').modal({
                backdrop: false
            });
        }

        $(".btn-save-cookie").click(function(e) {
            e.preventDefault();
            var form = $("#form-cookie").serialize();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            console.log(form);
            $.ajax({
                type: "POST",
                url: "/tiktok/cookie/add",
                data: form,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        function getViolation(id) {
            $(".content-violation").html("");
            $('#dialog_violation').modal({
                backdrop: false
            });
            $("#dialog_violation_loading").show();
            $.ajax({
                type: "GET",
                url: "/tiktok/violation/list",
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    $("#dialog_violation_loading").hide();
                    if (data.length == 0) {
                        var html = 'Không có vi phạm';

                    } else {
                        var i = 1;
                        var html = `<div class="table-responsive">
                                    <table id="table-tiktok-account" class="table hover-button-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center w-20">Loại</th>
                                                <th class="text-center">Thời Gian</th>
                                                <th class="text-left">Lý do</th>
                                            </tr>
                                        </thead>`;
                        $.each(data, function(k, v) {
                            html += `<tr><td>${i++}</td>
                            <td>${v.violation_info.violation_type}</td>
                            <td>${new Date(v.violation_time *1000).toLocaleString()}</td>
                            <td>${v.violation_info.violation_reason}</td></tr>`;
                        });
                        html += `</table></div>`;
                    }
                    $(".content-violation").html(html);

                },
                error: function(data) {

                }
            });
        }

        function viewViolationsLog(id) {
            $('#dialog_log').modal({
                backdrop: false
            });
            $("#dialog_log_loading").show();
            $.ajax({
                type: "GET",
                url: "/live/" + id,
                data: {},
                dataType: 'json',
                success: function(data) {
                    $("#dialog_log_loading").hide();
                    if (data.violations == null) {
                        data.violations = "Không có dữ liệu";
                    }
                    $("#log-content").val(data.violations);
                    h = calcHeight(data.violations);
                    h = h + 45;
                    $("#log-content").css({
                        "height": h + "px"
                    });
                },
                error: function(data) {

                }
            });
        }
        $(".btn-quick-restart").click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var id = btn.attr("data-id");
            var loadingText = '<i class="ion-load-c fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                btn.data('original-text', $(this).html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/quick/restart",
                data: {
                    "id": id,
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    btn.html(btn.data('original-text'));
                    if (data.status == 'success') {
                        btn.hide();
                    }
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.reload == 1) {
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function(data) {
                    btn.html(btn.data('original-text'));
                    console.log(data);
                }
            });
        });
        $(".btn-restart-live").click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var id = btn.attr("data-id");
            var loadingText = '<i class="ion-load-c fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                btn.data('original-text', $(this).html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/kill/lid",
                data: {
                    "id": id,
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    btn.html(btn.data('original-text'));
                    if (data.status == 'success') {
                        btn.hide();
                    }
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.reload == 1) {
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function(data) {
                    btn.html(btn.data('original-text'));
                    console.log(data);
                }
            });
        });

        $(".btn-report-bug").click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var id = btn.attr("data-id");
            var loadingText = '<i class="ion-load-c fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                btn.data('original-text', $(this).html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/bug",
                data: {
                    "id": id,
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {

                    if (data.status == 'success') {
                        btn.hide();
                    }
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.reload == 1) {
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function(data) {
                    //                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        });
        //2023/03/17 chức năng thêm sản phầm vào luồng live
        function viewProduct(id) {
            $('#dialog_product').modal({
                backdrop: false
            });
            $("#live_id").val(id);
            $("#result-add").html("");
            $("#dialog_product_title").html("Sản phẩm của luồng live " + id)
            getProduct(id);
        }

        function getProduct(id) {
            $("#product_data").html('');
            $("#dialog_product_loading").show();
            $.ajax({
                type: "GET",
                url: "/tiktok/product/list",
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(data) {
                    var arrProduct = [];
                    $("#dialog_product_loading").hide();
                    //                    console.log(data);
                    var html = '';
                    $.each(data.products, function(k, v) {
                        arrProduct.push(v.product_id);
                        html += ` <div class="col-md-4 webdesign illustrator ">
                                    <div class="gal-detail thumb m-t-10 pos">
                                        <span id="pin-${v.product_id}" class="cur-point float-left" data-toggle="tooltip" data-placement="top" title="Pin sản phẩm" onclick="pinProduct(${data.id},'${v.product_id}')"><i class="ti-pin-alt"></i></span>
                                        <span id="pro-${v.product_id}" class="cur-point float-right" data-toggle="tooltip" data-placement="top" title="Xóa" onclick="delProduct(${data.id},'${v.product_id}')"><i class="fa fa-times-circle cur-point"></i></span>
                                        <a target="_blank" href="https://shop.tiktok.com/view/product/${v.product_id}?region=VN&local=en" class="image-popup">
                                            <img src="${v.cover.url_list[0]}" class="thumb-img" alt="work-thumbnail">
                                        </a>
                                        <h4 class="text-center">${v.format_available_price}</h4>
                                        <div class="ga-border"></div>
                                        <p class="text-muted text-center"><small>${v.title}</small></p>
                                    </div>
                                </div>`;
                    });
                    $("#product_link").text(data.productsText);
                    $("#job_id").val(id);
                    $("#product_ids").val(JSON.stringify(arrProduct));
                    $("#product_data").html(html);
                    $('[data-toggle="tooltip"]').tooltip();
                },
                error: function(data) {

                }
            });
        }

        function addProduct() {
            $("#result-add").html("");
            var form = $("#frmProduct").serialize();
            var button = $("#save-product");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý...';
            if (button.html() !== loadingText) {
                button.data('original-text', button.html());
                button.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/tiktok/product/add",
                data: form,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    button.html(button.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    var html = `<div class="alert-violet"><ul>`;
                    $.each(data.result, function(k, v) {
                        html += `<li>${v.link} -> ${v.status}</li>`;
                    });
                    html += `</ul></div>`;
                    $("#result-add").html(html);
                    getProduct($("#live_id").val());
                },
                error: function(data) {
                    button.html(button.data('original-text'));
                }
            });
        }

        function pinProduct(id, productId) {
            var button = $("#pin-" + productId);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if (button.html() !== loadingText) {
                button.data('original-text', button.html());
                button.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/tiktok/product/pin",
                data: {
                    "id": id,
                    "product_id": productId
                },
                dataType: 'json',
                success: function(data) {
                    button.html(button.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    console.log(data);
                    //                    getProduct(id);

                },
                error: function(data) {

                }
            });
        }

        function delProduct(id, productId) {
            var button = $("#pro-" + productId);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if (button.html() !== loadingText) {
                button.data('original-text', button.html());
                button.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/tiktok/product/delete",
                data: {
                    "id": id,
                    "product_id": productId
                },
                dataType: 'json',
                success: function(data) {
                    button.html(button.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    console.log(data);
                    getProduct(id);

                },
                error: function(data) {

                }
            });
        }

        function configPin() {

            $('#dialog_setting_pin').modal({
                backdrop: false
            });
        }

        function savePinSetting() {
            var form = $("#formAddPinSetting").serialize();
            var button = $("#save-setting");
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý...';
            if (button.html() !== loadingText) {
                button.data('original-text', button.html());
                button.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/tiktok/product/pin/setting",
                data: form,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    button.html(button.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    var html = `<div class="alert-violet"><ul>`;
                    $.each(data.result, function(k, v) {
                        html += `<li>${v.link} -> ${v.status}</li>`;
                    });
                    html += `</ul></div>`;
                    $("#result-add").html(html);
                    getProduct($("#live_id").val());
                },
                error: function(data) {
                    button.html(button.data('original-text'));
                }
            });
        }

        @if ($isAdmin)

            function viewActionLog(id) {
                $('#dialog_log').modal({
                    backdrop: false
                });
                $("#dialog_log_loading").show();
                $.ajax({
                    type: "GET",
                    url: "/live/" + id,
                    data: {},
                    dataType: 'json',
                    success: function(data) {
                        $("#dialog_log_loading").hide();
                        if (data.action_log == null) {
                            data.action_log = "Không có dữ liệu";
                        }
                        $("#log-content").val(data.action_log);
                        h = calcHeight(data.action_log);
                        $("#log-content").css({
                            "height": h + "px"
                        });
                    },
                    error: function(data) {

                    }
                });
            }
        @endif
        function clearTiktokForm() {
            $("#tiktok_name").val("");
            $("#region").val("vn");
            $("#chk_proxy").prop("checked", false).change();
            $("#chk_proxy_pass").prop("checked", false).change();
            $("#proxy_ip").val("");
            $("#proxy_port").val("");
            $("#proxy_user").val("");
            $("#proxy_pass").val("");
        }
        $(".btn-add-tiktok").click(function(e) {
            e.preventDefault();
            clearTiktokForm();
            var type = $(this).attr("data-type");
            $("#dialog_type").val(type);
            if (type == 'web') {
                $(".div_add_web").show();
                $(".div_add_studio").hide();
            } else {
                $(".div_add_studio").show();
                $(".div_add_web").hide();
            }
            $('#dialog_tiktok').modal({
                backdrop: false
            });
        });

        $(".btn-save-tiktok").click(function(e) {
            e.preventDefault();
            var form = $("#frmSaveTiktok").serialize();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Đang xử lý...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var dialogType = $("#dialog_type").val();
            $.ajax({
                type: "POST",
                url: "/tiktok",
                data: form,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.status == 'success') {
                        var html = `
                            <tr><td class="text-center color-red"><b>${data.data.id}</b></td>
                            <td class="text-left">${data.data.tiktok_name}</td>
                            <td class="text-center">${data.data.region}</td>
                            <td class="text-center ur-status"><span class="badge badge-warning">Mới</span></td>
                            <td class="text-center ur-status"><span class="badge badge-warning">Chưa live</span></td>
                            <td class="text-center"></th>
                            <td class="text-center">0</td>
                            <td class="text-right">`;
                        if (dialogType == 'studio') {
                            html +=
                                `<button class="btn btn-circle btn-dark btn-sm waves-effect waves-light div_add_studio" onclick="loginTiktok(${data.data.id})"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Studio Đăng nhập<br> Đăng nhập tài khoản trên Tiktok LIVE Studio"><i class="fa fa-user cur-point"></i></button>
                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light div_add_studio" onclick="commitTiktok(${data.data.id})"
                                            data-toggle="tooltip" data-placement="top" data-html='true'
                                            title="Studio Commit<br>Hãy chắc chắn bạn đã đăng nhập vào Tiktok LIVE Studio"><i class="fa fa-upload cur-point"></i></button>`;
                        } else {
                            html +=
                                `<button class="btn btn-circle btn-dark btn-sm waves-effect waves-light div_add_web" onclick="dialogCookie(${data.data.id})"
                                            style="border-radius:5px;width: 130px" data-type="web" data-toggle="tooltip" data-placement="top" data-html='true'
                                            title="Thêm tài khoản Tiktok sử dụng cookie trên Website"><i class="ion-plus cur-point"></i> Thêm tài khoản</button>`;
                        }

                        html +=
                            `<button id="tik-${data.data.id}" class="btn btn-circle btn-dark btn-sm waves-effect waves-light " onclick="deleteTiktok(${data.data.id})"
                                            data-id="${data.data.id}" data-toggle="tooltip" data-placement="top"
                                            title="Xóa"><i class="fa fa-times-circle cur-point"></i></button></td></tr>`;
                        $('#table-tiktok-account tr:last').after(html);
                        $('[data-toggle="tooltip"]').tooltip()
                    }
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        function addNewTiktok() {
            $('#dialog_tiktok').modal({
                backdrop: false
            });
        }

        function refreshTiktok() {

        }

        function loginTiktok(id) {
            $.ajax({
                type: "GET",
                url: `/tiktok/${id}`,
                data: {},
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        window.open(
                            `tiktoksync://login/?name=${data.data.tiktok_name}&user=${data.data.username}`,
                            "_blank");
                    } else {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    }
                },
                error: function(data) {

                }
            });
        }

        function commitTiktok(id) {
            $.ajax({
                type: "GET",
                url: `/tiktok/${id}`,
                data: {},
                dataType: 'json',
                success: function(data) {
                    if (data.status == 'success') {
                        console.log(
                            `tiktoksync://commit/?name=${data.data.tiktok_name}&user=${data.data.username}`);
                        window.open(
                            `tiktoksync://commit/?name=${data.data.tiktok_name}&user=${data.data.username}`,
                            "_blank");
                        setTimeout(function() {
                            location.reload();
                        }, 6000);
                    } else {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    }
                },
                error: function(data) {

                }
            });
        }

        function deleteTiktok(id) {
            var btn = $("#tik-" + id);
            var loadingText = '<i class="ion-load-c fa-spin"></i>';
            if (btn.html() !== loadingText) {
                btn.data('original-text', btn.html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "PUT",
                url: "/tiktok",
                data: {
                    "id": id,
                    "action":"delete",
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    btn.html(btn.data('original-text'));
                    if (data.status == "success") {
                        btn.closest("tr").hide();
                    }

                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                },
                error: function(data) {
                    //                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        }

        function regTiktokV3(id) {
            var btn = $("#reg-v3-" + id);
            var loadingText = '<i class="ion-load-c fa-spin"></i> Đang chạy...';
            if (btn.html() !== loadingText) {
                btn.data('original-text', btn.html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/tiktok/v3/req",
                data: {
                    "id": id,
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    btn.html(btn.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if(data.status=="success"){
                        btn.hide();
                    }
                },
                error: function(data) {
                    //                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        }
        function renewDevice(id) {
            var btn = $("#renew-" + id);
            var loadingText = '<i class="ion-load-c fa-spin"></i> Đang đổi...';
            if (btn.html() !== loadingText) {
                btn.data('original-text', btn.html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/tiktok/device/renew",
                data: {
                    "id": id,
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    btn.html(btn.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                },
                error: function(data) {
                    //                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        }

        function renewIp(id) {
            var btn = $("#ip-" + id);
            var loadingText = '<i class="ion-load-c fa-spin"></i> Đang đổi...';
            if (btn.html() !== loadingText) {
                btn.data('original-text', btn.html());
                btn.html(loadingText);
            }
            $(`.ip-${id}`).html('<div style="text-align: -webkit-center;"><div class="dot-carousel"></div></div>');
            $.ajax({
                type: "POST",
                url: "/tiktok/ip/renew",
                data: {
                    "id": id,
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    btn.html(btn.data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.hasOwnProperty('profile')) {
                        $(`.ip-${id}`).html(data.profile.ip);
                    } else {
                        $(`.ip-${id}`).html("");
                    }

                },
                error: function(data) {
                    //                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        }

        function validateSource() {
            var data = $("#url_source").val();
            var form = $("#formLive").serialize();
            $(".load-check-url").show();
            $("#result_check").fadeOut('fast');
            $("#url_source_check").val("");
            $.ajax({
                type: "POST",
                url: "/live/validate",
                data: form,
                dataType: 'json',
                success: function(data) {
                    //                console.log(data);
                    var text = "";
                    $.each(data, function(key, val) {
                        text += val.message + (key == (data.length - 1) ? "" : "\n");
                    });
                    $("#url_source_check").val(text);
                    $(".load-check-url").hide();
                    if (data.length > 0) {
                        $("#result_check").fadeIn("slow");
                    }
                    $('#url_source_check').css("height", calcHeight($("#url_source_check").val()));
                },
                error: function(data) {
                    $(".load-check-url").hide();
                }
            });
        }

        function requestTest() {
            $.ajax({
                type: "GET",
                url: "/requestTest",
                data: {
                    "platform": "tiktok"
                },
                dataType: 'json',
                success: function(data) {
                    $('#rq-test').hide();
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                },
                error: function(data) {
                    $('#rq-test').hide();
                }
            });
        }
        var ids = [];
        var speeds = [];
        <?php
        foreach ($datas as $data) {
            //            echo "speeds.push($data->times);";
            echo "drawLineChartMini('speed$data->id','$data->id',$data->times,$data->speeds);";
        }
        ?>

        var countTotal = <?php echo count($datas); ?>;
        var countRun = 0;


        function checkStatusLive(btn, badge, id) {
            var intval = setInterval(function() {
                $.ajax({
                    type: "GET",
                    url: "/live/" + id,
                    data: {},
                    dataType: 'json',
                    success: function(data) {
                        //                    console.log(intval, data);
                        if (data.status === 'error') {
                            clearInterval(intval);
                        } else if (data.status == 2 || data.status == 0 || data.status == 5) {
                            clearInterval(intval);
                            var html = '';
                            if (data.status == 2) {
                                btn.removeClass("disp-none");
                                btn.html('<i class="fa fa-stop cur-point"></i>');
                                btn.attr("data-status", "3");
                                btn.attr("data-original-title", "Dừng live");
                                html =
                                    '<span class="badge badge-violet animated zoomInRight">Đang live</span>';
                            } else if (data.status == 0) {
                                btn.removeClass("disp-none");
                                html =
                                    '<span class="badge badge-success animated zoomInRight">Mới</span>';
                                btn.attr("data-status", "1");
                                btn.attr("data-original-title", "Bắt đầu live");
                                btn.html('<i class="fa fa-play cur-point"></i>');
                            } else if (data.status == 5) {
                                btn.removeClass("disp-none");
                                html =
                                    '<span class="badge badge-danger animated zoomInRight">Đã dừng</span>';
                            }
                            badge.prev().html(html);
                        } else if (data.status == 4) {
                            html =
                                '<span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang xử lý</span> ' +
                                data.estimate;
                            badge.prev().html(html);
                        }


                    },
                    error: function(data) {
                        clearInterval(intval);
                    }
                });
            }, 3000);
        }
        eventStatusLive();

        function eventStatusLive() {
            $(".btn-status-live").click(function(e) {
                e.preventDefault();
                var btn = $(this);
                var id = btn.attr("data-id");
                var status = btn.attr("data-status");
                var loadingText = '<i class="ion-load-c fa-spin"></i>';
                if ($(this).html() !== loadingText) {
                    btn.data('original-text', $(this).html());
                    btn.html(loadingText);
                }
                $.ajax({
                    type: "PUT",
                    url: "/live",
                    data: {
                        "id": id,
                        "status": status,
                        "_token": $("input[name=_token]").val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        btn.html(btn.data('original-text'));
                        //                    console.log(data);
                        var td = btn.closest("td");
                        var html = '';
                        if (status == -1 && data.status == 'success') {
                            btn.closest('tr').hide();
                            $("#count_total").html(--countTotal);
                        } else if (status == 1 && data.status == 'success') {
                            $("#count_run").html(++countRun);
                            btn.html('<i class="fa fa-stop cur-point"></i>');
                            btn.attr("data-status", "3");
                            btn.attr("data-original-title", "Dừng live");
                            btn.addClass("disp-none");
                            html =
                                '<span class="badge badge-warning zoomIn animated"><i class="ion-load-c fa-spin"></i> Đang đợi live</span>';
                            td.prev().html(html);
                            td.prev().prev().html("");
                            checkStatusLive(btn, td, id);
                        } else if (status == 3 && data.status == 'success') {
                            $("#count_run").html(--countRun);
                            btn.html('<i class="fa fa-play cur-point"></i>');
                            btn.attr("data-status", "1");
                            btn.attr("data-original-title", "Bắt đầu live");
                            btn.addClass("disp-none");
                            html =
                                '<span class="badge badge-warning zoomIn animated"><i class="ion-load-c fa-spin"></i> Đang đợi dừng</span>';
                            td.prev().html(html);
                            checkStatusLive(btn, td, id);
                        }

                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data
                            .message);
                        if (data.reload == 1) {
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        }
                    },
                    error: function(data) {
                        //                    btn.html($this.data('original-text'));
                        console.log(data);
                    }
                });
            });
        }

        $(".btn-edit-live").click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var id = btn.attr("data-id");
            var loadingText = '<i class="ion-load-c fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                btn.data('original-text', $(this).html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/live/" + id,
                data: {},
                dataType: 'json',
                success: function(data) {
                    //                console.log(data);
                    btn.html(btn.data('original-text'));
                    if (data.status == 'error') {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data
                            .message);
                    } else {
                        $([document.documentElement, document.body]).animate({
                            scrollTop: $("#edit_slideup").offset().top
                        }, 500);
                        $(".btn-live-type").removeClass("pricing-box-active");
                        $(".btn-live-type[value='" + data.command + "']").addClass(
                        "pricing-box-active");
                        if(data.command=='live_studio_v3'){
                            $("#live_studio_v3").show();
                        }else{
                            $("#live_studio_v3").hide();
                        }
                        $("#edit_id").val(data.id);
                        $("#title").val(data.note);
                        $("#url_source").val(data.url_source);
                        if (data.seq_source == 0) {
                            $("#radio_by").prop('checked', true);
                        } else {
                            $("#radio_random").prop('checked', true);
                        }
                        if (data.repeat == 0) {
                            $("#live_repeat").prop('checked', true).change();
                        } else {
                            $("#live_repeat").prop('checked', false).change();
                        }
                        if (data.end_alarm == 0) {
                            //                        $("#live_repeat").prop('checked', true);
                        } else {
                            //                        $("#live_repeat").prop('checked', false);
                            $("#chk_date_end").prop('checked', true).change();
                            $("#date_end").val(data.end_alarm_text);
                        }
                        if (data.start_alarm != 0) {
                            $("#radio_time").prop('checked', false).change();
                            $("#date_start").val(data.start_alarm_text);
                        } else {
                            $("#radio_time").prop('checked', true).change();
                        }
                        $("#topic").val(data.topic).change();
                        if (data.infinite_loop == 1) {
                            $("#infinite_loop").prop('checked', true);
                        } else {
                            $("#infinite_loop").prop('checked', false);
                        }
                    }

                },
                error: function(data) {
                    btn.html(btn.data('original-text'));
                }
            });
        });
        $(".btn-save-live").click(function(e) {
            e.preventDefault();
            var form = $("#formLive").serialize();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "POST",
                url: "/tiktokSaveLive",
                data: form,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    //                console.log(data);
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.status == 'success') {
                        location.reload();
                    }
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });
        $('#date_start').datetimepicker({
            //language:  'fr',
            weekStart: 1,
            todayBtn: 1,
            autoclose: true,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 0,
            format: 'mm/dd/yyyy hh:ii'
        });
        $('#date_end').datetimepicker({
            //language:  'fr',
            weekStart: 1,
            todayBtn: 1,
            autoclose: true,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 0,
            format: 'mm/dd/yyyy hh:ii'
        });
        $(".view-more").click(function() {
            var vm = $(this);
            var more = vm.prev(".more");
            var dot = more.prev(".dots");
            dot.css({
                "color": "#ccc"
            })
            //        console.log(dot.is(":hidden"));
            //        console.log(dot.is(":visible"));
            if (dot.css("display") === 'none') {
                dot.show();
                vm.html("Xem thêm");
                more.slideUp();
            } else {
                dot.hide();
                vm.html("Thu gọn");
                more.slideDown();
            }
        });
        calcHeight = function(value) {
            let numberOfLineBreaks = (value.match(/\n/g) || []).length;
            // min-height + lines x line-height + padding + border
            let newHeight = 20 + numberOfLineBreaks * 20 + 12 + 2 + 10;
            return newHeight;
        };
        let textarea = document.querySelector(".resize-ta");
        textarea.addEventListener("keyup", () => {
            textarea.style.height = calcHeight(textarea.value) + "px";
        });
        $("#minute_pin").TouchSpin({
            min: 2,
            max: 100,
            step: 1,
            decimals: 0,
            boostat: 5,
            maxboostedstep: 10,
            buttondown_class: "btn btn-dark",
            buttonup_class: "btn btn-dark",
            postfix: 'Phút'
        });

        $(".btn-live-type").click(function() {
            $(".btn-live-type").removeClass("pricing-box-active");
            $(this).addClass("pricing-box-active");
            $("#live_type").val($(this).val());

        });


// Biến toàn cục
let productSets = []; // Danh sách bộ sản phẩm đã lưu
let currentProductSet = null; // Bộ sản phẩm đang được chọn
let previewProducts = []; // Sản phẩm đang được xem trước

/**
 * Hiển thị modal quản lý bộ sản phẩm
 */
function showProductSetModal(liveId) {
    // Reset form
    $('#frmProductSet')[0].reset();
    $('#product_set_id').val(0);
    $('#product_links_preview').hide();
    $('#preview_content').empty();
    previewProducts = [];
    
    // Lưu live ID
    $('#live_id_set').val(liveId);
    
    // Tải danh sách bộ sản phẩm đã lưu
    loadProductSets();
    
    // Hiển thị modal
    $('#dialog_product_set').modal('show');
}

/**
 * Hiển thị modal cấu hình thời gian pin
 */
//function showPinConfigModal(profileId) {
//    // Thiết lập thông tin profile
//    $('#pin_config_profile_id').val(profileId);
//    
//    // Tải danh sách bộ sản phẩm cho dropdown
//    loadProductSetsForSelect();
//    
//    // Tải cấu hình pin hiện tại (nếu có)
//    loadCurrentPinConfig(profileId);
//    
//    // Mặc định chọn kiểu 1
//    $('input[name="pin_type"][value="interval"]').prop('checked', true);
//    togglePinConfigType('interval');
//    
//    // Hiển thị modal
//    $('#dialog_pin_config').modal('show');
//}
function showPinConfigModal(profileId) {
    // Thiết lập thông tin profile
    $('#pin_config_profile_id').val(profileId);
    
    // Tải danh sách bộ sản phẩm cho dropdown trước
    loadProductSetsForSelect(function() {
        // Sau khi tải danh sách bộ sản phẩm xong, tải cấu hình pin hiện tại
        loadCurrentPinConfig(profileId);
    });
    
    // Hiển thị modal
    $('#dialog_pin_config').modal('show');
}
function loadCurrentPinConfig(profileId) {
    $.ajax({
        url: '/tiktok/get-pin-config',
        type: 'GET',
        data: {
            profile_id: profileId
        },
        success: function(response) {
            // Mặc định là không pin sản phẩm
            $('#enable_auto_pin').prop('checked', false);
            $('.pin-config-container').hide();
            
            if (response.status === 'success' && response.pin_config) {
                const pinConfig = response.pin_config;
                
                // Thiết lập bộ sản phẩm - dropdown đã được tải trước đó
                if (pinConfig.product_set_id) {
                    $('#select_product_set').val(pinConfig.product_set_id);
                    $('#pin_config_set_id').val(pinConfig.product_set_id);
                }
                
                // Hiển thị option pin sản phẩm nếu có
                if (pinConfig.is_autopin) {
                    $('#enable_auto_pin').prop('checked', true);
                    $('.pin-config-container').show();
                }
                
                // Thiết lập kiểu pin
                if (pinConfig.pin_type) {
                    $(`input[name="pin_type"][value="${pinConfig.pin_type}"]`).prop('checked', true);
                    changeTabPinType(pinConfig.pin_type);
                } else {
                    // Mặc định chọn kiểu interval
                    $('input[name="pin_type"][value="interval"]').prop('checked', true);
                    changeTabPinType('interval');
                }
                
                // Thiết lập khoảng thời gian (nếu kiểu interval)
                if (pinConfig.pin_type === 'interval' && pinConfig.interval) {
                    // Chuyển đổi từ giây sang phút
                    const minutes = Math.ceil(pinConfig.interval / 60);
                    $('#pin_interval').val(minutes);
                }
                
                // Thiết lập thời gian pin cụ thể (nếu kiểu specific)
                if (pinConfig.pin_type === 'specific' && pinConfig.products) {
                    // Tải danh sách sản phẩm của bộ
                    if (pinConfig.product_set_id) {
                        loadProductSetForPinConfig(pinConfig.product_set_id, pinConfig.products);
                    }
                }
            } else {
                // Nếu không có cấu hình, mặc định chọn kiểu interval
                $('input[name="pin_type"][value="interval"]').prop('checked', true);
                changeTabPinType('interval');
            }
        }
    });
}


function changeTabPinType(type) {
    // Cập nhật radio button
    $(`input[name="pin_type"][value="${type}"]`).prop('checked', true);
    
    // Ẩn tất cả tab content
    $('.pin-type-tab-content').hide();
    
    // Bỏ active tất cả tab
    $('.pin-type-tab').removeClass('active');
    
    // Hiển thị tab được chọn
    $(`#${type}_config`).show();
    $(`.pin-type-tab[data-type="${type}"]`).addClass('active');
    
    // Tải danh sách sản phẩm nếu đã chọn bộ sản phẩm và đang ở tab specific
    if (type === 'specific') {
        const selectedSetId = $('#select_product_set').val();
        if (selectedSetId) {
            loadProductSetForPinConfig(selectedSetId);
        }
    }
}

function savePinConfig() {
    const profileId = $('#pin_config_profile_id').val();
    const productSetId = $('#select_product_set').val();
    const enableAutoPin = $('#enable_auto_pin').is(':checked');
    const pinType = $('input[name="pin_type"]:checked').val();
    
    if (!productSetId) {
        showNotification('error', 'Vui lòng chọn bộ sản phẩm');
        return;
    }
    
    // Tạo data để gửi lên server
    const formData = new FormData();
    formData.append('_token', $('input[name="_token"]').val());
    formData.append('profile_id', profileId);
    formData.append('product_set_id', productSetId);
    formData.append('pin_type', pinType);
    formData.append('is_autopin', enableAutoPin ? 1 : 0);
    
    // Nếu bật tự động pin, thêm các thông tin cấu hình pin
    if (enableAutoPin) {
        if (pinType === 'interval') {
            // Cấu hình kiểu 1: Khoảng thời gian
            const minutes = $('#pin_interval').val();
            
            if (!minutes || isNaN(minutes) || parseInt(minutes) < 1) {
                showNotification('error', 'Vui lòng nhập khoảng thời gian hợp lệ (tối thiểu 1 phút)');
                return;
            }
            
            // Chuyển đổi từ phút sang giây khi gửi lên server
            const seconds = parseInt(minutes) * 60;
            formData.append('interval', seconds);
        } else {
            // Cấu hình kiểu 2: Thời điểm cụ thể
            const products = [];
            let hasValidTimes = false;
            
            $('#pin_time_items tr').each(function() {
                const productId = $(this).data('product-id');
                const pinTime = $(this).find('.pin-time').val();
                
                // Chỉ thêm sản phẩm có thời gian hợp lệ
                if (pinTime && !isNaN(pinTime) && parseInt(pinTime) >= 0) {
                    products.push({
                        product_id: productId,
                        pin_time: parseInt(pinTime)
                    });
                    hasValidTimes = true;
                }
            });
            
            if (!hasValidTimes) {
                showNotification('error', 'Vui lòng nhập ít nhất một thời gian pin hợp lệ');
                return;
            }
            
            // Sắp xếp sản phẩm theo thời gian pin tăng dần
            products.sort((a, b) => a.pin_time - b.pin_time);
            
            formData.append('products', JSON.stringify(products));
        }
    }
    
    // Gửi request để lưu cấu hình
    $.ajax({
        url: '/tiktok/save-pin-config',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#btn_save_pin_config').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');
        },
        success: function(response) {
            $('#btn_save_pin_config').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu cấu hình');
            
            if (response.status === 'success') {
                showNotification('success', 'Đã lưu cấu hình pin sản phẩm thành công');
                
                // Đóng modal
                $('#dialog_pin_config').modal('hide');
            } else {
                showNotification('error', response.message || 'Có lỗi xảy ra khi lưu cấu hình');
            }
        },
        error: function() {
            $('#btn_save_pin_config').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu cấu hình');
            showNotification('error', 'Đã xảy ra lỗi khi lưu cấu hình');
        }
    });
}

//function savePinConfig() {
//    const profileId = $('#pin_config_profile_id').val();
//    const productSetId = $('#select_product_set').val();
//    const enableAutoPin = $('#enable_auto_pin').is(':checked');
//    const pinType = $('input[name="pin_type"]:checked').val();
//    
//    if (!productSetId) {
//        showNotification('error', 'Vui lòng chọn bộ sản phẩm');
//        return;
//    }
//    
//    // Tạo data để gửi lên server
//    const formData = new FormData();
//    formData.append('_token', $('input[name="_token"]').val());
//    formData.append('profile_id', profileId);
//    formData.append('product_set_id', productSetId);
//    formData.append('pin_type', pinType);
//    formData.append('is_autopin', enableAutoPin ? 1 : 0);
//    
//    // Nếu bật tự động pin, thêm các thông tin cấu hình pin
//    if (enableAutoPin) {
//        if (pinType === 'interval') {
//            // Cấu hình kiểu 1: Khoảng thời gian
//            const minutes = $('#pin_interval').val();
//            
//            if (!minutes || isNaN(minutes) || parseInt(minutes) < 1) {
//                showNotification('error', 'Vui lòng nhập khoảng thời gian hợp lệ (tối thiểu 1 phút)');
//                return;
//            }
//            
//            // Chuyển đổi từ phút sang giây khi gửi lên server
//            const seconds = parseInt(minutes) * 60;
//            formData.append('interval', seconds);
//        } else {
//            // Cấu hình kiểu 2: Thời điểm cụ thể
//            const products = [];
//            let hasValidTimes = false;
//            
//            $('#pin_time_items tr').each(function() {
//                const productId = $(this).data('product-id');
//                const pinTime = $(this).find('.pin-time').val();
//                
//                // Chỉ thêm sản phẩm có thời gian hợp lệ
//                if (pinTime && !isNaN(pinTime) && parseInt(pinTime) >= 0) {
//                    products.push({
//                        product_id: productId,
//                        pin_time: parseInt(pinTime)
//                    });
//                    hasValidTimes = true;
//                }
//            });
//            
//            if (!hasValidTimes) {
//                showNotification('error', 'Vui lòng nhập ít nhất một thời gian pin hợp lệ');
//                return;
//            }
//            
//            // Sắp xếp sản phẩm theo thời gian pin tăng dần
//            products.sort((a, b) => a.pin_time - b.pin_time);
//            
//            formData.append('products', JSON.stringify(products));
//        }
//    }
//    
//    // Gửi request để lưu cấu hình
//    $.ajax({
//        url: '/tiktok/save-pin-config',
//        type: 'POST',
//        data: formData,
//        processData: false,
//        contentType: false,
//        beforeSend: function() {
//            $('#btn_save_pin_config').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');
//        },
//        success: function(response) {
//            $('#btn_save_pin_config').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu cấu hình');
//            
//            if (response.status === 'success') {
//                showNotification('success', 'Đã lưu cấu hình pin sản phẩm thành công');
//                
//                // Đóng modal
//                $('#dialog_pin_config').modal('hide');
//            } else {
//                showNotification('error', response.message || 'Có lỗi xảy ra khi lưu cấu hình');
//            }
//        },
//        error: function() {
//            $('#btn_save_pin_config').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu cấu hình');
//            showNotification('error', 'Đã xảy ra lỗi khi lưu cấu hình');
//        }
//    });
//}

/**
 * Tải danh sách bộ sản phẩm đã lưu
 */
function loadProductSets() {
    $('#saved_sets_loading').show();
    
    $.ajax({
        url: '/tiktok/get-product-sets',
        type: 'GET',
        data: {
            _token: $('input[name="_token"]').val()
        },
        success: function(response) {
            $('#saved_sets_loading').hide();
            
            if (response.status === 'success' && response.productSets) {
                productSets = response.productSets;
                renderProductSets();
            } else {
                $('#saved_product_sets').html('<div class="col-12 text-center">Không có bộ sản phẩm nào đã lưu</div>');
            }
        },
        error: function() {
            $('#saved_sets_loading').hide();
            showNotification('error', 'Đã xảy ra lỗi khi tải bộ sản phẩm');
        }
    });
}

/**
 * Hiển thị danh sách bộ sản phẩm đã lưu
 */
function renderProductSets() {
    if (productSets.length === 0) {
        $('#saved_product_sets').html('<div class="col-12 text-center">Không có bộ sản phẩm nào đã lưu</div>');
        return;
    }
    
    let html = '';
    productSets.forEach(function(set, index) {
        let thumbnails = '';
        let moreCount = 0;
        
        // Tạo thumbnail từ 4 sản phẩm đầu tiên
        if (set.products && set.products.length > 0) {
            set.products.slice(0, 4).forEach(function(product) {
                if (product.image) {
                    thumbnails += `<div class="thumbnail-item">
                        <img src="${product.image}" alt="${product.name}">
                    </div>`;
                }
            });
            
            // Hiển thị số sản phẩm còn lại
            if (set.products.length > 4) {
                moreCount = set.products.length - 4;
                thumbnails += `<div class="more-items">+${moreCount}</div>`;
            }
        }
        
        html += `
        <div class="col-md-6">
            <div class="product-set-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        ${set.name} <span class="product-count">${set.products ? set.products.length : 0} sản phẩm</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-info btn-circle" onclick="editProductSet(${set.id})">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-circle" onclick="deleteProductSet(${set.id})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="product-set-thumbnail">
                        ${thumbnails}
                    </div>
                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-sm btn-dark" onclick="applyProductSet(${set.id})">
                            <i class="fa fa-check"></i> Áp dụng cho livestream
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
    });
    
    $('#saved_product_sets').html(html);
}

/**
 * Tải danh sách bộ sản phẩm cho dropdown
 */
function loadProductSetsForSelect(callback) {
    $.ajax({
        url: '/tiktok/get-product-sets',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success' && response.productSets) {
                let options = '<option value="">-- Chọn bộ sản phẩm --</option>';
                
                response.productSets.forEach(function(set) {
                    const productCount = set.products ? set.products.length : 0;
                    options += `<option value="${set.id}">${set.name} (${productCount} sản phẩm)</option>`;
                });
                
                $('#select_product_set').html(options);
                
                // Gọi callback nếu được cung cấp
                if (typeof callback === 'function') {
                    callback();
                }
            }
        }
    });
}

/**
 * Điền danh sách bộ sản phẩm vào dropdown
 */
function populateProductSetDropdown() {
    let options = '<option value="">-- Chọn bộ sản phẩm --</option>';
    
    productSets.forEach(function(set) {
        options += `<option value="${set.id}">${set.name} (${set.products ? set.products.length : 0} sản phẩm)</option>`;
    });
    
    $('#select_product_set').html(options);
}

/**
 * Xem trước danh sách sản phẩm từ link
 */
function previewProductLinks() {
    const links = $('#product_links').val().trim();
    
    if (!links) {
        showNotification('error', 'Vui lòng nhập ít nhất một link sản phẩm');
        return;
    }
    
    const linkArray = links.split('\n').filter(link => link.trim() !== '');
    
    if (linkArray.length === 0) {
        showNotification('error', 'Vui lòng nhập ít nhất một link sản phẩm hợp lệ');
        return;
    }
    
    // Lấy profile ID nếu có
    const profileId = $('#tiktok_profile_id').val() || '';
    
    // Hiển thị loading
    $('#preview_content').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Đang tải thông tin sản phẩm...</div>');
    $('#product_links_preview').show();
    
    // Gửi request để lấy thông tin sản phẩm
    $.ajax({
        url: '/tiktok/get-product-info',
        type: 'POST',
        data: {
            _token: $('input[name="_token"]').val(),
            product_links: links,
            profile_id: profileId
        },
        success: function(response) {
            // Nếu đang xử lý bất đồng bộ
            if (response.status === 'processing') {
                // Hiển thị tiến độ
                showProgressBar(response.processed, response.total);
                
                // Hiển thị sản phẩm đã xử lý
                if (response.products && response.products.length > 0) {
                    previewProducts = response.products;
                    renderProductPreview(previewProducts);
                }
                
                // Tiếp tục kiểm tra tiến độ
                pollBatchProgress(response.batch_id);
            } else if (response.status === 'success' && response.products) {
                previewProducts = response.products;
                renderProductPreview(previewProducts);
                hideProgressBar();
            } else {
                $('#preview_content').html('<div class="alert alert-danger">' + (response.message || 'Không thể lấy thông tin sản phẩm. Vui lòng kiểm tra lại các link.') + '</div>');
                hideProgressBar();
            }
        },
        error: function() {
            $('#preview_content').html('<div class="alert alert-danger">Đã xảy ra lỗi khi lấy thông tin sản phẩm.</div>');
            hideProgressBar();
        }
    });
}

function showProgressBar(processed, total) {
    // Tính phần trăm tiến độ
    const percentage = Math.round((processed / total) * 100);
    
    // Tạo hoặc cập nhật thanh tiến độ
    if ($('#product_progress_container').length === 0) {
        // Tạo mới nếu chưa có
        const progressHtml = `
        <div id="product_progress_container" class="mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span>Đang tải thông tin sản phẩm...</span>
                <span id="progress_text">${processed}/${total} (${percentage}%)</span>
            </div>
            <div class="progress">
                <div id="product_progress_bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                    role="progressbar" style="width: ${percentage}%;" 
                    aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>`;
        
        // Thêm vào trước nội dung preview
        $('#preview_content').before(progressHtml);
    } else {
        // Cập nhật nếu đã có
        $('#progress_text').text(`${processed}/${total} (${percentage}%)`);
        $('#product_progress_bar').css('width', `${percentage}%`).attr('aria-valuenow', percentage);
    }
}

/**
 * Ẩn thanh tiến độ
 */
function hideProgressBar() {
    $('#product_progress_container').remove();
}

/**
 * Kiểm tra tiến độ xử lý batch
 */
function pollBatchProgress(batchId) {
    // Chờ một khoảng thời gian ngắn trước khi kiểm tra
    setTimeout(function() {
        $.ajax({
            url: '/tiktok/check-product-batch-progress',
            type: 'GET',
            data: {
                _token: $('input[name="_token"]').val(),
                batch_id: batchId
            },
            success: function(response) {
                if (response.status === 'processing') {
                    // Cập nhật tiến độ
                    showProgressBar(response.processed, response.total);
                    
                    // Cập nhật danh sách sản phẩm
                    if (response.products && response.products.length > 0) {
                        previewProducts = response.products;
                        renderProductPreview(previewProducts);
                    }
                    
                    // Tiếp tục kiểm tra
                    pollBatchProgress(batchId);
                } else if (response.status === 'success') {
                    // Hoàn thành
                    previewProducts = response.products;
                    renderProductPreview(previewProducts);
                    hideProgressBar();
                    showNotification('success', 'Đã tải xong thông tin sản phẩm');
                } else {
                    // Lỗi
                    $('#preview_content').html('<div class="alert alert-danger">' + (response.message || 'Không thể lấy thông tin sản phẩm.') + '</div>');
                    hideProgressBar();
                }
            },
            error: function() {
                $('#preview_content').html('<div class="alert alert-danger">Đã xảy ra lỗi khi kiểm tra tiến độ.</div>');
                hideProgressBar();
            }
        });
    }, 1000); // Chờ 1 giây trước khi kiểm tra
}

function renderProductPreview(products) {
    if (products.length === 0) {
        $('#preview_content').html('<div class="alert alert-warning">Không có sản phẩm nào được tìm thấy.</div>');
        return;
    }
    
    let html = '';
    products.forEach(function(product, index) {
        const productImage = product.image || '/images/no-image.png';
        const productPrice = (product.price);
        const commission = product.commission ? `<div class="preview-commission text-success">Hoa hồng: ${product.commission}</div>` : '';
        const storeInfo = product.store_name ? `<div class="preview-store text-muted">Cửa hàng: ${product.store_name}</div>` : '';
        const stockInfo = product.stock_num ? `<div class="preview-stock text-muted">Sẵn có: ${product.stock_num}</div>` : '';
        
        html += `
        <div class="product-preview-item">
            <div class="preview-image">
                <img src="${productImage}" alt="${product.name || 'Sản phẩm'}">
            </div>
            <div class="preview-details">
                <div class="preview-title">Giá: ${product.name || 'Giá'}</div>
                <div class="preview-price">${productPrice}</div>
                ${commission}
                ${storeInfo}
                ${stockInfo}
                <div class="preview-id">ID: ${product.product_id}</div>
            </div>
        </div>`;
    });
    
    $('#preview_content').html(html);
}

function createProductSet() {
    const name = $('#product_set_name').val().trim();
    const setId = parseInt($('#product_set_id').val()) || 0;
    
    if (!name) {
        showNotification('error', 'Vui lòng nhập tên bộ sản phẩm');
        return;
    }
    
    if (previewProducts.length === 0) {
        showNotification('error', 'Vui lòng xem trước và xác nhận danh sách sản phẩm');
        return;
    }
    
    // Tạo đối tượng bộ sản phẩm
    const productSet = {
        id: setId > 0 ? setId : null,
        name: name,
        products: previewProducts
    };
    
    // Gửi request để lưu bộ sản phẩm
    $.ajax({
        url: '/tiktok/save-product-set',
        type: 'POST',
        data: {
            _token: $('input[name="_token"]').val(),
            product_set: JSON.stringify(productSet)
        },
        beforeSend: function() {
            $('#btn_create_product_set').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');
        },
        success: function(response) {
            $('#btn_create_product_set').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu bộ sản phẩm');
            
            if (response.status === 'success') {
                showNotification('success', 'Đã lưu bộ sản phẩm thành công');
                
                // Reset form
                $('#frmProductSet')[0].reset();
                $('#product_set_id').val(0);
                $('#product_links_preview').hide();
                $('#preview_content').empty();
                previewProducts = [];
                
                // Chuyển sang tab bộ sản phẩm đã lưu
                $('#productSetTabs a[href="#saved-content"]').tab('show');
                
                // Tải lại danh sách bộ sản phẩm
                loadProductSets();
            } else {
                showNotification('error', response.message || 'Có lỗi xảy ra khi lưu bộ sản phẩm');
            }
        },
        error: function() {
            $('#btn_create_product_set').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu bộ sản phẩm');
            showNotification('error', 'Đã xảy ra lỗi khi lưu bộ sản phẩm');
        }
    });
}

/**
 * Chỉnh sửa bộ sản phẩm
 */
function editProductSet(id) {
    const productSet = productSets.find(set => set.id === id);
    
    if (!productSet) {
        showNotification('error', 'Không tìm thấy thông tin bộ sản phẩm');
        return;
    }
    
    // Điền thông tin vào form
    $('#product_set_name').val(productSet.name);
    $('#product_set_id').val(productSet.id);

    // Tạo danh sách link để điền vào textbox
    let links = '';
    if (productSet.products && productSet.products.length > 0) {
        productSet.products.forEach(function(product) {
            if (product.original_link) {
                links += product.original_link + '\n';
            }
        });
    }
    
    // Điền links vào textbox
    $('#product_links').val(links);
        
    // Hiển thị danh sách sản phẩm
    previewProducts = productSet.products;
    renderProductPreview(previewProducts);
    $('#product_links_preview').show();
    
    // Chuyển sang tab tạo bộ sản phẩm
    $('#productSetTabs a[href="#create-content"]').tab('show');
}

/**
 * Xóa bộ sản phẩm
 */
function deleteProductSet(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa bộ sản phẩm này?')) {
        return;
    }
    
    $.ajax({
        url: '/tiktok/delete-product-set',
        type: 'POST',
        data: {
            _token: $('input[name="_token"]').val(),
            product_set_id: id
        },
        beforeSend: function() {
            showNotification('info', 'Đang xóa bộ sản phẩm...');
        },
        success: function(response) {
            if (response.status === 'success') {
                showNotification('success', 'Đã xóa bộ sản phẩm thành công');
                
                // Tải lại danh sách bộ sản phẩm
                loadProductSets();
            } else {
                showNotification('error', response.message || 'Có lỗi xảy ra khi xóa bộ sản phẩm');
            }
        },
        error: function() {
            showNotification('error', 'Đã xảy ra lỗi khi xóa bộ sản phẩm');
        }
    });
}

/**
 * Áp dụng bộ sản phẩm cho livestream
 */
function applyProductSet(id) {
    const liveId = prompt('Nhập ID của livestream muốn áp dụng bộ sản phẩm này:');
    
    if (!liveId) {
        return;
    }
    
    $.ajax({
        url: '/tiktok/apply-product-set',
        type: 'POST',
        data: {
            _token: $('input[name="_token"]').val(),
            live_id: liveId,
            product_set_id: id
        },
        beforeSend: function() {
            showNotification('info', 'Đang áp dụng bộ sản phẩm...');
        },
        success: function(response) {
            if (response.status === 'success') {
                showNotification('success', 'Đã áp dụng bộ sản phẩm thành công');
                
                // Hiển thị kết quả chi tiết
                let resultHtml = '<div class="alert alert-success">Đã thêm sản phẩm vào livestream!</div>';
                resultHtml += '<div class="table-responsive"><table class="table table-bordered table-sm">';
                resultHtml += '<thead><tr><th>Sản phẩm</th><th>Trạng thái</th></tr></thead><tbody>';
                
                response.result.forEach(function(item) {
                    const statusClass = item.status === 'success' ? 'text-success' : 'text-danger';
                    const statusIcon = item.status === 'success' ? 'check-circle' : 'times-circle';
                    resultHtml += `<tr>
                        <td>${item.name} (${item.product_id})</td>
                        <td class="${statusClass}"><i class="fa fa-${statusIcon}"></i> ${item.status === 'success' ? 'Thành công' : 'Thất bại'}</td>
                    </tr>`;
                });
                
                resultHtml += '</tbody></table></div>';
                
                // Hiển thị kết quả trong modal
                showModal('Kết quả áp dụng bộ sản phẩm', resultHtml);
            } else {
                showNotification('error', response.message || 'Có lỗi xảy ra khi áp dụng bộ sản phẩm');
            }
        },
        error: function() {
            showNotification('error', 'Đã xảy ra lỗi khi áp dụng bộ sản phẩm');
        }
    });
}

/**
 * Chuyển đổi kiểu cấu hình pin
 */
function togglePinConfigType(type) {
    if (type === 'interval') {
        $('#interval_config').show();
        $('#specific_config').hide();
    } else {
        $('#interval_config').hide();
        $('#specific_config').show();
        
        // Tải danh sách sản phẩm nếu đã chọn bộ sản phẩm
        const selectedSetId = $('#select_product_set').val();
        if (selectedSetId) {
            loadProductSetForPinConfig(selectedSetId);
        }
    }
}

/**
 * Tải danh sách sản phẩm của bộ đã chọn
 */
function loadProductSetForPinConfig(setId, existingPinTimes) {
    $('#pin_times_loading').show();
    $('#pin_time_items').empty();
    
    $.ajax({
        url: '/tiktok/get-product-sets',
        type: 'GET',
        success: function(response) {
            $('#pin_times_loading').hide();
            
            if (response.status === 'success' && response.productSets) {
                // Tìm bộ sản phẩm đã chọn
                let selectedSet = null;
                for (const set of response.productSets) {
                    if (set.id == setId) {
                        selectedSet = set;
                        break;
                    }
                }
                
                if (!selectedSet || !selectedSet.products || selectedSet.products.length === 0) {
                    $('#pin_time_items').html('<tr><td colspan="4" class="text-center">Không có sản phẩm nào trong bộ này</td></tr>');
                    return;
                }
                
                // Tạo map của các thời gian pin đã cấu hình
                const pinTimeMap = {};
                if (existingPinTimes) {
                    existingPinTimes.forEach(function(item) {
                        pinTimeMap[item.product_id] = item.pin_time;
                    });
                }
                
                // Hiển thị danh sách sản phẩm
                let html = '';
                selectedSet.products.forEach(function(product, index) {
                    const productImage = product.image || '/images/no-image.png';
                    const pinTime = pinTimeMap[product.product_id] || '';
                    
                    html += `
                    <tr data-product-id="${product.product_id}">
                        <td>${index + 1}</td>
                        <td>
                            <img src="${productImage}" class="img-thumbnail" style="max-height: 50px;">
                        </td>
                        <td>
                            <strong>${product.name || 'Không có tên'}</strong><br>
                            <small class="text-muted">ID: ${product.product_id}</small>
                        </td>
                        <td>
                            <input type="number" class="form-control pin-time" name="pin_time_${product.product_id}" min="0" value="${pinTime}" placeholder="Thời gian (phút)">
                        </td>
                    </tr>`;
                });
                
                $('#pin_time_items').html(html);
            }
        }
    });
}

/**
 * Định dạng giá tiền
 */
function formatPrice(price) {
    if (!price) return 'Liên hệ';
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}



/**
 * Hiển thị modal với nội dung tùy chỉnh
 */
function showModal(title, content, callback = null, size = '') {
    // Sử dụng bootstrap modal hoặc modal tùy chỉnh của bạn
    let modalClass = 'modal-dialog';
    if (size === 'lg') {
        modalClass += ' modal-lg';
    } else if (size === 'sm') {
        modalClass += ' modal-sm';
    }
    
    const modalId = 'dynamic-modal-' + Math.floor(Math.random() * 1000);
    const modalHtml = `
    <div class="modal fade" id="${modalId}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="${modalClass}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${title}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">${content}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>`;
    
    // Thêm modal vào body
    $('body').append(modalHtml);
    
    // Hiển thị modal
    $('#' + modalId).modal('show');
    
    // Xử lý sự kiện đóng modal
    $('#' + modalId).on('hidden.bs.modal', function () {
        $(this).remove();
        if (typeof callback === 'function') {
            callback();
        }
    });
}

// Khởi tạo sự kiện khi trang đã tải xong
$(document).ready(function() {
    // Sự kiện cho nút xem trước sản phẩm
    $('#btn_preview_products').click(function() {
        previewProductLinks();
    });
    
    // Sự kiện cho nút lưu bộ sản phẩm
    $('#btn_create_product_set').click(function() {
        createProductSet();
    });
    
    $('#select_product_set').change(function() {
        const selectedSetId = $(this).val();
        $('#pin_config_set_id').val(selectedSetId);
        
        if (selectedSetId && $('#enable_auto_pin').is(':checked')) {
            if ($('input[name="pin_type"]:checked').val() === 'specific') {
                loadProductSetForPinConfig(selectedSetId);
            }
        } else {
            $('#pin_time_items').empty();
        }
    });
    
    // Sự kiện khi chọn kiểu pin
    $('input[name="pin_type"]').change(function() {
        togglePinConfigType($(this).val());
    });
    
    // Sự kiện cho nút lưu cấu hình pin
    $('#btn_save_pin_config').click(function() {
        savePinConfig();
    });
    
    // Khi đóng modal, reset form
    $('#dialog_pin_config').on('hidden.bs.modal', function() {
        $('#frmPinConfig')[0].reset();
        $('#pin_time_items').empty();
        $('.pin-config-container').hide();
    });
    
    $('#enable_auto_pin').change(function() {
        if ($(this).is(':checked')) {
            $('.pin-config-container').slideDown();
            
            // Tải danh sách sản phẩm nếu đang ở tab specific
            if ($('input[name="pin_type"]:checked').val() === 'specific') {
                const selectedSetId = $('#select_product_set').val();
                if (selectedSetId) {
                    loadProductSetForPinConfig(selectedSetId);
                }
            }
        } else {
            $('.pin-config-container').slideUp();
        }
    });
});    
    </script>
@endsection
