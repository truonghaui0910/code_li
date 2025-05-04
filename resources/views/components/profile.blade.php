@extends('layouts.master')

@section('content')
<div class="row">
    <!--    <div class="col-lg-6 fadeInDown animated">
            <div class="card-box">
                <h4 class="header-title m-t-0 m-b-20">Cost</h4>
    
            </div>
        </div>-->
    <div class="col-xl-4 col-lg-4">
        <div class="text-center card-box">
            <div class="member-card">
                <div class="thumb-xl member-thumb m-b-10 center-block">
                    <img src="images/default-avatar.png" class="rounded-circle img-thumbnail" alt="profile-image" style="width: 128px;height: 128px">
                    <!--                    <div class="widget-bg-color-icon"><div class="bg-icon bg-icon-violet pull-in">
                                        <i class=" ti-user text-info"></i>
                                        </div>
                                        </div>-->
                </div>

                <div class="">
                    <h4 class="m-b-5">{{$user_login->user_name}}</h4>
                </div>
                @if($isVip || $isAdmin)
                <button type="button" class="btn btn-success btn-sm w-sm waves-effect m-t-10 waves-light btn-vip-add-user">Tạo tài khoản</button>
                @else
                <button class="btn btn-success btn-sm w-sm waves-effect m-t-10 waves-light" onclick="requestVip()" >Yêu cầu tạo tài khoản</button>
                @endif

                <button type="button" class="btn btn-danger btn-sm w-sm waves-effect m-t-10 waves-light btn-change-info">Đổi mật khẩu</button>
                <button type="button" class="btn btn-violet btn-sm w-sm waves-effect m-t-10 waves-light btn-dialog-bonus">Mã nhận thưởng</button>


                <div class="text-left m-t-40">

                    <p class="text-muted "><strong>Số điện thoại :</strong><span class="m-l-15">{{$user_login->phone}}</span></p>
                    <p class="text-muted "><strong>Gói đang sử dụng :</strong> <span class="m-l-15">{{$user_login->package_code}}, {{$user_login->tiktok_package}}</span></p>
                    <p class="text-muted "><strong>Được tạo :</strong> <span class="m-l-15">{{$user_login->number_key_live * 5}} luồng live, {{$user_login->tiktok_key_live *10}} tài khoản tiktok</span></p>
                    <p class="text-muted "><strong>Số luồng chạy đồng thời :</strong> <span class="m-l-15"><span id="key_live_1">{{$user_login->number_key_live}}</span> live, <span id="key_live_2">{{$user_login->tiktok_key_live}}</span> tiktok</span></p>
                    <p class="text-muted "><strong>Số tài khoản được tạo :</strong> <span class="m-l-15">{{$user_login->number_account}}</span></p>
                    <p class="text-muted "><strong>Ngày đăng ký :</strong> <span class="m-l-15">{{gmdate('Y/m/d H:i:s',$user_login->package_start_date + 7 *3600)}}</span></p>
                    <p class="text-muted "><strong>Ngày hết hạn :</strong> <span class="m-l-15">{{gmdate('Y/m/d H:i:s',$user_login->package_end_date + 7 *3600)}} ({{\App\Common\Utils::countDayLeft($user_login->package_end_date)}})</span></p>
                    <p class="text-muted "><strong>Ngày đăng ký tiktok :</strong> <span class="m-l-15">@if($user_login->tiktok_start_date!=null) {{gmdate('Y/m/d H:i:s',$user_login->tiktok_start_date + 7 *3600)}} @endif</span></p>
                    <p class="text-muted "><strong>Ngày hết hạn tiktok:</strong> <span class="m-l-15">{{gmdate('Y/m/d H:i:s',$user_login->tiktok_end_date + 7 *3600)}} ({{\App\Common\Utils::countDayLeft($user_login->tiktok_end_date)}})</span></p>

                </div>

                <ul class="social-links list-inline m-t-30">
                    <li class="list-inline-item">
                        <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="{{$user_login->facebook}}" data-original-title="Facebook"><i class="fa fa-facebook"></i></a>
                    </li>
                </ul>

            </div>

        </div> <!-- end card-box -->

    </div>
    <div class="col-xl-8 col-lg-4">
        <div class="card-box">

            <div class="p-b-10">
                <p>Số luồng đang chạy <span id="count_run">{{$countRun}}</span>/{{$user_login->number_key_live}}</p>
                <div class="progress progress-sm">
                    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $user_login->number_key_live == 0 ? 0 : ($countRun / $user_login->number_key_live * 100); ?>%">
                    </div>
                </div>
                <p>Số luồng live đã tạo <span id="count_run">{{$countAll}}</span>/{{$user_login->number_key_live * 5}}</p>
                <div class="progress progress-sm">

                    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $user_login->number_key_live == 0 ? 0 : ($countAll / ($user_login->number_key_live * 5)) * 100; ?>%">
                    </div>
                </div>
            </div>
        </div>
        @if(count($childs)>0)
        <div class="card-box">
            <h4 class="m-t-0 m-b-20 header-title">Danh sách tài khoản con</h4>
