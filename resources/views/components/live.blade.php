@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-lg-6 col-xl-3">
        <div class="widget-bg-color-icon card-box fadeInDown animated">
            <div class="bg-icon bg-icon-violet pull-left">
                <i class="ti-package text-info"></i>
            </div>
            <div class="text-right">
                <h3 class="text-dark m-t-10"><b class="counter">{{$user_login->package_code}}</b>
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
                <h3 class="text-dark m-t-10"><b class="counter">{{\App\Common\Utils::countDayLeft($user_login->package_end_date)}}</b></h3>
                <p class="text-muted mb-0">Thời hạn sử dụng {{gmdate("Y/m/d",$user_login->package_end_date + 7 * 3600)}}</p>
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
                <h3 class="text-dark m-t-10"><b class="counter"><span id="count_total">{{$datas->total()}}</span>/{{$maxCreated}} luồng</b></h3>
                <p class="text-muted mb-0">Số luồng live đã tạo</p>
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
                <h3 class="text-dark m-t-10"><b class="counter count_run"><span id="count_run">{{$countRun}}</span>/{{$user_login->number_key_live}} luồng</b>
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
                <p>Mời các bạn tham gia nhóm Zalo 68 Học Viện Livestream để nhận CODE quà tặng, giảm giá. Được tư vấn, hỗ trợ nhiệt tình!Link tham gia nhóm 
                    <a target="_blank" href="https://zalo.me/g/msuihg308">https://zalo.me/g/msuihg308</a>
                Hoặc scan QR code <img width="200px" src="/images/zalo_group_1.png">
                    
                </p>
            </div>
        </div>
    </div>
</div>
@if($user_login->package_end_date < (time() + 3 * 86400) && $user_login->package_code!="LIVETEST")
<div class="row animated fadeInDown">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 color-red">Thông báo</h4>
            <div class="pa-20 color-red" style="font-weight: 500;">
                <p>Tài khoản của bạn sắp hết hạn, hãy gia hạn sớm để tránh làm gián đoạn luồng live</p>
            </div>
        </div>
    </div>
</div>
@endif
@if(isset($notify))
<div class="row animated fadeInDown">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 color-red">Thông báo</h4>
            <div class="pa-20 color-red" style="font-weight: 500;">
                   {!!$notify->content!!}
            </div>
        </div>
    </div>
