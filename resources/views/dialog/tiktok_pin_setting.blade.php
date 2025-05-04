<div class="modal fade " id="dialog_setting_pin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-cogs fa-fw"></i></span> <span>Cài đặt Pin sản phẩm</span></h4>                        
                </div>
                
                <div class="pa-10 modal-body ">
                    <form id='formAddPinSetting' method="POST" spellcheck="false">
                        <input type="hidden" id="job_id" name="job_id" />
                        <input type="hidden" id="product_ids" name="product_ids"  />
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="checkbox">
                                            <input id="is_auto_pin" type="checkbox" name="is_auto_pin">
                                            <label for="is_auto_pin">
                                               Tự động PIN sản phẩm
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                        <div class="div_is_auto_pin disp-none">
                                <div class="form-group">
                                    <label for="minute_pin">Tự động pin mỗi</label>
                                    <input type="text" id="minute_pin" name="minute_pin" class=" form-control"
                                           data-bts-button-down-class="btn btn-dark" 
                                           data-bts-button-up-class="btn btn-dark">
                                </div>    
                            </div>
                        </div>
                        </div>
                        
                                <button id="save-setting" type="button" class="btn btn-sm btn-dark waves-effect waves-light" onclick="savePinSetting()"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Lưu lại"><i class="fa fa-save cur-point"></i> Lưu lại</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>