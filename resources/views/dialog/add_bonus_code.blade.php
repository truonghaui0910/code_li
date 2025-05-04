<div class="modal fade" id="dialog_add_bonus_code" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-60">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-gift fa-fw"></i></span> <span>Bonus Code</span></h4>                        
                </div>
                <div id="dialog_add_bonus_code_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formAddBonusCode' method="POST" spellcheck="false">
                        {{ csrf_field() }}
                        <input type="hidden" id="bonus_code_id" name="bonus_code_id"/>
                        <div class="row">


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="code">Bonus Code</label>
                                    <input type="text" id="bonus_code" name="bonus_code" class=" form-control" value="" >
                                </div>    
                            </div>



                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bonus_code_number_days">Youtube Days</label>
                                    <input type="number" id="bonus_code_number_days" name="number_days" class=" form-control" value="0">
                                </div>    
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bonus_code_tiktok_days">Tiktok Days</label>
                                    <input type="number" id="bonus_code_tiktok_days" name="tiktok_days" class=" form-control" value="0">
                                </div>    
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bonus_code_shopee_days">Shopee Days</label>
                                    <input type="number" id="bonus_code_shopee_days" name="shopee_days" class=" form-control" value="0">
                                </div>    
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="checkbox">
                                            <input id="chk_extra" type="checkbox" name="chk_extra" value="1">
                                            <label for="chk_extra">
                                                Tặng thêm ngày khi mua gói
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row div_chk_extra disp-none">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="extra_number_days">Extra Youtube Days</label>
                                    <input id="extra_number_days" type="number" name="extra_number_days" class=" form-control" value="0">
                                </div>    
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="extra_tiktok_days">Extra Tiktol Days</label>
                                    <input id="extra_tiktok_days" type="number" name="extra_tiktok_days" class=" form-control" value="0">
                                </div>    
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="extra_shopee_days">Extra Shopee Days</label>
                                    <input id="extra_shopee_days" type="number" name="extra_shopee_days" class=" form-control" value="0">
                                </div>    
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label>Start</label>
                                        <input type="datetime-local" id="bonus_code_start" class="form-control" name="bonus_code_start"
                                               placeholder="Thời gian bắt đầu">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label>End</label>
                                        <input type="datetime-local" id="bonus_code_end" class="form-control" name="bonus_code_end"
                                               placeholder="Thời gian kết thúc">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="code">Limit</label>
                                    <input type="number" id="bonus_limit" name="bonus_limit" class=" form-control" value="2000" >
                                </div>    
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-outline-violet waves-effect waves-light w-md btn-sm float-right btn-save-bonus-code" type="button" >Save</button>
                            </div>
                        </div>
                        <br>
                    </form>

                    <div class="row">
                        <div id="bonus_code_loading" class="mx-auto disp-none"><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</div>
                    </div>
                    <div id="bonus_code_result_table" class="table-responsive">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>