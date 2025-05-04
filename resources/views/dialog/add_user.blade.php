<div class="modal fade " id="dialog_add_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-user fa-fw"></i></span> <span id="title-brand">User</span></h4>                        
                </div>
                <div id="dialog_add_user_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formAddCustomer' method="POST" spellcheck="false">
                        <input type="hidden" id="user_id" name="user_id" />
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="facebook">Facebook </label>
                                    <input type="text" id="facebook" name='facebook' class=" form-control form-control-warning" placeholder="" value="">
                                </div>    
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">Username <span class="color-red">*</span></label>
                                    <input type="text" id="user_name" name="user_name" class=" form-control" value="">
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">Phone</label>
                                    <input type="number" id="phone" name="phone" class=" form-control" value="">
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Password <span class="color-red">*</span></label>
                                    <input type="text" id="password" name="password" class=" form-control" value="">
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="date_end">Date End</label>
                                    <input type="text" id="date_end" name="date_end" class=" form-control" value="" data-mask="9999/99/99 99:99:99">
                                </div>    
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Roles</label>
                                    <select id="role" style="height: 76px;overflow: hidden" name="role[]" class="select2_multiple form-control" multiple="multiple">
                                        {!!$role!!}
                                    </select>
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <input id="is_freezing_youtube" class="btn-freezing" data-type="1" type="checkbox" name="is_freezing_youtube" value="1">
                                    <label for="is_freezing_youtube">
                                        Phong Ấn Youtube <span id="fr_youtube_detail"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <input id="is_freezing_tiktok" class="btn-freezing" data-type="2" type="checkbox" name="is_freezing_tiktok" value="1">
                                    <label for="is_freezing_tiktok">
                                        Phong Ấn Tiktok <span id="fr_tiktok_detail"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <input id="is_freezing_shopee" class="btn-freezing" data-type="3" type="checkbox" name="is_freezing_shopee" value="1">
                                    <label for="is_freezing_shopee">
                                        Phong Ấn Shopee <span id="fr_shopee_detail"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="desc">Description</label>
                                    <textarea style="height: 125px" id="des" name="des" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="log">Log</label>
                                    <textarea style="height: 125px" id="log" name="log" class="form-control"></textarea>
                                </div>
                            </div>
                            <br>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-info color-g btn-sm btn-quick-login" onclick="quickLogin()"><i
                                            class="fa fa-share-alt"></i> Login</button>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="form-group">
                                    <button class="btn btn-outline-violet waves-effect w-md float-right btn-sm btn-save-customer" type="button" >Save</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>