<div class="modal fade " id="dialog_use_bonus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-gift fa-fw"></i></span> <span>Nhập mã tặng thưởng</span></h4>                        
                </div>
                <div id="dialog_vip_add_user_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formUseBonus' method="POST" spellcheck="false">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="bonus_code">Mã thưởng<span class="color-red">*</span></label>
                                    <input type="text" id="bonus_code" name="bonus_code" class=" form-control" value="">
                                </div>    
                            </div>
                        </div>
                        <button class="btn btn-violet waves-effect waves-light btn-sm float-right btn-use-bonus " type="button" >Nhận Thưởng</button>
                        <button data-dismiss="modal" class="btn btn-danger waves-effect waves-light  btn-sm float-right mr-2" type="button" >Đóng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>