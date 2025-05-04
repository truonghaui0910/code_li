<div class="modal fade " id="dialog_add_invoice" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-credit-card fa-fw"></i></span> <span>Add Invoice</span></h4>                        
                </div>
                <div id="dialog_add_invoice_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formAddInvoice' method="POST" spellcheck="false">
                        <input type="hidden" id="invoice_user_id" name="invoice_user_id" />
                        <input id="invoice_month" type="hidden" name="month" value="1" class="month" />
                        {{ csrf_field() }}
                        <div class="row">


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">Username</label>
                                    <input type="text" id="invoice_user_name" name="user_name" class=" form-control" value="" >
                                </div>    
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="package">Package</label>
                                    <select id="package_code" name="package_code" class="select2_multiple form-control">
                                        {!!$packages!!}
                                    </select>  
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="money">Money</label>
                                    <input type="text" id="money" class=" form-control mark-number" name="money" value="200000" >
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="number_key_live">Number Live</label>
                                    <input type="number" id="number_key_live" class=" form-control" name="number_key_live" value="1" >
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="number_account">Number Account</label>
                                    <input type="number" id="number_account" class=" form-control" name="number_account" value="1" >
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <br>
                                    <span type="button" class="btn-month btn-icon cur-point m-r-15 pricing-box-active" val="1">1 Tháng</span>
                                    <span type="button" class="btn-month btn-icon cur-point m-r-15" val="3">3 Tháng</span>
                                    <span type="button" class="btn-month btn-icon cur-point m-r-15" val="6">6 Tháng</span>
                                    <span type="button" class="btn-month btn-icon cur-point m-r-15" val="12">12 Tháng</span>
                                </div>
                                
                            </div>
<!--                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="refun">Tiền còn lại</label>
                                    <input type="text" id="refun" name="refun" class=" form-control" value="" >
                                </div>    
                            </div>-->
                            <div class="col-md-12">
                                <br>
                                <div class="checkbox">
                                    <input id="auto_approve" type="checkbox" name="auto_approve" >
                                    <label for="auto_approve">
                                        Auto approve
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <!--<label class="col-6 col-form-label">Bank</label>-->
                                    <div class="col-12 p-t-5 p-l-16">
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="radio_ms" value="moonshots" name="radio_bank">
                                            <label for="radio_ms"> Moonshots </label>
                                        </div>
                                        <div class="radio form-check-inline">
                                            <input type="radio" id="radio_sa" value="sang_acb" name="radio_bank">
                                            <label for="radio_sa"> Sáng Bank </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        
   
                            
                        <fieldset class="qr_info">
                            <legend>
                                <div class="legend-container">

                                    <div class="icons-container">
                                        <i onclick="copyDownloadImg('qr_info','download')" class="font-16 fa fa-download cur-point float-right qr_info"  data-toggle="tooltip" data-placement="top" title="Download QR"></i>
                                        <i onclick="copyDownloadImg('qr_info','copy')" class="font-16 m-r-5 fa fa-copy cur-point float-right qr_info"  data-toggle="tooltip" data-placement="top" title="Copy QR"></i>
                                    </div>
                                </div>
                            </legend>
                            <div id="qr_info" class="row qr_info">
                                <div class="col-md-4">
                                    <div class="confirm-qr" style="background: transparent">
                                    <!--<img class="qr w-100 h-100" src="/images/sang_qr2.jpg"/>-->
                                    <canvas id="canvas_qr_2" class="qr w-100 h-100" ></canvas>
                                    <span style="font-size: 16px"><b><span class="sub_total color-green"></span></b></span>

                                    </div>
                                </div>
                                <div class="col-md-8 m-t-10">

                                        <div class="row">
                                            <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Số tài khoản</span> <span class="color-green font-16 font-bold">393982668</span></div>
                                            <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Chủ tài khoản</span> <span class="color-green font-16 font-bold">CTY CO PHAN AM NHAC VA CONG NGHE MOONSHOTS</span></div>
                                            <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Ngân hàng Á Châu </span> <span><img style="height: 20px" src="images/ACB.png"></span></div>
                                            <div class="col-md-12 m-b-15"><span class="font-16 m-r-15">Nội dung</span> <span class="ivid color-green font-16 font-bold"></span></div>
                                        </div>
                                </div>
                            </div>
                    </fieldset>
                       
                            
                        
                        <br>
                        <button class="btn btn-primary waves-effect waves-light w-md btn-sm float-right btn-save-invoice" type="button" >Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>