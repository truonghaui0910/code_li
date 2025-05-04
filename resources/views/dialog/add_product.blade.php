<div class="modal fade " id="dialog_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom cur-point" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" data-placement="top" data-html="true" title="Đóng" style="top:11px">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-shopping-cart"></i></span> <span id="dialog_product_title"></span></h4>                        
                    <!--<i class="fa fa-cogs cur-point m-r-10" onclick="configPin()" data-toggle="tooltip" data-placement="top" data-html="true" title="Cấu hình pin sản phầm"></i>-->
                </div>
                <!--<div id="dialog_product_loading" style="text-align: center;display: none"><i class="fa fa-spinner fa-spin"></i> Loading...</div>-->
                <div class="pa-10 modal-body ">
                    <div class="content-notify">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmProduct">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="live_id" name="live_id"/>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Link Sản Phẩm <small>(Có thể nhập nhiều link, phân cách nhau bởi dấu xuống dòng)</small></label> 
                                                <div class="col-12">
                                                    <textarea id="product_link" class="form-control " name="product_link" rows="5" spellcheck="false"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button id="save-product" type="button" class="btn btn-dark waves-effect waves-light" onclick="addProduct()"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Thêm sản phầm vào luồng live"><i class="fa fa-save cur-point"></i> Lưu lại</button>
                                </form>
                            </div>
                            <div id="result-add" class="col-md-12 m-t-10">

                            </div>
                        </div>
                        <hr>
                        <table class="w-100">
                            <tr>
                                <td>
                                    <label class="col-12 col-form-label">Danh sách sản phẩm</label> 
                                </td>
                                <td class="pull-right">
                                    <button id="save-product" type="button" class="btn btn-sm btn-dark waves-effect waves-light" onclick="configPin()"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Cấu hình pin sản phầm"><i class="fa fa-cogs cur-point m-r-10"></i> Cấu hình pin sản phẩm</button></td>
                            </tr>
                        </table>
                        
                        <div id="dialog_product_loading" style="text-align: center;display: none"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                        <div class="port m-b-20">
                            <div class="portfolioContainer">
                                <div id="product_data" class="row">
                                    <!--                                    <div class="col-md-4 webdesign illustrator ">
                                                                            <div class="gal-detail thumb m-t-10 pos">
                                                                                <span class="float-right" data-toggle="tooltip" data-placement="top" title="Xóa" onclick="delProduct(1)"><i class="fa fa-times-circle cur-point"></i></span>
                                                                                <a target="_blank" href="" class="image-popup">
                                                                                    <img src="thub" class="thumb-img" alt="work-thumbnail">
                                                                                </a>
                                                                                <h4 class="text-center">gia</h4>
                                                                                <div class="ga-border"></div>
                                                                                <p class="text-muted text-center"><small>title</small></p>
                                                                            </div>
                                                                        </div>-->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>