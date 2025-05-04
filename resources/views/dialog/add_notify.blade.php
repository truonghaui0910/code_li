<div class="modal fade div_scroll_50" id="dialog_notify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-bell"></i></span> <span>Thông báo</span></h4>                        
                </div>
                <div id="dialog_notify_loading" style="text-align: center;display: none"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <div class="content-notify">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmSaveNotify">
                                    {{ csrf_field() }}
                                    <input type="hidden" id="notify_id" name="notify_id"/>
<!--                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-6 col-form-label">Tiêu đề</label>
                                                <div class="col-12">
                                                    <input type="text" id="notify_title" class="form-control" name="notify_title">
                                                </div>
                                            </div>
                                        </div>
                                    </div>-->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-12 col-form-label">Nội dung</label> 
                                                <div class="col-12">
                                                    <input type="hidden" id="notify_content_real" name="notify_content">
                                                    <!--<textarea id="notify_content1" class="form-control " name="notify_content" rows="5" spellcheck="false"></textarea>-->
<div id="notify_content"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-6 col-form-label">Cấu hình thời gian</label>
                                                <div class="col-12">
                                                    <input type="datetime-local" id="notify_date_start" class="form-control" name="date_start" placeholder="Thời gian bắt đầu">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <div class="checkbox">
                                                        <input id="chk_date_end" type="checkbox" name="chk_date_end" value="1">
                                                        <label for="chk_date_end">
                                                            Kết thúc
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row div_chk_date_end disp-none">
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <input type="datetime-local" id="notify_date_end" class="form-control" name="date_end"
                                                           placeholder="Thời gian kết thúc">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <div class="checkbox">
                                                        <input id="is_maintenance" type="checkbox" name="is_maintenance" value="1">
                                                        <label for="is_maintenance">
                                                            Bảo trì hệ thống
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-dark waves-effect waves-light btn-save-notify m-t-10 m-b-10"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Lưu lại"><i class="fa fa-save cur-point"></i> Lưu lại</button>
                                </form>
                            </div>
                        </div>
                        @if(count($notify)>0)
                        <div class="table-responsive">
                            <table id="table-notify" class="table hover-button-table" style="width: 99%; table-layout: fixed;">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-left w-25">Nội dung</th>
                                        <th class="text-center">Bắt đầu</th>
                                        <th class="text-center">Kết thúc</th>
                                        <th class="text-center">Loại</th>
                                        <th class="text-right">Chức năng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notify as $data)
                                    <tr >
                                        <td class="text-center color-red"><b>{{$data->id}}</b></td>
                                        <td class="text-left text-ellipsis">{{$data->content}}</td>
                                        <td class="text-center ">{{$data->start_time_text}}</td>
                                        <td class="text-center ">{{$data->end_time_text}}</td>
                                        <td class="text-center">{{$data->type}}</td>
                                        <td class="text-right">
              
                                            <button id="edit-{{$data->id}}" 
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light"
                                                     onclick="editNotify({{$data->id}})"
                                                    data-id="{{$data->id}}" 
                                                    data-toggle="tooltip" 
                                                    data-placement="top"
                                                    title="Sửa"><i class="fa fa-edit cur-point"></i></button>
                                            <button id="tik-{{$data->id}}" 
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light" 
                                                    onclick="deleteNotify({{$data->id}})"
                                                    data-id="{{$data->id}}" 
                                                    data-toggle="tooltip" 
                                                    data-placement="top"
                                                    title="Xóa"><i class="fa fa-times-circle cur-point"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <!--<center>Không có dữ liệu</center>-->
                        <div class="table-responsive">
                            <table id="table-tiktok-account" class="table hover-button-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center w-25">Nội dung</th>
                                        <th class="text-center">Bắt đầu</th>
                                        <th class="text-center">Kết thúc</th>
                                        <th class="text-center">Loại</th>
                                        <th class="text-right">Chức năng</th>
                                </tr>
</thead>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>