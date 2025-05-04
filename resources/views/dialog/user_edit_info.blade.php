<div class="modal fade " id="dialog_user_edit_info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-user fa-fw"></i></span> <span id="title-brand">Sửa thông tin tài khoản</span></h4>                        
                </div>
                <div id="dialog_add_user_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formAddCustomer' method="POST" spellcheck="false">
                        <input type="hidden" id="user_id" name="user_id" />
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">Tài khoản</label>
                                    <input type="text" id="user_name" name="user_name" disabled class=" form-control" value="{{$user_login->user_name}}">
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="facebook">Facebook </label>
                                    <input type="text" id="facebook" name='facebook' class=" form-control form-control-warning" placeholder="" value="{{$user_login->facebook}}">
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone </label>
                                    <input type="text" id="phone" name='phone' class=" form-control form-control-warning" placeholder="" value="{{$user_login->phone}}">
                                </div>    
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Mật khẩu <span class="color-red">*</span></label>
                                    <input type="text" id="password" name="password" class=" form-control" value="">
                                </div>    
                            </div>

                            <div class="col-md-12">
                                <br>
                                <div class="checkbox">
                                    <input id="change_pass" type="checkbox" name="change_pass">
                                    <label for="change_pass">
                                        Đổi mật khẩu
                                    </label>
                                </div>
                            </div>
                            <div class="w-100 div_change_pass disp-none">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password_new">Mật khẩu mới <span class="color-red">*</span></label>
                                        <input type="text" id="password_new" name="password_new" class=" form-control" value="">
                                    </div>    
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password_new_confirm">Xác nhận mật khẩu mới<span class="color-red">*</span></label>
                                        <input type="text" id="password_new_confirm" name="password_new_confirm" class=" form-control" value="">
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-outline-violet waves-effect w-md float-right btn-sm btn-save-info" type="button" >Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>