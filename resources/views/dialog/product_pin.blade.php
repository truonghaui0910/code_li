<div class="modal fade" id="dialog_pin_config" tabindex="-1" role="dialog" aria-labelledby="pinConfigModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom cur-point" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" data-placement="top" data-html="true" title="Đóng" style="top:11px">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-clock"></i></span> <span id="dialog_pin_config_title">Cấu hình thời gian pin sản phẩm</span></h4>
                </div>
                <div class="pa-10 modal-body">
                    <div class="content-notify">
                        <form id="frmPinConfig">
                            {{ csrf_field() }}
                            <input type="hidden" id="pin_config_profile_id" name="profile_id" value=""/>
                            
                            <div class="form-group">
                                <label>Chọn bộ sản phẩm:</label>
                                <select id="select_product_set" class="form-control" name="product_set_id">
                                    <option value="">-- Chọn bộ sản phẩm --</option>
                                    <!-- Options sẽ được thêm vào bằng JavaScript -->
                                </select>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="pin_type_interval" name="pin_type" class="custom-control-input" value="interval" checked>
                                        <label class="custom-control-label" for="pin_type_interval">Kiểu 1: Pin theo khoảng thời gian</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="pin_type_specific" name="pin_type" class="custom-control-input" value="specific">
                                        <label class="custom-control-label" for="pin_type_specific">Kiểu 2: Pin vào thời điểm cụ thể</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Cấu hình kiểu 1: Khoảng thời gian -->
                                    <div id="interval_config" class="pin-config-section">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label">Khoảng thời gian pin (giây):</label>
                                            <div class="col-md-8">
                                                <input type="number" id="pin_interval" name="interval" class="form-control" min="30" value="600">
                                                <small class="form-text text-muted">Sau mỗi khoảng thời gian này, hệ thống sẽ tự động pin sản phẩm tiếp theo trong danh sách.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Cấu hình kiểu 2: Thời điểm cụ thể -->
                                    <div id="specific_config" class="pin-config-section" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Thiết lập thời gian pin cụ thể cho từng sản phẩm (tính từ lúc bắt đầu livestream).
                                        </div>
                                        
                                        <div id="pin_times_loading" style="text-align: center; display: none;">
                                            <i class="fa fa-spinner fa-spin"></i> Đang tải danh sách sản phẩm...
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover" id="pinTimeTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th width="5%">#</th>
                                                        <th width="15%">Hình ảnh</th>
                                                        <th width="50%">Sản phẩm</th>
                                                        <th width="30%">Thời gian pin (phút)</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="pin_time_items">
                                                    <!-- Sản phẩm sẽ được thêm vào đây bằng JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                <button type="button" id="btn_save_pin_config" class="btn btn-dark waves-effect waves-light">
                                    <i class="fa fa-save"></i> Lưu cấu hình
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>