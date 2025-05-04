@extends('layouts.master')

@section('content')
<div class="row fadeInDown animated">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0"><i class="fa fa-filter"></i> {{ trans('label.filterSearch') }}</h4>
            <div class="col-md-12 col-sm-6 col-xs-12">

                <form id="formFilter" class="form-label-left" action="/customer" method="GET">
                    <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Username</label>
                                <div class="col-12">
                                    <input id="username" class="form-control" type="text" name="username" value="{{$request->username}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Status</label>
                                <div class="col-12">
                                    <select id="cbbStatus" name="s" class="form-control">
                                        {!!$status!!}
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
</div>
<div class="fadeInDown animated">
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="header-title m-t-0">Customer</h4>
                    </div>
                    <div class="col-md-6 m-b-10">
                        <i class="fa fa-ellipsis-v"></i>
                        <button class="btn btn-dark btn-xs pull-right btn-add-bonus-code m-l-10"> <i class="fa  fa-gift fa-fw"></i> Bonus</button>
                        <button style="width:90px" class="btn btn-dark btn-xs pull-right btn-add-notify m-l-10"> <i class="fa  fa-bell fa-fw"></i> Thông báo</button>
                        <button style="width:90px" class="btn btn-dark btn-xs pull-right btn-add-user" data-type="1" value=""> <i class="fa fa-plus fa-fw"></i> {{trans('Thêm user')}}</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table hover-button-table">
                        <thead>
                            <tr>
                                <th scope="row" class="text-left">ID</th>
                                <th scope="row" class="text-left">Tài khoản</th>
                                <th scope="row" class="text-center">Ips</th>
                                <th scope="row" class="text-center">Gói cước</th>
                                <th scope="row" class="text-center">Live</th>
                                <th scope="row" class="text-center">Tiktok</th>
                                <th scope="row" class="text-center">Shopee</th>
                                <th scope="row" class="text-center">Thông tin</th>
                                <th scope="row" class="text-center">Ngày tạo</th>
                                <th scope="row" class="text-center">@sortablelink('last_activity','Online')</th>
                                <!--<th scope="row" class="text-center">Hết hạn</th>-->
                                <th scope="row" class="text-center">Trạng thái</th>
                                <th scope="row" class="text-right">Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $data)
                            <tr>
                                <td scope="row" class="text-left">{{$data->id}}</td>
                                <td class="text-left">{!!$data->group_user!!}</td>
                                <td class="text-center">{!!$data->ip_count!!}</td>
                                <td class="text-center">
                                    <?php
                                    $customerYoutube = "";
                                    $customerTiktok = "";
                                    $customerShopee = "";
                                    if ($data->is_default == 1) {
                                        $customerYoutube = "0";
                                        $customerTiktok = "0";
                                        $customerShopee = "0";
                                        foreach ($livingByCus as $living) {
                                            if ($living->cus_id == $data->customer_id) {
                                                if ($living->platform == 1) {
                                                    $customerYoutube = "$living->total";
                                                } else if ($living->platform == 2) {
                                                    $customerTiktok = "$living->total";
                                                } else if ($living->platform == 3) {
                                                    $customerShopee = "$living->total";
                                                }
                                            }
                                        }
                                        foreach ($totalLiveByCus as $living) {
                                            if ($living->customer_id == $data->customer_id) {
                                                $customerYoutube .= "/$living->number_key_live";
                                                $customerTiktok .= "/$living->tiktok_key_live";
                                                $customerShopee .= "/$living->shopee_key_live";
                                                continue;
                                            }
                                        }
                                    }
                                    ?>
                                    <span class="{{$data->end_color}} cur-point" data-toggle="tooltip" data-html="true" data-placement="top" title="{{\App\Common\Utils::countDayLeft($data->package_end_date)}}<br>{{gmdate("Y/m/d H:i:s",$data->package_end_date + 7 *3600)}}">{{$data->package_code}} {{$customerYoutube}}</span>&nbsp;
                                    <span class="{{$data->tiktok_end_color}} cur-point" data-toggle="tooltip" data-html="true" data-placement="top" title="{{\App\Common\Utils::countDayLeft($data->tiktok_end_date)}}<br>{{gmdate("Y/m/d H:i:s",$data->tiktok_end_date + 7 *3600)}}">{{$data->tiktok_package}} {{$customerTiktok}}</span>&nbsp;
                                    <span class="{{$data->shopee_end_color}} cur-point" data-toggle="tooltip" data-html="true" data-placement="top" title="{{\App\Common\Utils::countDayLeft($data->shopee_end_date)}}<br>{{gmdate("Y/m/d H:i:s",$data->shopee_end_date + 7 *3600)}}">{{$data->shopee_package}} {{$customerShopee}}</span>
                                </td>
                                <?php
                                $li = "0/$data->number_key_live";
                                $tiktokLive = "0/$data->tiktok_key_live";
                                $shopeeLive = "0/$data->shopee_key_live";
                                foreach ($livings as $living) {
                                    if ($living->user_id == $data->user_code) {
                                        if ($living->platform == 1) {
                                            $li = "$living->total/$data->number_key_live";
                                        } else if ($living->platform == 2) {
                                            $tiktokLive = "$living->total/$data->tiktok_key_live";
                                        } else if ($living->platform == 3) {
                                            $shopeeLive = "$living->total/$data->shopee_key_live";
                                        }
                                        continue;
                                    }
                                }