</div>
@endif
<div id="edit_slideup" class="row animated fadeInDown">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0">Cấu hình</h4>
            <div class="pa-20">
                <form id="formLive" class="form-horizontal" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="edit_id" id="edit_id" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Tên luồng live</label>
                                <div class="col-12">
                                    <input type="text" class="form-control" name="note" id="note" placeholder="Đặt tên để gợi nhớ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Nền tảng</label>
                                <div class="col-12 d-flex">
                                    <button type="button"
                                            data-type="youtube"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15 pricing-box-active"
                                            value="rtmp://x.rtmp.youtube.com/live2">
                                        <i class="fa fa-youtube fa-2x" data-toggle="tooltip" data-placement="top" title="Youtube"></i></button>
                                    <button type="button"
                                            data-type="facebook"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15"
                                            value="rtmps://live-api-s.facebook.com:443/rtmp">
                                        <i class="fa fa-facebook-square fa-2x" data-toggle="tooltip" data-placement="top" title="Facebook"></i></button>
                                    <button type="button"
                                            data-type="twitch"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15"
                                            value="rtmp://live.twitch.tv/app">
                                        <i class="fa fa-twitch fa-2x" data-toggle="tooltip" data-placement="top" title="Twitch"></i></button>
                                    <button type="button"
                                            data-type="gglive"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15"
                                            value="rtmp://entrypoint.evgcdn.net/live">
                                        <i class="" data-toggle="tooltip" data-placement="top" title="GoLive">GoLive</i></button>
                                    <button type="button"
                                            data-type="livegame"
                                            class="h-60 w-60 radius-6 btn-live-type btn-icon cur-point m-r-15"
                                            value="rtmp://rtmp.vegacdn.com/publishapp">
                                        <i class="" data-toggle="tooltip" data-placement="top" title="LiveGame">Live Game</i></button>
                                    <input id="url_live" type="hidden" name="url_live" value="rtmp://x.rtmp.youtube.com/live2" />
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Khóa luồng</label>
                                <div class="col-12">
                                    <!--<input type="text" id="key_live" class="form-control" name="key_live">-->
                                    <textarea id="key_live" name="key_live" class="form-control resize-ta" rows="3" spellcheck="false"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-6 col-form-label">Loại nguồn</label>
                                <div class="col-12">
                                    <select id="type_source" class="form-control" name="type_source">
                                        <option value="1">Video</option>
                                        <!--<option value="2">Streamming</option>-->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Link nguồn <span class="load-check-url"><i class="ion-load-c fa-spin"></i> Đang kiểm tra...</span></label>

                                
                                <div class="col-12 position-relative">
                                    <textarea id="url_source" class="form-control resize-ta" name="url_source" rows="8" onchange="validateSource()"
                                              spellcheck="false"></textarea>
                                </div>
                                <div class="col-12">
                                    <ul>
                                        <li>
                                            <span class="font-13 font-weight-normal font-italic">Sử dụng 1 link Google Drive đạt hiệu năng tốt nhất,link youtube có hiện tượng lag</span>
                                        </li>
                                        <li>
                                            <span class="font-13 font-weight-normal font-italic">Thông số video chuẩn: định dạng: .mp4 , mã hóa: h264 , chất lượng âm thanh: 128kbs 44100 Hz</span>
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
                                        <input type="radio" id="radio_by" value="0" name="radio_by" checked="true">
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
                                        <input id="live_repeat" type="checkbox" name="live_repeat" value="0" checked>
                                        <label for="live_repeat">
                                            Live vĩnh viễn
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div_live_repeat m-l-15 animated wow " style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="checkbox">
                                            <input id="infinite_loop" type="checkbox" name="infinite_loop" value="1" >
                                            <label for="infinite_loop">
                                                Tiến trình vô hạn
                                            </label>
                                        </div>
                                        <span class="font-13"><i>Tiến trình được duy trì ổn định mãi mãi, phù hợp cho luồng live 1 file</i></span>
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
                    

                    <button type="button" class="btn btn-outline-violet waves-effect waves-primary btn-save-live " <?php echo($user_login->status == 0) ? "disabled" : ""; ?> >Auto live</button>
                    @if($user_login->status==0)
                    <span id="rq-test"><a onclick="requestTest('rq-test')" href="javascript:void()">Yêu cầu dùng thử</a> hoặc </span><span>Liên hệ <a target="_blnk" href="https://www.facebook.com/messages/t/100002470941874"> <b>Admin</b> </a> để được kích hoạt dùng thử</span>
                    @endif
                    @if($user_login->package_code!="LIVETEST" && $user_login->package_end_date > time() && $user_login->package_end_date  - 2 * 86400 < time())
                        <p class="font-20 m-t-10">Gói cước {{$user_login->package_code}} sắp hết hạn, hãy gia hạn sớm <a href="/invoice/{{$user_login->package_code}}" class=""><b>GIA HẠN</b></a></p>
                    @endif
                </form>
            </div>
            <div><a class="facebook-live" target="_blank" href="https://www.facebook.com/live/create" style="display: none;">Tạo video trực tiếp trên Facebook</a></div>
        </div>
    </div>
