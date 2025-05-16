<!-- Modal tạo bộ sản phẩm mới -->
<div class="modal fade" id="dialog_product_set" tabindex="-1" role="dialog" aria-labelledby="productSetModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom cur-point" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" data-placement="top" data-html="true" title="Đóng" style="top:11px">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fas fa-layer-group"></i></span> <span id="dialog_product_set_title">Quản lý Bộ Sản phẩm</span></h4>
                </div>
                <div class="pa-10 modal-body">
                    <div class="content-notify">
                        <!-- Tab menu -->
                        <ul class="nav nav-tabs" id="productSetTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="create-tab" data-toggle="tab" href="#create-content" role="tab" aria-controls="create-content" aria-selected="true">Tạo bộ sản phẩm</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="saved-tab" data-toggle="tab" href="#saved-content" role="tab" aria-controls="saved-content" aria-selected="false">Bộ sản phẩm đã lưu</a>
                            </li>
                        </ul>
                        
                        <!-- Tab content -->
                        <div class="tab-content" id="productSetTabContent">
                            <!-- Tab tạo bộ sản phẩm mới -->
                            <div class="tab-pane fade show active" id="create-content" role="tabpanel" aria-labelledby="create-tab">
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <form id="frmProductSet">
                                            {{ csrf_field() }}
                                            <input type="hidden" id="product_set_id" name="product_set_id" value="0"/>
                                            
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Tên bộ sản phẩm <span class="text-danger">*</span></label> 
                                                <div class="col-12">
                                                    <input type="text" id="product_set_name" class="form-control" name="product_set_name" placeholder="Nhập tên bộ sản phẩm..." required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Danh sách Link Sản Phẩm <span class="text-danger">*</span><br><small>(Mỗi link sản phẩm một dòng)</small></label> 
                                                <div class="col-12">
                                                    <textarea id="product_links" class="form-control" name="product_links" rows="6" spellcheck="false" placeholder="https://shop.tiktok.com/view/product/1731288182077949551?region=VN&local=en"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div id="product_links_preview" class="mb-3" style="display: none;">
                                                <label>Xem trước sản phẩm đã thêm:</label>
                                                <div id="preview_content" class="border p-3 mt-2 bg-light">
                                                    <!-- Nội dung xem trước sẽ được thêm vào đây -->
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <button type="button" id="btn_preview_products" class="btn btn-info waves-effect waves-light">
                                                    <i class="fa fa-eye"></i> Xem trước sản phẩm
                                                </button>
                                                <button type="button" id="btn_create_product_set" class="btn btn-dark waves-effect waves-light">
                                                    <i class="fa fa-save"></i> Lưu bộ sản phẩm
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab bộ sản phẩm đã lưu -->
                            <div class="tab-pane fade" id="saved-content" role="tabpanel" aria-labelledby="saved-tab">
                                <div id="saved_sets_loading" style="text-align: center;display: none">
                                    <i class="fa fa-spinner fa-spin"></i> Đang tải...
                                </div>
                                
                                <div class="row mt-3" id="saved_product_sets">
                                    <!-- Danh sách bộ sản phẩm đã lưu sẽ được thêm vào đây bằng JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal cấu hình thời gian pin sản phẩm -->
<!-- Modal cấu hình thời gian pin sản phẩm -->
<div class="modal fade" id="dialog_pin_config" tabindex="-1" role="dialog" aria-labelledby="pinConfigModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom cur-point" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" data-placement="top" data-html="true" title="Đóng" style="top:11px">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-clock-o"></i></span> <span id="dialog_pin_config_title">Cấu hình pin sản phẩm tự động</span></h4>
                </div>
                <div class="pa-10 modal-body">
                    <div class="content-notify">
                        <form id="frmPinConfig">
                            {{ csrf_field() }}
                            <input type="hidden" id="pin_config_profile_id" name="profile_id" value=""/>
                            <input type="hidden" id="pin_config_set_id" name="product_set_id" value=""/>
                            
                            <div class="form-group">
                                <label>Chọn bộ sản phẩm:</label>
                                <select id="select_product_set" class="form-control" name="selected_product_set">
                                    <option value="">-- Chọn bộ sản phẩm --</option>
                                    <!-- Options sẽ được thêm vào bằng JavaScript -->
                                </select>
                                <small class="form-text text-muted">Bộ sản phẩm là danh sách các sản phẩm được thêm vào luồng live</small>
                            </div>
                            
                            <div class="form-group">
                                <div class="checkbox">
                                    <input id="enable_auto_pin" type="checkbox" name="enable_auto_pin">
                                    <label for="enable_auto_pin">
                                        Bật tự động pin sản phẩm
                                    </label>
                                </div>
                                <small class="form-text text-muted">Khi bật tính năng này, hệ thống sẽ tự động pin sản phẩm trong luồng live dựa theo cấu hình bên dưới</small>
                            </div>
                            
                            <div class="pin-config-container" style="display: none;">
                                <div class="card-box">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link pin-type-tab active" data-type="interval" href="javascript:void(0)" onclick="changeTabPinType('interval')">
                                                <input type="radio" id="pin_type_interval" name="pin_type" class="d-none" value="interval" checked>
                                                <i class="fa fa-clock-o"></i> Pin theo khoảng thời gian
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link pin-type-tab" data-type="specific" href="javascript:void(0)" onclick="changeTabPinType('specific')">
                                                <input type="radio" id="pin_type_specific" name="pin_type" class="d-none" value="specific">
                                                <i class="fa fa-calendar"></i> Pin vào thời điểm cụ thể
                                            </a>
                                        </li>
                                    </ul>
                                    
                                    <div class="tab-content">
                                        <!-- Nội dung tab khoảng thời gian -->
                                        <div id="interval_config" class="pin-type-tab-content tab-pane active">
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label">Khoảng thời gian pin (phút):</label>
                                                <div class="col-md-8">
                                                    <input type="number" id="pin_interval" name="pin_interval" class="form-control" min="1" value="10">
                                                    <small class="form-text text-muted">Sau mỗi khoảng thời gian này, hệ thống sẽ tự động pin sản phẩm tiếp theo trong danh sách.</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Nội dung tab thời điểm cụ thể -->
                                        <div id="specific_config" class="pin-type-tab-content tab-pane">
                                            <div class="alert alert-danger">
                                                <i class="fa fa-info-circle"></i> Thiết lập thời gian pin cụ thể cho từng sản phẩm (tính từ lúc bắt đầu livestream). Bạn chỉ cần nhập thời gian cho những sản phẩm muốn pin.
                                            </div>
                                            
                                            <div id="pin_times_loading" style="text-align: center; display: none;">
                                                <i class="fa fa-spinner fa-spin"></i> Đang tải danh sách sản phẩm...
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="pinTimeTable">
                                                    <thead>
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

