<div class="modal fade " id="dialog_vip_add_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-user fa-fw"></i></span> <span>Thêm tài khoản con</span></h4>                        
                </div>
                <div id="dialog_vip_add_user_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formVipAddUser' method="POST" spellcheck="false">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">Tài khoản <span class="color-red">*</span></label>
                                    <input type="text" id="vip_user_name" name="user_name" class=" form-control" value="">
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Mật khẩu <span class="color-red">*</span></label>
                                    <input type="text" id="vip_password" name="password" class=" form-control" value="">
                                </div>    
                            </div>   
                        </div>
                        <button class="btn btn-outline-violet waves-effect waves-light w-md btn-sm float-right btn-vip-save-user" type="button" >Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>