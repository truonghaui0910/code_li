<div class="modal fade " id="dialog_add_bonus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-gift fa-fw"></i></span> <span>Bonus</span></h4>                        
                </div>
                <div id="dialog_add_bonus_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formAddBonus' method="POST" spellcheck="false">
                        <input type="hidden" id="bonus_user_id" name="bonus_user_id" />
                        <input type="hidden" name="month" value="1" class="month" />
                        {{ csrf_field() }}
                        <div class="row">


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_name">Username</label>
                                    <input type="text" id="bonus_user_name" name="bonus_user_name" class=" form-control" value="" >
                                </div>    
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus_number_live">Youtube Live</label>
                                    <input id="bonus_number_live" type="number" name="number_live" class=" form-control" value="0">
                                </div>    
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus_number_days">Youtube Days</label>
                                    <input type="number" id="bonus_number_days" name="number_days" class=" form-control" value="0">
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus_number_live">Tiktok Live</label>
                                    <input id="bonus_tiktok_live" type="number" name="tiktok_live" class=" form-control" value="0">
                                </div>    
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus_tiktok_days">Tiktok Days</label>
                                    <input type="number" id="bonus_tiktok_days" name="tiktok_days" class=" form-control" value="0">
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus_number_live">Shopee Live</label>
                                    <input id="bonus_shopee_live" type="number" name="shopee_live" class=" form-control" value="0">
                                </div>    
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus_shopee_days">Shopee Days</label>
                                    <input type="number" id="bonus_shopee_days" name="shopee_days" class=" form-control" value="0">
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonus_number_account">Number Account</label>
                                    <input type="number" id="bonus_number_account" name="number_account" class=" form-control" value="0">
                                </div>    
                            </div>
                        </div>
                        <button class="btn btn-outline-violet waves-effect waves-light w-md btn-sm float-right btn-save-bonus" type="button" >Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>