<!--                                                <div class="form-group row">
                                        <table class="w-100">
                                            <td>
                                                <label class="col-12 col-form-label">Danh sách tài khoản</label>

                                            </td>
                                            <td>
                                                <label class="col-12 col-form-label">Nền tảng</label>
                                                <div class="col-12">
                                                        <select id="platform" class="form-control" name="platform">
                                                            <option value="1">Youtube/Facebook</option>
                                                            <option value="2">Tiktok</option>
                                                            <option value="3">Shopee/Lazada</option>
                                                        </select>
                                                </div>
                                            </td>

                                        </table>
                                    </div>-->
            <div class="p-b-10">
                <div class="table-responsive">
                    <table id="table-live" class="table">
                        <thead>
                            <tr>
                                <th class="text-left w-10">Tên tài khoản</th>
                                <th class="text-center">Số luồng Youtube</th>
                                <th class="text-center">Số luồng Tiktok</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Online</th>
                                <!--<th class="text-right">Chức Năng</th>-->

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($childs as $data)
                            <tr>
                                <td class="text-left w-30"><b>{{$data->user_name}}</b></td>
                                <td class="text-center">
                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-change-live" 
                                            data-id="{{$data->id}}" data-cal="p" data-platform="1" data-toggle="tooltip" data-placement="top"
                                            title="Cộng 1 luồng live"><i class="fa fa-plus cur-point"></i></button>
                                    {{$data->youtube_living}}/<span class="1_{{$data->id}}">{{$data->number_key_live}}</span>
                                                                                               <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-change-live" 
                                            data-id="{{$data->id}}" data-cal="m" data-platform="1" data-toggle="tooltip" data-placement="top"
                                            title="Trừ 1 luồng live"><i class="fa fa- fa-minus cur-point"></i></button>
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-change-live" 
                                            data-id="{{$data->id}}" data-cal="p" data-platform="2"data-toggle="tooltip" data-placement="top"
                                            title="Cộng 1 luồng live"><i class="fa fa-plus cur-point"></i></button>
                                    {{$data->tiktok_living}}/<span class="2_{{$data->id}}">{{$data->tiktok_key_live}}</span>
                                                                                               <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-change-live" 
                                            data-id="{{$data->id}}" data-cal="m" data-platform="2" data-toggle="tooltip" data-placement="top"
                                            title="Trừ 1 luồng live"><i class="fa fa- fa-minus cur-point"></i></button>
                                </td>
                                <td class="text-center ur-status">{{gmdate("Y/m/d H:i",$data->created + $user_login->timezone * 3600)}}</td>
                                <td class="text-center">{{\App\Common\Utils::timeText($data->last_activity)}}</td>

