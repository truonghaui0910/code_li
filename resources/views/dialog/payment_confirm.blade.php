<div class="modal fade" id="dialog_confirm_payment" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15" >
                <div class="pa-10 modal-body ">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="confirm-qr">
                            <!--<img class="qr w-100 h-100" src="/images/sang_qr2.jpg"/>-->
                            <canvas id="canvas_qr_2" class="qr w-100 h-100" ></canvas>
                            <span style="font-size: 23px">Tổng cộng : <b><span class="sub_total color-green">{{number_format($subTotal - ($subTotal * $package->discount_per /100), 0, ',', '.')}}</span></b></span>
                                
                            </div>
                        </div>
                        <div class="col-md-8 m-t-30">
                            <h4>Quét mã QR để thanh toán hoặc chuyển khoản tới tài khoản</h4>
                                <div class="row">
                                    <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Số tài khoản</span> <span class="color-green font-18 font-bold">393982668</span> <i data-toggle="tooltip" data-placement="top" title="Copy" class="fa fa-copy color-green" onclick="copyText('phone-number')"></i></div>
                                    <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Chủ tài khoản</span> <span class="color-green font-18 font-bold">CTY CO PHAN AM NHAC VA CONG NGHE MOONSHOTS</span></div>
                                    <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Ngân hàng Á Châu </span> <span><img style="height: 20px" src="images/ACB.png"></span></div>
                                    <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Nội dung</span> <span class="color-green font-18 font-bold"><span class="ivid">{{$invoiceId}}</span> <i data-toggle="tooltip" data-placement="top" title="Copy" class="fa fa-copy color-green" onclick="copyText('iv')"></i></span></div>
                                </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-md-12 text-right">
                        <button id="btn-saveInvoice" type="button" class="btn btn-primary waves-effect waves-light">Xác nhận đã chuyển khoản</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>