</div>
<div class="row animated fadeInDown">
    <div class="col-lg-6">
        <div class="card-box" style="min-height: 152px;">
            <h4 class="header-title m-t-0"><i class="fa fa-filter"></i> Lọc dữ liệu</h4>
            <div class="col-md-12 col-sm-6 col-xs-12">

                <form id="formFilter" class="form-label-left" action="/live" method="GET">
                    <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Tên luồng</label>
                                <div class="col-12">
                                    <input id="note" class="form-control" type="text" name="note" value="{{$request->note}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Trạng thái</label>
                                <div class="col-12">
                                    <select id="cbbStatus" name="s" class="form-control">
                                        {!!$statusLive!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">&nbsp;</label>
                                <div class="col-12">
                                    <button id="btnSearch" type="submit" class="btn btn-dark btn-micro"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-box" style="min-height: 152px;">
            <h4 class="header-title m-t-0"><i class="fa fa-info-circle"></i> Thông tin</h4>
            <div class="col-md-12 col-sm-6 col-xs-12">
                <table class="w-100 m-t-30 no-border font-18">
                    <tr>
                        <td><span class="badge badge-info font-15">Tổng số</span> {{count($datas)}}</td>
                        <td><span class="badge badge-success font-15">Mới</span> {{$countNew}}</td>
                        <td><span class="badge badge-violet font-15">Đang live</span> {{$countRun}}</td>
                        <td><span class="badge badge-warning font-15">Đang xử lý</span> {{$countProcess}}</td>
                        <td><span class="badge badge-danger font-15">Đã dừng </span> {{$countStoped}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row animated fadeInDown">
    <div class="col-lg-12">
        <div class="card-box">
            <!--<h4 class="m-t-0 header-title">Danh sách cấu hình</h4>-->
            <table style="width: 100%">
                <tr>
                    <td><h4 class="header-title m-t-0 m-b-30">Danh sách cấu hình <i style="font-size: 13px" class="color-green">(Nếu bị lỗi thì copy ID (chữ số màu đỏ) gửi cho admin)</i></h4></td>
                    <td><h4 class="header-title m-t-0 m-b-30 pull-right"><a data-toggle="tooltip" data-placement="top" data-html="true"
                                                                            title="<p style='text-align:left'>Tool này dùng để fix nguồn bị sai bit-rate,encode...<br>
                                                                            Bước 1: Download tool về,sau đó giải nén.<br>
                                                                            Bước 2: Mở file live-tool.exe và nhập username như yêu cầu<br>
                                                                            Bước 3: Chọn file nguồn sau đó tool sẽ tự động fix lỗi.<br>
                                                                            Bước 4: Upload file kết quả lên drive và sử dụng làm file nguồn để live</p>" target="_blank" href="https://drive.google.com/file/d/1MPMRxNhqYoUug-t-IXDBPa3Sgy9-J1F8/view?usp=sharing">Tool Fix Live</a></h4></td>
                </tr>
            </table>
            <br>
            @if(count($datas)>0)
            <div class="table-responsive">
                <table id="table-live" class="table hover-button-table">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-left w-10">Tên luồng</th>
                            <th>Nền tảng</th>
                            <!--<th class="text-center">Thứ tự live</th>-->
                            <th class="text-center">Thời gian bắt đầu</th>
                            <th class="text-center">Thời gian kết thúc</th>
                            <th class="text-center">Thời gian live</th>
                            <th class="w-15">Log</th>
                            <th class="w-15 text-center">Speed</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-right w-20">Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <?php
                        $red = "";
                        $tip = "";
                        if ($data->log != null) {
                            if (\App\Common\Utils::containString($data->log, "is not support") || \App\Common\Utils::containString($data->log, "is too hight")) {
                            $red = "color-red";
                            $tip = "Lệnh của bạn đang bị lỗi, hãy dùng tool fix live để xử lý.Click để xem hướng dẫn chi tiết";
                            $log ="Video của bạn bị lỗi Render. Bạn cần Render lại video định dạng .mp4, codec = h264, audio bitrate = 44100. Xem hướng dẫn sửa lỗi bằng cách bấm vào nút 'Hướng dẫn sửa lỗi' ở bên cạnh !";
                        } else if (\App\Common\Utils::containString($data->log, "not found") || \App\Common\Utils::containString($data->log, "Link sai định dạng") || \App\Common\Utils::containString($data->log, "cho phép download")) {
                            $red = "color-red";
                            $tip = "Link nguồn của bạn có vấn đề, vui lòng kiểm tra lại.";
                            $log = "Link nguồn của bạn có vấn đề, vui lòng kiểm tra lại.";
                        }elseif(\App\Common\Utils::containString($data->log, "Video < 1MB")){
                            $red = "color-red";
                            $tip = "Không thể download được video, vui lòng kiểm tra lại link. Bạn cần sử dụng link Google Drive chia sẻ công khai";
                            $log = "Không thể download được video, vui lòng kiểm tra lại link. Bạn cần sử dụng link Google Drive chia sẻ công khai";

                        }else{
                            $log = substr_replace($data->log, '<span class="dots">...</span><span class="more">', 30, 0) . '</span><div class="view-more color-violet cur-point" onclick="">Xem thêm</div>';
                        }
                        } else {
                            $log = "";
                        }
                        ?>
                        <tr class="{{$red}}">
                            <td class="text-center color-violet"><b>{{$data->id}}</b></td>
                            <td class="text-left">{{$data->note}}</td>
                            <td class="text-left">
                                @if(\App\Common\Utils::containString($data->url_live,'youtube'))
                                Youtube
                                @elseif(\App\Common\Utils::containString($data->url_live,'facebook'))
                                Facebook
                                @elseif(\App\Common\Utils::containString($data->url_live,'twitch'))
                                Twitch
                                @elseif(\App\Common\Utils::containString($data->url_live,'entrypoint.evgcdn'))
                                GoLive
                                @elseif(\App\Common\Utils::containString($data->url_live,'vegacdn.com'))
                                LiveGame
                                @endif
                            </td>
                            <!--<td class="text-center">@if($data->seq_source==0) Lần lượt @else Ngẫu nhiên @endif</td>-->
                            <td class="text-center ur-status">@if($data->start_alarm == 0) Chạy ngay @else
                                {{gmdate("m/d/Y H:i",$data->start_alarm + $user_login->timezone * 3600)}} @endif</td>
                            <td class="text-center ur-status">
                                @if($data->end_alarm!=0) 
                                {{gmdate("m/d/Y H:i",$data->end_alarm + $user_login->timezone * 3600)}} 
                                @elseif($data->repeat == 0)
                                Vĩnh viễn 
                                @else
                                Hết video
                                @endif
                            </td>
                            <td class="text-center">{{\App\Common\Utils::timeText($data->started_time)}}</td>
                            <td>
                                {!!$log!!}
                            </td>
                            <td><div><canvas id="speed{{$data->id}}"></canvas></div></td>
                            <td class="text-center ur-status">
                                @if($data->status==0)
                                @if($red=="")
                                <span class="badge badge-success">Mới</span>
                                @else
                                <a target="_blank" href="https://blog.autolive.vip/huong-dan-xu-ly-loi-live-stream-bi-giat-lag/"><span class="badge badge-danger" data-toggle="tooltip" data-placement="top" title="{{$tip}}">Hướng dẫn sửa lỗi</span></a>
                                @endif
                                @elseif($data->status==1)
                                <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang đợi live</span>
                                @elseif($data->status==2)
                                <span class="badge badge-violet">Đang live</span>
                                @elseif($data->status==3)
                                <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang đợi dừng</span>
                                @elseif($data->status==4)
                                <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang xử lý</span> {!!$data->estimate!!}
                                @elseif($data->status==5)
                                <span class="badge badge-danger">Đã dừng</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($data->status==2 && $data->repeat==0)
<!--                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-restart-live" style="border-radius:5px;width: 85px"
                                        data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top"
                                        title="Fix Nodata">Fix Nodata</button>-->
                                @endif
                                @if(($isUpdateSource || $isAdmin) && $data->status==2)
                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-change-source"
                                        data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top"
                                        title="Đổi nguồn live"><i class=" ti-exchange-vertical cur-point"></i></button>
                                @endif
                                @if($data->is_report==0)
                                <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-report-bug"
                                        data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top"
                                        title="Báo lỗi live"><i class="fa fa-bug cur-point"></i></button>
                                @endif
                                @if($isAdmin)
                                <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-action-log" onclick="viewActionLog({{$data->id}})"
                                        data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top"
                                        title="Xem log action"><i class="fa fa-info-circle cur-point"></i></button>
                                @endif
                                @if($data->status==0  || $data->status==5)
                                <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-status-live"
                                        data-id="{{$data->id}}" data-status='1' data-toggle="tooltip" data-placement="top"
                                        title="Bắt đầu live"><i class="fa fa-play cur-point"></i></button>
                                @elseif($data->status==2 || $data->status==4 || $data->status==1)
                                <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-status-live"
                                        data-id="{{$data->id}}" data-status='3' data-toggle="tooltip" data-placement="top"
                                        title="Dừng live"><i class="fa fa-stop cur-point"></i></button>
                                     
                                @endif
                                <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-edit-live"
                                        data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top"
                                        title="Sửa thông tin"><i class="fa fa-pencil-square-o cur-point"></i></button>
                                <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-status-live"
                                        data-id="{{$data->id}}" data-status='-1' data-toggle="tooltip" data-placement="top"
                                        title="Xóa"><i class="fa fa-times-circle cur-point"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
                        <div class="row ">
                <div class="col-md-6 ">
                    <div>

                        <?php
                            $info = str_replace('_START_', $datas->firstItem() != null ? $datas->firstItem() : '0', trans('label.title.sInfo'));
                            $info = str_replace('_END_', $datas->lastItem() != null ? $datas->lastItem() : '0', $info);
                            $info = str_replace('_TOTAL_', $datas->total(), $info);
                            echo $info;
                            ?>
                    </div>
                </div>
                <div class="col-md-6 ">
                    <div class="pull-right disp-flex" style="display: flex;">
                        <select id="cbbLimit" name="limit" aria-controls="tbl-title" class="form-control input-sm">
                            {!!$limitSelectbox!!}
                        </select>&nbsp;
                        <?php if (isset($datas)) { ?>
                        {!!$datas->links()!!}
                        <?php } ?>
                    </div>
                </div>

            </div>
            @else
            <center>Không có dữ liệu</center>
            @endif

        </div>
    </div>
</div>

 @if($isAdmin)
    @include('dialog.log')
 @endif
@endsection

@section('script')
<script type="text/javascript">
    
        $(".btn-restart-live").click(function (e) {
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
                success: function (data) {
                    btn.html(btn.data('original-text'));
                    if (data.status == 'success') {
                         btn.hide();
                    } 
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.reload == 1) {
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function (data) {
                    btn.html(btn.data('original-text'));
                    console.log(data);
                }
            });
        });
        $(".btn-change-source").click(function (e) {
            e.preventDefault();
            var btn = $(this);
            var id = btn.attr("data-id");
            var loadingText = '<i class="ion-load-c fa-spin"></i>';

            $.confirm({
                animation: 'rotateXR',
                title: 'Confirm!',
                content: 'Bạn có chắc chắn là đã thay đổi link nguồn?',
                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function () {
                            if (btn.html() !== loadingText) {
                                btn.data('original-text', btn.html());
                                btn.html(loadingText);
                            }
                            $.ajax({
                                type: "POST",
                                url: "/updateSource",
                                data: {
                                    "id": id,
                                    "_token": $("input[name=_token]").val()
                                },
                                dataType: 'json',
                                success: function (data) {
                                    console.log(data);
                                    btn.html(btn.data('original-text'));
//                                    if (data.status == 'success') {
//                                         btn.hide();
//                                    } 
                                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                                    if (data.reload == 1) {
                                        setTimeout(function () {
                                            location.reload();
                                        }, 3000);
                                    }
                                },
                                error: function (data) {
                //                    btn.html($this.data('original-text'));
                                    console.log(data);
                                }
                            });
                        }
                    },
                    cancel: function () {

                    }

                }
            });            


        });
        $(".btn-report-bug").click(function (e) {
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
                success: function (data) {

                    if (data.status == 'success') {
                         btn.hide();
                    } 
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.reload == 1) {
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function (data) {
//                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        });
    
    @if($isAdmin)
        function viewActionLog(id){
            $('#dialog_log').modal({
                backdrop: false
            });
            $("#dialog_log_loading").show();
            $.ajax({
                type: "GET",
                url: "/live/" + id,
                data: {},
                dataType: 'json',
                success: function (data) {
                    $("#dialog_log_loading").hide();
                    if(data.action_log==null){
                        data.action_log ="Không có dữ liệu";
                    }
                    $("#log-content").val(data.action_log);
                    h = calcHeight(data.action_log);
                    $("#log-content").css({"height":h+"px"});
                },
                error: function (data) {
                   
                }
            }); 
        }
    @endif
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
            success: function (data) {
//                console.log(data);
                var text = "";
                $.each(data, function (key, val) {
                    text += val.message + (key==(data.length-1)?"":"\n");
                });
                $("#url_source_check").val(text);
                $(".load-check-url").hide();
                if (data.length > 0) {
                    $("#result_check").fadeIn("slow");
                }
                $('#url_source_check').css("height",calcHeight($("#url_source_check").val()));
            },
            error: function (data) {
                $(".load-check-url").hide();
            }
        });
    }

    function requestTest() {
        $.ajax({
            type: "GET",
            url: "/requestTest",
            data: {},
            dataType: 'json',
            success: function (data) {
                $('#rq-test').hide();
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
            },
            error: function (data) {
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
    var countRun = <?php echo $countRun; ?>;
    $(".btn-live-type").click(function () {
        $(".btn-live-type").removeClass("pricing-box-active");
        $(this).addClass("pricing-box-active");
        $("#url_live").val($(this).val());
        if ($(this).attr("data-type") === "facebook") {
            $(".facebook-live").fadeIn();
            $(".div_live_repeat").fadeIn();
        } else {
            $(".facebook-live").hide();
            $(".div_live_repeat").hide();
        }
    });

    function checkStatusLive(btn, badge, id) {
        var intval = setInterval(function () {
            $.ajax({
                type: "GET",
                url: "/live/" + id,
                data: {},
                dataType: 'json',
                success: function (data) {
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
                            html = '<span class="badge badge-violet animated zoomInRight">Đang live</span>';
                        } else if (data.status == 0) {
                            btn.removeClass("disp-none");
                            html = '<span class="badge badge-success animated zoomInRight">Mới</span>';
                            btn.attr("data-status", "1");
                            btn.attr("data-original-title", "Bắt đầu live");
                            btn.html('<i class="fa fa-play cur-point"></i>');
                            location.reload();
                        } else if (data.status == 5) {
                            btn.removeClass("disp-none");
                            html = '<span class="badge badge-danger animated zoomInRight">Đã dừng</span>';
                        }
                        badge.prev().html(html);
                    } else if (data.status == 4) {
                        html = '<span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang xử lý</span> ' + data.estimate;
                        badge.prev().html(html);
                    }


                },
                error: function (data) {
                    clearInterval(intval);
                }
            });
        }, 3000);
    }
    eventStatusLive();

    function eventStatusLive() {
        $(".btn-status-live").click(function (e) {
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
                success: function (data) {
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

                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    if (data.reload == 1) {
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function (data) {
//                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        });
    }

    $(".btn-edit-live").click(function (e) {
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
            success: function (data) {
//                console.log(data);
                $(".btn-save-live").html("Update")
                btn.html(btn.data('original-text'));
                if (data.status == 'error') {
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                } else {
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#edit_slideup").offset().top
                    }, 500);
                    $(".btn-live-type").removeClass("pricing-box-active");
                    $(".btn-live-type[value='" + data.url_live + "']").addClass("pricing-box-active");
                    $("#edit_id").val(data.id);
                    $("#note").val(data.note);
                    $("#url_live").val(data.url_live);
                    $("#key_live").val(data.key_live);
                    $("#type_source").val(data.type_source).change();
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
                    
                    if (data.infinite_loop == 1) {
                        $("#infinite_loop").prop('checked', true);
                    } else {
                        $("#infinite_loop").prop('checked', false);
                    }                    
                }

            },
            error: function (data) {
                btn.html($this.data('original-text'));
            }
        });
    });
    $(".btn-save-live").click(function (e) {
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
            url: "/live",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
//                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                if (data.status == 'success') {
                    location.reload();
                }
            },
            error: function (data) {
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
    $(".view-more").click(function () {
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
    
    

    calcHeight =function (value) {
        let numberOfLineBreaks = (value.match(/\n/g) || []).length;
        // min-height + lines x line-height + padding + border
        let newHeight = 20 + numberOfLineBreaks * 20 + 12 + 2 + 10;
        return newHeight;
    };

    let textarea = document.querySelector(".resize-ta");
    textarea.addEventListener("keyup", () => {
        textarea.style.height = calcHeight(textarea.value) + "px";
    });
</script>
@endsection