<!--                                <td class="text-right ">
                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-change-live" 
                                            data-id="{{$data->id}}" data-cal="p" data-toggle="tooltip" data-placement="top"
                                            title="Cộng 1 luồng live"><i class="fa fa-plus cur-point"></i></button>
                                    <button class="btn btn-circle btn-dark btn-sm waves-effect waves-light btn-change-live" 
                                            data-id="{{$data->id}}" data-cal="m" data-toggle="tooltip" data-placement="top"
                                            title="Trừ 1 luồng live"><i class="fa fa- fa-minus cur-point"></i></button>
                                </td>-->

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        @endif
        <div class="card-box">
            <h4 class="m-t-0 m-b-20 header-title">Lịch sử giao dịch</h4>
            <div class="p-b-10">
                @if(count($invoices)>0)
                <div class="table-responsive">
                    <table id="table-live" class="table">
                        <thead>
                            <tr>

                                <th class="text-left w-10">Mã hóa đơn</th>
                                <th class="text-center">Số tháng</th>
                                <th class="text-center">Ngày tạo</th>
                                <!--<th class="text-center">Hạn thanh toán</th>-->
                                <th class="text-center">Số tiền</th>
                                <th class="text-center">Trạng thái</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $data)
                            <tr>
                                <td class="text-left w-30"><b>{{$data->invoice_id}}</b></td>
                                <td class="text-center">{{$data->month}}</td>
                                <td class="text-center ur-status">{{gmdate("Y/m/d H:i",$data->system_create_date + $user_login->timezone * 3600)}}</td>
                                <!--<td class="text-center">{{\App\Common\Utils::countDayLeft($data->due_date)}}</td>-->
                                <td class="text-center"><span data-toggle="tooltip" data-placement="top"  data-original-title="{{$data->note}}">{{number_format($data->payment_money, 0, ',', '.')}}</span></td>
                                <td class="text-center ur-status">
                                    @if($data->status==0 && $data->due_date >= time())
                                    <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang chờ xử lý</span>
                                    @elseif($data->status==1)
                                    <span class="badge badge-success">Thành công</span>
                                    @elseif($data->status==0 && $data->due_date < time())
                                    <span class="badge badge-danger">Đã hết hạn</span>
                                    @endif
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
</div>
{{ csrf_field() }}
@include('dialog.user_edit_info')
@if($isVip || $isAdmin)
@include('dialog.vip_add_user')
@endif
@endsection

@section('script')
<script type="text/javascript">


    $(".btn-change-live").click(function (e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.attr("data-id");
        var cal = btn.attr("data-cal");
        var platform =  btn.attr("data-platform");
        var loadingText = '<i class="ion-load-c fa-spin"></i>';
        if ($(this).html() !== loadingText) {
            btn.data('original-text', $(this).html());
            btn.html(loadingText);
        }
        $.ajax({
            type: "POST",
            url: "/profile/calulate/live",
            data: {
                "id": id,
                "cal": cal,
                "platform":platform,
                "_token": $("input[name=_token]").val()
            },
            dataType: 'json',
            success: function (data) {
                btn.html(btn.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                if (data.status == 'success') {

                    $(`#key_live_${platform}`).html(data.parent);
                    $(`.${platform}_${id}`).html(data.child);
                }

            },
            error: function (data) {
                console.log(data);
            }
        });
    });
    function requestVip() {
        $.ajax({
            type: "GET",
            url: "/requestVip",
            data: {},
            dataType: 'json',
            success: function (data) {
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                if (data.status == 'success') {
                    location.reload();
                }
            },
            error: function (data) {

            }
        });
    }
    $(".btn-change-info").click(function (e) {
        e.preventDefault();
        $('#dialog_user_edit_info').modal({
            backdrop: false
        });
    });

    $(".btn-save-info").click(function (e) {
        e.preventDefault();
        var form = $("#formAddCustomer").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "POST",
            url: "/changeUserInfo",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);

            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    $(".btn-vip-add-user").click(function (e) {
        e.preventDefault();
        $("vip_user_name").val("");
        $("vip_password").val("");
        $("vip_number_live").val(1);
        $('#dialog_vip_add_user').modal({
            backdrop: false
        });
    });
    $(".btn-vip-save-user").click(function (e) {
        e.preventDefault();
        var form = $("#formVipAddUser").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "POST",
            url: "/vipCreateNewUser",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                location.reload();

            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });
    $("#vip_number_live").change(function () {
        var remain = $("#vip_number_live_remain_hidden").val();
        $("#vip_number_live_remain").val(remain - $(this).val());
    });
</script>
@endsection

