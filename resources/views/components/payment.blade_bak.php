@extends('layouts.master')

@section('content')
@if($isOverLive)
<div class="row animated fadeInDown">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0 color-violet">Thông báo</h4>
            <div class="pa-20 color-violet" style="font-weight: 500;">
                <p>Bạn có {{$numberLiving}} luồng đang live, hãy dừng {{$stopLive}} luồng trước khi mua gói {{$package->package_code}}</p>
            </div>
        </div>
    </div>
</div>
@endif
<div class="row animated fadeInDown">
    <div class="col-12">
        <div class="card-box m-b-10">
            <div class="panel-body">
                <div class="clearfix">
                    <div class="pull-left">
                        <h3 class="text-right"><i class=" ti-credit-card"></i> Hóa Đơn</h3>
                    </div>
                </div>
                <!--<hr>-->
                <div class="row">
                    <div class="col-md-6 pull-left m-t-30">
                        <address>
                            <p class="m-t-10"><strong>Tài khoản:</strong> {{$user_login->user_name}}</p>
                            <p class="m-t-10"><strong>Facebook:</strong> {{$user_login->facebook}}</p>
                            <p class="m-t-10"><strong>Gói cước hiện tại:</strong> {{$currentPackage}}</p>
                            <p class="m-t-10"><strong>Hạn dùng: </strong> {{\App\Common\Utils::countDayLeft($currenDateEnd)}}</p>
                        </address>
                    </div>
                    <div class="col-md-6 pull-right m-t-30 text-right">