//                                Log::info("$data->user_code $li $tiktokLive");
                                ?>
                                <td class="text-center">{{$li}}</td>
                                <td class="text-center">{{$tiktokLive}}</td>
                                <td class="text-center">{{$shopeeLive}}</td>
                                <td class="text-center">
                                    <a target="_blank" href="{{$data->facebook}}"><i data-toggle="tooltip" data-placement="top" title="Facebook" class="mdi mdi-facebook-box font-18 cur-point"></i></a>
                                </td>
                                <td class="text-center">{{gmdate("Y/m/d H:i:s",$data->created + 7 *3600)}}</td>
                                <td class="text-center">{{\App\Common\Utils::timeText($data->last_activity)}}</td>
                                <!--<td class="text-center" data-toggle="tooltip" data-placement="top" title="{{gmdate("Y/m/d H:i:s",$data->package_end_date + 7 *3600)}}">{{\App\Common\Utils::countDayLeft($data->package_end_date)}}</td>-->
                                <td class="text-center ur-status">@if($data->status == 2 || $data->status==0) <span class="badge badge-danger">Inactive</span> @else <span class="badge badge-success">Active</span> @endif</td>
                                <td class="text-right">
                                    @if($data->status==0 || $data->status==2)
                                    <a class="btn btn-dark btn-sm waves-effect waves-light btn-status-user" data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top" title="Mở khóa tài khoản"><i class="ti-unlock cur-point"></i></a>
                                    @else
                                    <a class="btn btn-dark btn-sm waves-effect waves-light btn-status-user" data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top" title="Khóa tài khoản"><i class="ti-lock cur-point"></i></a>
                                    @endif
                                    <!--<a class="btn btn-dark btn-sm waves-effect waves-light btn-alert" data-id="{{$data->id}}" data-us="{{$data->user_name}}" data-toggle="tooltip" data-placement="top" title="Thông báo"><i class="fa fa-bell cur-point"></i></a>-->
                                    <a class="btn btn-dark btn-sm waves-effect waves-light btn-bonus" data-id="{{$data->id}}" data-us="{{$data->user_name}}" data-toggle="tooltip" data-placement="top" title="Bonus"><i class="ti-gift cur-point"></i></a>
                                    <a class="btn btn-dark btn-sm waves-effect waves-light btn-invoice" data-id="{{$data->id}}" data-us="{{$data->user_name}}" data-pkg="{{$data->old_pkg}}" data-keylive="{{$data->old_key_live}}" data-money="{{$data->old_money}}" data-account="{{$data->number_account}}"  data-month="{{$data->old_month}}"data-toggle="tooltip" data-placement="top" title="Tạo hóa đơn"><i class="fa fa-credit-card cur-point"></i></a>
                                    <a class="btn btn-dark btn-sm waves-effect waves-light btn-edit-user" data-id="{{$data->id}}" data-toggle="tooltip" data-placement="top" title="Sửa thông tin"><i class="fa fa-pencil-square-o cur-point"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="row">
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
                            <div class="pull-right display-flex">
                                <select id="cbbLimit" name="limit" aria-controls="tbl-title" class="form-control input-sm">
                                    {!!$limitSelectbox!!}
                                </select>&nbsp;
                                <?php if (isset($datas)) { ?>
                                    {!!$datas->links()!!}
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

@include('dialog.add_user')
@include('dialog.add_invoice')
@include('dialog.add_bonus')
@include('dialog.add_notify')
@include('dialog.add_bonus_code')
@endsection

@section('script')
<script type="text/javascript">

    function quickLogin() {
        //            window.open('https://dash.360promo.net/login');
        var u = $("#user_name").val();
        var p = $("#password").val();
        let newWindow = open(`https://v21.autolive.me/login?u=${encodeURIComponent(u)}&p=${encodeURIComponent(p)}`, 'example2', 'width=300,height=300');
        newWindow.focus();
    }


    $(".btn-add-bonus-code").click(function () {
        $("#bonus_code_result_table").html("");
        getBonusCodes();
        clearFormBonusCode();
        $('#dialog_add_bonus_code').modal({
            backdrop: false
        });
    });

    $(".btn-save-bonus-code").click(function (e) {
        e.preventDefault();
        var form = $("#formAddBonusCode").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "POST",
            url: "/postBonusCode",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                getBonusCodes();

            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    function getBonusCodes() {
        $("#bonus_code_loading").show();
        $.ajax({
            type: "GET",
            url: "/getBonusCodes",
            data: {

            },
            success: function (data) {
                $("#bonus_code_loading").hide();
                console.log('bonuscode', data);

                var html = `<div class="row">`;
                html += '<div class="col-md-12">';
                html += `<table class="table text-center" style=""><thead><tr>
                <th class='text-center' style='width:5%'>#</th>
                <th class='text-center' style='width:30%'>Code</th>
                <th class='text-center' style='width:10%'>Youtube</th>
                <th class='text-center' style='width:10%'>Tiktok</th>
                <th class='text-center' style='width:10%'>Shopee</th>
                <th class='text-center' style='width:10%'>Start</th>
                <th class='text-center' style='width:10%'>End</th>
                <th class='text-center' style='width:10%'>Used</th>
                <th class='text-center' style='width:10%'>Limit</th>
                <th class='text-center'style='width:5%'>Status</th>
                <th class='text-center'style='width:5%'>Function</th></tr>`;
                var i = 0;
                $.each(data.bonus, function (key, value) {
                    i++;
                    html +=
                            `<tr>
                <td class='text-center'><span class="cur-poiter">${i}</span></td>
                <td class='text-center'>${value.code}</td>
                <td class='text-center'>${value.youtube}/${value.youtube_extra}</td>
                <td class='text-center'>${value.tiktok}/${value.tiktok_extra}</td>
                <td class='text-center'>${value.shopee}/${value.shopee_extra}</td>
                <td class='text-center'>${value.start}</td>
                <td class='text-center'>${value.end}</td>
                <td class='text-center'>${value.used}</td>
                <td class='text-center'>${value.limit}</td>
                <td id="badge_iv_${value.id}" class='text-center'><span class="badge badge-success">Active</span></td>
                <td class='text-center'>
                    <div class="btn-group">
                       <i class="fa fa-ellipsis-v cur-point" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>                                 

                      <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item cur-point" onclick="editBonusCode(${value.id})"><i class="fa fa fa-pencil-square-o"></i> Edit</a>
                        <a class="dropdown-item cur-point" onclick="deleteBonusCode(${value.id})"><i class="fa fa-trash"></i> Delete</a>

                      </div>
                    </div>
                </td></tr> `;
                });
                html += '</table></div></div>';
                $("#bonus_code_result_table").html(html);
                $('[data-toggle="tooltip"]').tooltip();




            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function editBonusCode(id) {
        $.ajax({
            type: "GET",
            url: "/findBonusCode",
            data: {
                id: id
            },
            success: function (data) {
                console.log('findBonusCode', data);
                $("#bonus_code_id").val(data.bonus.id);
                $("#bonus_code").val(data.bonus.code);
                $("#bonus_code_number_days").val(data.bonus.youtube);
                $("#bonus_code_tiktok_days").val(data.bonus.tiktok);
                $("#bonus_code_shopee_days").val(data.bonus.shopee);
                $("#extra_number_days").val(0);
                $("#extra_tiktok_days").val(0);
                $("#extra_shopee_days").val(0);
                if (data.bonus.is_extra) {
                    $("#chk_extra").prop("checked", true).change();
                    $("#extra_number_days").val(data.bonus.youtube_extra);
                    $("#extra_tiktok_days").val(data.bonus.tiktok_extra);
                    $("#extra_shopee_days").val(data.bonus.shopee_extra);
                } else {
                    $("#chk_extra").prop("checked", false).change();
                }
                $("#bonus_limit").val(data.bonus.limit);
                $("#bonus_code_start").val(data.bonus.start);
                $("#bonus_code_end").val(data.bonus.end);
                $([document.documentElement, document.body]).animate({
                    scrollTop: $("#dialog_add_bonus_code").offset().top
                }, 300);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function deleteBonusCode(id) {
        $.ajax({
            type: "GET",
            url: "/deleteBonusCode",
            data: {
                id: id
            },
            success: function (data) {
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                getBonusCodes();
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }

    function clearFormBonusCode() {
        $("#bonus_code_id").val("");
        $("#bonus_code").val("");
        $("#bonus_code_number_days").val(0);
        $("#bonus_code_tiktok_days").val(0);
        $("#bonus_code_shopee_days").val(0);
        $("#extra_number_days").val(0);
        $("#extra_tiktok_days").val(0);
        $("#extra_shopee_days").val(0);
        $("#chk_extra").prop("checked", false).change();
        $("#extra_number_days").val(0);
        $("#extra_tiktok_days").val(0);
        $("#extra_shopee_days").val(0);
        $("#bonus_limit").val(2000);
        $("#bonus_code_start").val("");
        $("#bonus_code_end").val("");
    }

    $(".btn-add-notify").click(function () {
        $('#notify_content').summernote('reset');
        $("#notify_id").val("");
        $('#dialog_notify').modal({
            backdrop: false
        });
    });
    $("#package_code").change(function (e) {
        e.preventDefault();
        $("#number_key_live").val($('option:selected', this).attr('live'));
        $("#number_account").val($('option:selected', this).attr('acc'));
        $("#money").val($('option:selected', this).attr('mo' + $(".month").val()));
    });
    $(".btn-month").click(function () {
        $(".btn-month").removeClass("pricing-box-active");
        $(this).addClass("pricing-box-active");
        var month = $(this).attr('val');
        $(".month").val(month);
        $("#money").val($('option:selected', $("#package_code")).attr('mo' + month));
        //        $("#expire").val(getTimestamp(new Date().getTime() + $(".month").val() * 31 * 86400000));
        //        $(".sub_total").html(number_format($(this).val() * $(".price").val() - re, 0, ',', '.'));
    });
    $(".btn-bonus").click(function (e) {
        e.preventDefault();
        $("#bonus_user_id").val($(this).attr('data-id'));
        $("#bonus_user_name").val($(this).attr('data-us'));
        $("#bonus_user_name_user_name").attr("disabled", true);
        $("#bonus_number_live").val(0);
        $("#bonus_number_account").val(0);
        $("#bonus_number_days").val(0);
        $('#dialog_add_bonus').modal({
            backdrop: false
        });
    });

    $(".btn-save-bonus").click(function (e) {
        e.preventDefault();
        var form = $("#formAddBonus").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }

        $.ajax({
            type: "POST",
            url: "/postBonus",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);

            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    $(".btn-invoice").click(function (e) {
        e.preventDefault();
        $("#invoice_user_id").val($(this).attr('data-id'));
        $("#invoice_user_name").val($(this).attr('data-us'));
        $("#invoice_user_name").attr("disabled", true);
        $("#package_code").val($(this).attr('data-pkg')).change();
        $("#money").val($(this).attr('data-money'));
        $("#number_key_live").val($(this).attr('data-keylive'));
        $("#number_account").val($(this).attr('data-account'));
        var month = $(this).attr('data-month');
        $('.btn-month').removeClass('pricing-box-active');
        $('.btn-month[val="' + month + '"]').addClass('pricing-box-active');
        $("#invoice_month").val(month);
        $(".qr_info").hide();
        $('#radio_ms, #radio_sa').prop('checked', false);
        $('#dialog_add_invoice').modal({
            backdrop: false
        });
    });
    $(".copy-qr").click(function (e) {
//        e.preventDefault();
//            const content = document.getElementById('qr_info');
//
//            // Sử dụng html2canvas để chụp nội dung thẻ
//            html2canvas(content).then(async (canvas) => {
//                // Xuất canvas thành hình ảnh
//                const imgData = canvas.toDataURL('image/png');
//
//                // Chuyển đổi canvas thành Blob
//                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
//
//                // Sao chép ảnh vào clipboard (nếu được trình duyệt hỗ trợ)
//                try {
//                    await navigator.clipboard.write([
//                        new ClipboardItem({ 'image/png': blob })
//                    ]);
//                    $.Notification.autoHideNotify("success", 'top center', 'Notify', "Copied");
//                } catch (error) {
//                    console.error('Không thể sao chép ảnh vào clipboard:', error);
//                }
//
//                // Tải ảnh về máy
//                const link = document.createElement('a');
//                link.href = imgData;
//                link.download = 'capture.png';
//                link.click();
//            });
    });
    


    $(".btn-save-invoice").click(function (e) {
        e.preventDefault();
        var form = $("#formAddInvoice").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $(".qr_info").hide();
        $.ajax({
            type: "POST",
            url: "/postInvoiceAdmin",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                if(data.status=="success" && !$('#auto_approve').prop('checked')){
                    if(data.invoice.bank=='moonshots'){
                        createQr(data.invoice.payment_money, data.invoice.invoice_id, 'canvas_qr_2');
                        $(".sub_total").html(number_format(data.invoice.payment_money, 0, ',', '.'));
                        $(".ivid").html(data.invoice.invoice_id);
                        $(".qr_info").show();
                    }
                }
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    $(".btn-add-user").click(function (e) {
        e.preventDefault();
        clearForm();
        $('#dialog_add_user').modal({
            backdrop: false
        });
    });

    $(".btn-status-user").click(function (e) {
        e.preventDefault();
        var btn = $(this);
        var token = $('input[name="_token"]').val();
        $.ajax({
            type: "POST",
            url: "/updateUser",
            data: {
                'id': btn.attr('data-id'),
                '_token': token
            },
            dataType: 'json',
            success: function (data) {
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                var td = btn.closest("td");
                var html = '';

                if (data.user.status === 2 || data.user.status === 0) {
                    btn.html('<i class="ti-unlock cur-point"></i>');
                    html = '<span class="badge badge-danger zoomIn animated">Inactive</span>';
                } else {
                    btn.html('<i class="ti-lock cur-point"></i>');
                    html = '<span class="badge badge-success zoomIn animated">Active</span>';
                }
                td.prev().html(html);

            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

    });

    $(".btn-edit-user").click(function (e) {
        e.preventDefault();
        $("#dialog_add_user_loading").show();
        $.ajax({
            type: "GET",
            url: "/customer/" + $(this).attr('data-id'),
            data: {},
            dataType: 'json',
            success: function (data) {
                $("#user_id").val(data.id);
                $("#facebook").val(data.facebook);
                $("#user_name").val(data.user_name);
                $("#phone").val(data.phone);
                $("#user_name").attr("disabled", true);
                $("#password").val(data.password_plaintext);
                $("#date_end").val(data.date_end_string);
                //                $("#package").val(data.package_code).change();
                //                $("#number_live").val(data.number_key_live);
                //                $("#number_account").val(data.number_account);
                //                $("#expire").val(getTimestamp(data.package_end_date * 1000));
                var role = data.role.split(",");
                $("#role").val(role);
                $("#des").val(data.description);
                $("#log").val(data.log);
                if (data.is_freezing_youtube) {
                    $("#is_freezing_youtube").prop("checked", true);
                } else {
                    $("#is_freezing_youtube").prop("checked", false);

                }

                if (data.is_freezing_tiktok) {
                    $("#is_freezing_tiktok").prop("checked", true);
                } else {
                    $("#is_freezing_tiktok").prop("checked", false);
                }
                if (data.is_freezing_shopee) {
                    $("#is_freezing_shopee").prop("checked", true);
                } else {
                    $("#is_freezing_shopee").prop("checked", false);
                }

                $("#fr_youtube_detail").html(data.freezing_youtube);
                $("#fr_tiktok_detail").html(data.freezing_tiktok);
                $("#fr_shopee_detail").html(data.freezing_shopee);
                $("#dialog_add_user_loading").hide();

            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        $('#dialog_add_user').modal({
            backdrop: false
        });
    });

    $(".btn-expire").click(function (e) {
        e.preventDefault();
        var date = moment().add($(this).attr('add'), $(this).attr('data-type'));
        $("#expire").val(date.format('MM/DD/YYYY'));
    });

    function clearForm() {
        $("#user_id").val("");
        $("#facebook").val("");
        $("#user_name").val("");
        $("#user_name").attr("disabled", false);
        $("#password").val("");
        //        $("#package").val("LIVETEST").change();
        //        $("#number_live").val("1");
        //        $("#number_account").val("1");
        //        $("#expire").val(getTimestamp(new Date().getTime() + 3 * 86400000));
        $("#role").val([]);
        $("#des").val("");
    }

    $(".btn-save-customer").click(function (e) {
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
            url: "/addOrEditUser",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);

            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    $(".btn-freezing").change(function (e) {
        e.preventDefault();
        var form = $("#formAddCustomer").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        var dataType = $(this).attr("data-type");

        if (this.checked) {
            var value = 1;
        } else {
            var value = 0;
        }
        $.ajax({
            type: "POST",
            url: "/customer/freezing",
            data: {
                _token: '{{csrf_token()}}',
                user_id: $("#user_id").val(),
                platform: dataType,
                value: value
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);

            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

//    //2023/02/26 notify
//    $('#notify_date_start').datetimepicker({
//        //language:  'fr',
//        weekStart: 1,
//        todayBtn: 1,
//        autoclose: true,
//        todayHighlight: 1,
//        startView: 2,
//        forceParse: 0,
//        showMeridian: 0,
//        format: 'mm/dd/yyyy hh:ii'
//    });
//    $('#notify_date_end').datetimepicker({
//        //language:  'fr',
//        weekStart: 1,
//        todayBtn: 1,
//        autoclose: true,
//        todayHighlight: 1,
//        startView: 2,
//        forceParse: 0,
//        showMeridian: 0,
//        format: 'mm/dd/yyyy hh:ii'
//    });

    function editNotify(id) {
        var btn = $("#edit-" + id);
        var loadingText = '<i class="ion-load-c fa-spin"></i>';
        if (btn.html() !== loadingText) {
            btn.data('original-text', btn.html());
            btn.html(loadingText);
        }
        $.ajax({
            type: "GET",
            url: "/notify/" + id,
            data: {},
            dataType: 'json',
            success: function (data) {
                btn.html(btn.data('original-text'));
                console.log(data);
                $("#notify_id").val(id);
//                $('#notify_content').summernote('reset');
//                $('#notify_content').summernote('editor.pasteHTML', data.data.content);

                $("#notify_content").summernote("destroy");
                $("#notify_content").html(data.data.content);
                initSummernote('#notify_content', false,'');

                $("#notify_date_start").val(data.data.start_time_text);
                if (data.data.end_time != 0) {
                    $("#notify_date_end").val(data.data.end_time_text);
                    $("#chk_date_end").attr('checked', true).change();
                } else {
                    ("#chk_date_end").attr('checked', false).change();
                }
                if (data.data.is_maintenance == 1) {
                    $("#is_maintenance").attr('checked', true);
                } else {
                    $("#is_maintenance").attr('checked', false);
                }

            },
            error: function (data) {
                //                    btn.html($this.data('original-text'));
                console.log(data);
            }
        });
    }

    $(".btn-save-notify").click(function (e) {
        e.preventDefault();
        var markup = $('#notify_content').summernote('code');
        $("#notify_content_real").val(markup);
        var form = $("#frmSaveNotify").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        console.log(form);
        $.ajax({
            type: "POST",
            url: "/notify",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
//                console.log(data);
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                if ($("#notify_id").val() == "") {
                    if (data.status == 'success') {
                        $('#table-notify tr:last').after(`
                            <tr><td class="text-center color-red"><b>${data.data.id}</b></td>
                            <td class="text-left text-ellipsis">${data.data.content}</td>
                            <td class="text-center">${data.data.start_time_text}</td>
                            <td class="text-center">${data.data.end_time_text}</td>
                            <td class="text-center">${data.data.type}</td>
                            <td class="text-right">
                                <button id="edit-${data.data.id}" 
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light"
                                                    onclick="editNotify(${data.data.id})"
                                                    data-id="${data.data.id}" 
                                                    data-toggle="tooltip" 
                                                    data-placement="top"
                                                    title="Sửa"><i class="fa fa-edit cur-point"></i></button>
                                <button id="tik-${data.data.id}" class="btn btn-circle btn-dark btn-sm waves-effect waves-light " onclick="deleteNotify(${data.data.id})"
                                                    data-id="${data.data.id}" data-toggle="tooltip" data-placement="top"
                                                    title="Xóa"><i class="fa fa-times-circle cur-point"></i></button>
                            </td></tr>
                            `);
                        $('[data-toggle="tooltip"]').tooltip();
                    }

                }
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });

    function deleteNotify(id) {
        var btn = $("#tik-" + id);
        var loadingText = '<i class="ion-load-c fa-spin"></i>';
        if (btn.html() !== loadingText) {
            btn.data('original-text', btn.html());
            btn.html(loadingText);
        }
        $.ajax({
            type: "PUT",
            url: "/notify",
            data: {
                "id": id,
                "_token": $("input[name=_token]").val()
            },
            dataType: 'json',
            success: function (data) {
                btn.html(btn.data('original-text'));
                if (data.status == "success") {
                    btn.closest("tr").hide();
                }

                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
            },
            error: function (data) {
                //                    btn.html($this.data('original-text'));
                console.log(data);
            }
        });
    }

    $('#notify_content').summernote({
        height: 250, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true                 // set focus to editable area after initializing summernote
    });

    $('.div_scroll_50').slimScroll({
        height: '100vh',
        position: 'right',
        size: "5px",
        color: '#98a6ad',
        wheelStep: 30
    });

    var initSummernote = function (id, airMode, placeholder = 'Description here...') {
        $(id).summernote({
            //        height: 250,
            minHeight: null,
            maxHeight: null,
            focus: false,
            spellCheck: false,
            airMode: airMode,
            placeholder: placeholder,
            dialogsInBody: true,
            tabDisable: false,
            disableAutoParagraph: true,
            cleaner: true,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['link', ['link']],
                ['insert', ['picture']],
                ['view', ['fullscreen']],
            ]
        });
    };
</script>
@endsection