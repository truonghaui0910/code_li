<div class="modal fade " id="dialog_add_cost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="dialog-icon"><i class="fa fa-user fa-fw"></i></span> <span>Thêm cost</span></h4>                        
                </div>
                <div id="dialog_add_cost_loading" class="disp-none" style="text-align: center;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <form id='formAddCost' method="POST" spellcheck="false">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="period">Period <span class="color-red">*</span></label>
                                    <input id="period" name="period" class="form-control" type="text" data-mask="9999/99" placeholder="YYYY/MM">
                                </div>    
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="money">Số tiền</label>
                                    <input id="money" type="text" name="money" class="form-control mark-number" />
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="desc">Note</label>
                                    <textarea style="height: 125px" id="note" name="note" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>
                        <button class="btn btn-primary waves-effect waves-light w-md btn-sm float-right btn-save-cost" type="button" >Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>