<!--                            <p><strong> Ngày tạo: </strong> {{$startDate}}</p>
                        <p><strong>Hạn thanh toán: </strong> {{$duaDate}}</p>
                        <p class="m-t-10"><strong>Trạng thái: </strong> <span class="badge badge-danger">Chưa giải quyết</span></p>
                        <p class="m-t-10"><strong>Mã Hóa đơn: </strong> {{$invoiceId}}</p>-->


                        <p class="m-t-10"><strong>Mã hóa đơn: </strong> {{$invoiceId}}</p>
                        <p class="m-t-10"><strong> Ngày tạo: </strong> {{$startDate}}</p>
                        <p class="m-t-10"><strong> Gói cước: </strong> {{$package->package_code}}</p>
                        <p class="m-t-10"><strong>Ngày hết hạn: </strong> <span class="expire-date">{{gmdate('Y/m/d H:i:s',$packageEndDate + $user_login->timezone * 3600)}}</span></p>
                        <p class="payment-pending m-t-10 more"><strong>Trạng thái: </strong> <span class="badge badge-warning"><i class="ion-load-c fa-spin"></i> Đang chờ xử lý</span></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table>
                                <tr>
                                    <td><button type="button" class="btn-month-payment btn-icon cur-point m-r-15 pa-20 btn-month radius35 pricing-box-active" value="1">
                                            <div><span>Mua 1 Tháng</span></div><div>
                                                <div><span class="text-line-through color-red">{{number_format($package->number_live * $basePrice, 0, ',', '.')}}</span></div>
                                                <span><h4>{{number_format($package->price, 0, ',', '.')}}</h4></span></div>
                                        </button>
                                    </td>
                                    <td><button type="button" class="btn-month-payment btn-icon cur-point m-r-15 pa-20 btn-month radius35 " value="3">
                                            <div><span>Mua 3 Tháng</span></div>
                                            <div><span class="text-line-through color-red">{{number_format($package->number_live * $basePrice * 3, 0, ',', '.')}}</span></div>
                                            <div>
                                                <span><h4>{{number_format($package->price *3 - $package->discount_3, 0, ',', '.')}}</h4></span>
                                                <span>Tiết kiệm</span><span class="font-20"> -{{$package->discount_3_per}}%</span>
                                            </div>
                                        </button>
                                    </td>
                                    <td><button type="button" class="btn-month-payment btn-icon cur-point m-r-15 pa-20 btn-month radius35" value="6">
                                            <div><span>Mua 6 Tháng</span></div>
                                            <div><span class="text-line-through color-red">{{number_format($package->number_live * $basePrice * 6, 0, ',', '.')}}</span></div>
                                            <div>
                                                <span ><h4>{{number_format($package->price *6 - $package->discount_6, 0, ',', '.')}}</h4></span>
                                                <span>Tiết kiệm</span><span class="font-20"> -{{$package->discount_6_per}}%</span>
                                            </div>
                                        </button></td>
                                    <td><button type="button" class="btn-month-payment btn-icon cur-point m-r-15 pa-20 btn-month radius35" value="12">
                                            <div><span>Mua 12 Tháng</span></div>
                                            <div><span class="text-line-through color-red">{{number_format($package->number_live * $basePrice * 12, 0, ',', '.')}}</span></div>
                                            <div>
                                                <span ><h4>{{number_format($package->price *12 - $package->discount_12, 0, ',', '.')}}</h4></span>
                                                <span>Tiết kiệm</span><span class="font-20"> -{{$package->discount_12_per}}%</span>
                                            </div>
                                        </button></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!--                <div class="row m-t-30">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="voucher" id="voucher" placeholder="Mã voucher">
                                    </div>
                                    <div class="col-md-2">
                                        <button id="btn-voucher" type="button" class="btn btn-primary waves-effect waves-light">Sử dụng</button>
                                    </div>
                                </div>-->
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table m-t-30">
                                <thead>
                                    <tr><th>#</th>
                                        <th>Sản phẩm</th>
                                        <th style="text-align: center">Số tháng</th>
                                        <!--<th style="text-align: center">Giá gốc</th>-->
                                        <th style="text-align: center">Giá niêm yết</th>
                                        <th style="text-align: center">Giảm giá</th>
                                        <th style="text-align: center">Ngày còn lại</th>
                                        <th style="text-align: left">Mô tả</th>
                                        <th style="text-align: center">Tổng cộng</th>
                                    </tr></thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>{{$package->package_code}}</td>
                                        <td style="text-align: center" class="month_text">1</td>
                                        <!--<td style="text-align: center" class="text-line-through color-red">{{number_format($package->number_live *200000, 0, ',', '.')}}</td>-->
                                        <td style="text-align: center">{{number_format($package->price, 0, ',', '.')}}</td>
                                        <td style="text-align: center" class="discount color-red font-bold">@if($package->discount_per>0) {{number_format($package->price * $package->discount_per /100, 0, ',', '.')}} ({{$package->discount_per}}%) @endif</td>
                                        <td style="text-align: center" class="date_remain">{{$dateRemain}}</td>
                                        <td class="date_remain_text">
                                            <?php
                                            if ($dateRemain != 0) {
                                                echo "Cộng " . $dateRemain . " ngày,tính từ số ngày còn lại của gói cũ";
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align: center" class="sub_total">{{number_format($subTotal - ($subTotal * $package->discount_per /100), 0, ',', '.')}}</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <p>Bạn hãy chuyển tiền tới tài khoản sau với nội dung chuyển khoản là: <span class="color-red"><b>{{$invoiceId}}</b> <i data-toggle="tooltip" data-placement="top" title="Copy" class="fa fa-copy color-green" onclick="copyText('iv')"></i></span></p>
                    </div>

                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-md-2 ">
                                <!--<img  data-toggle="tooltip" data-placement="top" title="Bấm vào để phóng to" class="qr" src="/images/sang_qr2.jpg"/>-->
                                <canvas id="canvas_qr" class="qr" ></canvas>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Số tài khoản</span> <span class="color-green font-18 font-bold">996688688</span></div>
                                    <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Chủ tài khoản</span> <span class="color-green font-18 font-bold">Nguyễn Văn Sáng</span></div>
                                    <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Ngân hàng Á Châu </span> <span><img style="height: 20px" src="images/ACB.png"></span></div>
                                </div>
                            </div>              
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-no-padding" style="overflow: auto;">
                        <p class="text-muted well well-sm no-shadow color-red" style="margin-top: 10px;font-weight: bold;font-size: 16px;">
                            Lưu ý, sau khi chuyển tiền thành công thì mới ấn nút "Xác nhận chuyển tiền", nếu sau 10p mà chưa vào được hệ thống thì chụp ảnh Hóa đơn gửi facebook cho <a href="https://www.facebook.com/gaumeo.hp">Admin</a>
                        </p>
                    </div>
                </div>

                <div class="d-print-none">
                    <div class="text-right">
                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                        <button id="btn-confirm-payment" type="button" class="btn btn-primary waves-effect waves-light" <?php echo $isOverLive ? "disabled" : ""; ?>>Thanh toán</button>
                    </div>
                </div>
                <form id="form-invoice">
                    <input type="hidden" name="invoiceId" id="iv"value="{{$invoiceId}}"/>
                    <input type="hidden" name="month" value="1" class="month" />
                    <input type="hidden" name="price" id="pr" value="{{$package->price}}" class="price" />
                    <input type="hidden" name="package_code" value="{{$package->package_code}}"/>
                    <input type="hidden" name ='_token' value='{{csrf_token()}}'/>
                    <input type="hidden" id="phone-number" value="996688688"/>
                </form>
            </div>
        </div>
    </div>
</div>

@include('dialog.qr')
@include('dialog.payment_confirm')
@endsection

@section('script')
<script type="text/javascript">
    var price = $("#pr").val();
    var invoice = $("#iv").val();
    var subtotal = price;
    createQr(price, invoice, 'canvas_qr');
    $(".btn-month-payment").click(function () {
        $(".btn-month").removeClass("pricing-box-active");
        $(this).addClass("pricing-box-active");
        $(".month").val($(this).val());
        var remain = <?php echo $dateRemain; ?>;
        var remainText = '<?php echo "Cộng " . $dateRemain . " ngày,tính từ số ngày còn lại của gói cũ"; ?>';
        if (remain > 0) {
            remainText = '<?php echo "Cộng " . $dateRemain . " ngày,tính từ số ngày còn lại của gói cũ"; ?>';
        } else {
            remainText = '';
        }
        var dis = 0;
        var discount = <?php echo $package->discount_per; ?>;
        var expire = '<?php echo gmdate('Y/m/d H:i:s', $packageEndDate + $user_login->timezone * 3600); ?>';
        if ($(this).val() == 3) {
            dis = <?php echo $dis3; ?>;
            remain = <?php echo $dateRemain3; ?>;
            if (remain > 0) {
                remainText = '<?php echo "Cộng " . $dateRemain3 . " ngày,tính từ số ngày còn lại của gói cũ"; ?>';
            }
            expire = '<?php echo gmdate('Y/m/d H:i:s', $packageEndDate + 2 * 31 * 86400 + $user_login->timezone * 3600); ?>';
        } else if ($(this).val() == 6) {
            dis = <?php echo $dis6; ?>;
            remain = <?php echo $dateRemain6; ?>;
            if (remain > 0) {
                remainText = '<?php echo "Cộng " . $dateRemain6 . " ngày,tính từ số ngày còn lại của gói cũ"; ?>';
            }
            expire = '<?php echo gmdate('Y/m/d H:i:s', $packageEndDate + 5 * 31 * 86400 + $user_login->timezone * 3600); ?>';
        } else if ($(this).val() == 12) {
            dis = <?php echo $dis12; ?>;
            remain = <?php echo $dateRemain12; ?>;
            if (remain > 0) {
                remainText = '<?php echo "Cộng " . $dateRemain12 . " ngày,tính từ số ngày còn lại của gói cũ"; ?>';
            }
            expire = '<?php echo gmdate('Y/m/d H:i:s', $packageEndDate + 11 * 31 * 86400 + $user_login->timezone * 3600); ?>';
        }
        var total = $(this).val() * $(".price").val();
        subtotal = total - dis - ((total - dis) * discount / 100);
        $(".month_text").html($(this).val());
        $(".sub_total").html(number_format(total - dis - ((total - dis) * discount / 100)), 0, ',', '.');
        $('.expire-date').html(expire);
        $('.date_remain').html(remain);
        $('.date_remain_text').html(remainText);
        if (discount > 0) {
            $('.discount').html(number_format((total - dis) * discount / 100, 0, ',', '.') + ` (${discount}%)`);
        }
        createQr(subtotal, invoice, 'canvas_qr');
    });

    $("#btn-saveInvoice").click(function (e) {
        e.preventDefault();
        var form = $("#form-invoice");
        var formData = form.serialize();

        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "POST",
            url: "/postInvoice",
            data: formData,
            dataType: 'json',
            success: function (data) {
//                console.log(data);
                $this.html($this.data('original-text'));
                $this.attr("disabled", true);
                $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
//                setTimeout(function(){redirectProfile();}, 3000);
                $('#dialog_confirm_payment').modal('toggle');
                $('.payment-pending').show();
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });

        function redirectProfile() {
            window.location.href = "/profile";
        }
    });
    $("#btn-confirm-payment").click(function (e) {
        e.preventDefault();
        createQr(subtotal, invoice, 'canvas_qr_2');
        $('#dialog_confirm_payment').modal({
            backdrop: true
        });
    });
    $(".qr").click(function (e) {
        e.preventDefault();
        $('#dialog_qr').modal({
            backdrop: true
        });
    });
    function copyText(id) {
        navigator.clipboard.writeText($("#" + id).val());
        $.Notification.notify('success', 'top center', 'Notification', 'Đã copy: ' + $("#" + id).val());
    }
</script>
@endsection

