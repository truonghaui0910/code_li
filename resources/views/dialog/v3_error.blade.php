<div class="modal fade " id="dialog_v3_error" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span>Error</span></h4>                        
                </div>

                <div class="pa-10 modal-body ">
                    <form id='formV3Error' method="POST" spellcheck="false">
                        <input type="hidden" id="profile_id" name="profile_id" />
                        {{ csrf_field() }}
                        <div class="row">


<!--                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Error</label>
                                    <input type="text" id="text_error" name="text_error" class=" form-control" value="" >

                                </div>    
                            </div>-->
                            <div class="col-md-12">
                                <div class="form-group position-relative">
                                    <input type="text" class="form-control" id="countryInput"  autocomplete="off">
                                    <div class="dropdown-menu w-100" id="countryDropdown"></div>
                                </div>
                            </div>


                        </div>
                        <button onclick="saveEror()" class="btn btn-outline-violet waves-effect waves-light w-md btn-sm float-right " type="button" >Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>