<!-- CSS bổ sung -->
<style>
.product-set-card {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.product-set-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.product-set-card .card-header {
    /*background-color: #f8f9fa;*/
    font-weight: bold;
}

.product-set-card .product-count {
    background-color: #6c757d;
    color: white;
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 8px;
}

.product-set-thumbnail {
    display: flex;
    margin-top: 10px;
    margin-bottom: 10px;
}

.product-set-thumbnail .thumbnail-item {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    overflow: hidden;
    margin-right: 5px;
    border: 1px solid #eee;
}

.product-set-thumbnail .thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-set-thumbnail .more-items {
    width: 50px;
    height: 50px;
    border-radius: 4px;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #555;
}

.product-preview-item {
    display: flex;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
    background-color: white;
    align-items: center;
}

.product-preview-item .preview-image {
    width: 100px;
    height: 100px;
    overflow: hidden;
    border-radius: 4px;
    margin-right: 10px;
}

.product-preview-item .preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-preview-item .preview-details {
    flex: 1;
}

.product-preview-item .preview-title {
    font-weight: bold;
    margin-bottom: 5px;
}

.product-preview-item .preview-price {
    color: #f44336;
}

.product-preview-item .preview-id {
    color: #666;
    font-size: 12px;
}


.sticky-footer {
    position: sticky;
    bottom: 0;
    background-color: #1e2430; /* Cùng màu với modal body trong dark mode */
    padding: 15px 0;
    margin-top: 20px;
    border-top: 1px solid #2d3748;
    text-align: right;
    z-index: 100;
}

/*.pin-config-modal {
    background-color: #0A0B18;
    color: inherit;
}

.pin-config-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background-color: transparent;
}

.pin-config-body {
    background-color: transparent;
}

.pin-config-select, 
.pin-config-input {
    background-color: rgba(45, 55, 72, 0.5) !important;
    border: 1px solid rgba(74, 85, 104, 0.5) !important;
    color: inherit !important;
    transition: all 0.3s ease;
}

.pin-config-select:focus, 
.pin-config-input:focus {
    background-color: rgba(45, 55, 72, 0.7) !important;
    border-color: rgba(74, 85, 104, 0.8) !important;
    box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25) !important;
}

.pin-config-muted-text {
    color: rgba(160, 174, 192, 0.8) !important;
}

 CSS cho tabs 
.pin-config-tabs {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 20px;
}*/

.pin-type-tab {
    cursor: pointer;
    padding: 10px 15px !important;
    /*border: 1px solid rgba(74, 85, 104, 0.5) !important;*/
    border-bottom: none !important;
    /*background-color: rgba(45, 55, 72, 0.5) !important;*/
    color: inherit !important;
    border-radius: 4px 4px 0 0 !important;
    margin-right: 4px;
    transition: all 0.3s ease;
    border: 1px solid #3a3a3a;    
}

.pin-type-tab.active {
    /*background-color: rgba(49, 130, 206, 0.7) !important;*/
    /*border-bottom: 2px solid #63b3ed !important;*/
    font-weight: bold;
}

.pin-type-tab-content {
    border: 1px solid rgba(74, 85, 104, 0.5);
    /*border-top: none;*/
    padding: 15px;
    background-color: rgba(45, 55, 72, 0.3);
    border-radius: 4px 0 4px 4px;
}

/*.pin-config-alert {
    background-color: rgba(44, 82, 130, 0.5);
    border: 1px solid rgba(43, 108, 176, 0.5);
    color: inherit;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}

.pin-config-table {
    color: inherit;
}

.pin-config-table thead th {
    border-bottom: 2px solid rgba(74, 85, 104, 0.5);
    background-color: rgba(45, 55, 72, 0.3);
}

.pin-config-table td, 
.pin-config-table th {
    border-top: 1px solid rgba(74, 85, 104, 0.5);
    padding: 8px;
}

 CSS cho checkbox 
.pin-config-checkbox {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
}

.pin-config-checkbox input[type="checkbox"] {
    margin-right: 8px;
}*/

/* CSS cho sticky footer */
.pin-config-footer {
    position: sticky;
    bottom: 0;
    padding: 15px 0;
    margin-top: 20px;
    /*border-top: 1px solid rgba(255, 255, 255, 0.1);*/
    text-align: right;
    z-index: 100;
    background-color: inherit;
}

/*.pin-cancel-btn {
    margin-right: 10px;
}

.pin-save-btn {
    background-color: #3182ce;
    border-color: #3182ce;
}

.pin-save-btn:hover {
    background-color: #2b6cb0;
    border-color: #2b6cb0;
}

 CSS cho loading 
.pin-loading {
    text-align: center;
    padding: 20px;
}*/
</style>