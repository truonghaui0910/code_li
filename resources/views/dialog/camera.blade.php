    <div class="modal fade" id="phone-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                 <div class="card-box m-b-0 pa-15">
                    <div class="modal-header pa-5">
                        <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-cogs fa-fw"></i></span> <span>Camera</span></h4>                        
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="phone-frame">
                                    <div id="small-box">
                                        <div class="resize-handle"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="control-panel">
                                    <h6>Controls</h6>
                                    <div class="form-group">
                                        <label for="resolution-select">Độ phân giải:</label>
                                        <select id="resolution-select" class="form-control">
                                            <option value="1080x1920">1080x1920</option>
                                            <option value="720x1280">720x1280</option>
                                        </select>
                                    </div>
                                    <div class="form-group box-controls">
                                        <label>Vị trí và kích thước</label>
                                        <div class="d-flex justify-content-between">
                                            <input type="number" id="box-x" class="form-control" placeholder="X">
                                            <input type="number" id="box-y" class="form-control" placeholder="Y">
                                            <input type="number" id="box-width" class="form-control" placeholder="W">
                                            <input type="number" id="box-height" class="form-control" placeholder="H">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="volume1">Âm lượng camera:</label>
                                        <input type="range" class="volume-control" id="volume1" min="0" max="100" value="50">
                                    </div>
                                    <div class="form-group">
                                        <label for="volume2">Âm lượng live:</label>
                                        <input type="range" class="volume-control" id="volume2" min="0" max="100" value="50">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submit-btn">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>