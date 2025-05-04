<div class="modal fade " id="dialog_tiktok" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-80">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-info-circle"></i></span>
                        <span>Tiktok</span></h4>
                </div>
                <div id="dialog_tiktok_loading" style="text-align: center;display: none"><i
                        class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="div_add_studio alert alert-danger">
                    <strong>Chú ý!</strong> Bạn phải download Tool Live Tiktok trước khi thêm tài khoản tiktok. <a
                        data-toggle="tooltip" data-placement="top" data-html="true"
                        title="<p style='text-align:left'>
                        B1: Download tool về.<br>
                        B2: Giải nén file.<br>
                        B3: Chạy file setup_tiktoksync_real.exe (run as administrator).<br>
                        </p>"
                        target="_blank"
                        href="https://drive.google.com/file/d/1aLI629ElwmTSKuM8RDC2P5s6cmNxavl0/view?usp=share_link">
                        Download</a>
                </div>
                <div class="div_add_web">

                </div>
                <div class="pa-10 modal-body ">
                    <div class="content-tiktok">
                        <div class="row">
                            <div class="col-md-12">
                                <form id="frmSaveTiktok">
                                    <input type="hidden" id="dialog_type" />
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <table class="w-100">
<!--                                            <td style="width: 20%">
                                                <label class="col-12 col-form-label">Tên tài khoản Tiktok</label>
                                                <div class="col-12">
                                                    <input type="text" id="tiktok_name" class="form-control"
                                                           name="tiktok_name">
                                                </div>

                                            </td>
                                            <td style="width: 15%">
                                                <label class="col-12 col-form-label">Quốc gia</label>
                                                <div class="col-12">
                                                    <select id="region" class="form-control" name="region">
                                                        <option value="vn">Việt Nam</option>
                                                        <option value="us">USA</option>
                                                        <option value="gb">UK</option>
                                                    </select>
                                                </div>
                                            </td>-->
<!--                                            <td style="width: 10%">
                                                <label class="col-12 col-form-label">&nbsp;</label>
                                                <div class="col-12">
                                                    <div class="checkbox">
                                                        <input id="chk_proxy" type="checkbox" name="chk_proxy" value="1">
                                                        <label for="chk_proxy">
                                                            Proxy
                                                        </label>
                                                    </div>
                                                </div>




                                            </td>-->
<!--                                            <td style="width: 45%">
                                                <div class="row div_chk_proxy disp-none">
                                                    <label class="col-12 col-form-label">Proxy IP</label>
                                                    <div class="col-md-6 m-b-5">
                                                        <input type="text" id="proxy_ip" class="form-control"
                                                               name="proxy_ip" placeholder="IP">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" id="proxy_port" class="form-control"
                                                               name="proxy_port" placeholder="port">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="checkbox">
                                                            <input id="chk_proxy_pass" type="checkbox" name="chk_proxy_pass" value="1">
                                                            <label for="chk_proxy_pass">
                                                                Xác thực proxy
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="div_chk_proxy_pass d-flex disp-none ">
                                                        <div class="col-md-6">
                                                            <input type="text" id="proxy_user" class="form-control"
                                                                   name="proxy_user" placeholder="Tài khoản proxy">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" id="proxy_pass" class="form-control"
                                                                   name="proxy_pass" placeholder="Mật khẩu proxy">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>-->
                                            <!--                                            <td>
                                                <label class="col-12 col-form-label">Tắt live khi có vi phạm</label>
                                                <div class="col-12">
                                                        <select id="violation_number_stop" class="form-control" name="violation_number_stop">
                                                            <option value="0">Không tự động tắt</option>
                                                            <option value="1">1 vi phạm</option>
                                                            <option value="2">2 vi phạm</option>
                                                            <option value="3">3 vi phạm</option>
                                                            <option value="4">4 vi phạm</option>
                                                            <option value="5">5 vi phạm</option>
                                                        </select>
                                                </div>
                                            </td>-->
<!--                                            <td style="vertical-align: bottom;width: 5%">
                                                <button type="button"
                                                        class="btn btn-dark waves-effect waves-light btn-save-tiktok"
                                                        data-toggle="tooltip" data-placement="top" title="Lưu lại"><i
                                                        class="fa fa-save cur-point"></i> Lưu lại</button>

                                            </td>-->

                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tiktok_name">Tên tài khoản Tiktok</label>
                                                <input type="text" id="tiktok_name" name="tiktok_name" class=" form-control" >
                                            </div>    
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="region">Quốc gia</label>
                                                <select id="region" class="form-control" name="region">
                                                    <option value="vn">Việt Nam</option>
                                                    <option value="us">USA</option>
                                                    <option value="gb">UK</option>
                                                </select>
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <div class="checkbox">
                                                        <input id="chk_proxy" type="checkbox" name="chk_proxy" value="1">
                                                        <label for="chk_proxy">
                                                            Proxy
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row div_chk_proxy disp-none">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="proxy_ip">Proxy IP</label>
                                                <input id="proxy_ip" type="text" name="proxy_ip" class=" form-control" placeholder="IP">
                                            </div>    
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="proxy_port">Port</label>
                                                <input id="proxy_port" type="text" name="proxy_port" class=" form-control" placeholder="Port">
                                            </div>    
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group row">
                                                <label for="">&nbsp;</label>
                                                <div class="col-12">
                                                    <div class="checkbox">
                                                        <input id="chk_proxy_pass" type="checkbox" name="chk_proxy_pass" value="1">
                                                        <label for="chk_proxy_pass">
                                                            Xác thực proxy
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="div_chk_proxy_pass col-md-2 disp-none">
                                            <div class="form-group">
                                                <label for="proxy_user">&nbsp;</label>
                                                <input id="proxy_user" type="text" name="proxy_user" class=" form-control" placeholder="Tài khoản proxy">
                                            </div>    
                                        </div>
                                        <div class="div_chk_proxy_pass col-md-2 disp-none">
                                            <div class="form-group">
                                                <label for="proxy_pass">&nbsp;</label>
                                                <input id="proxy_pass" type="text" name="proxy_pass" class=" form-control" placeholder="Mật khẩu proxy">
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 m-b-15 text-right">
                                            <button type="button"
                                                    class="btn btn-dark waves-effect waves-light btn-save-tiktok"
                                                    data-toggle="tooltip" data-placement="top" title="Lưu lại"><i
                                                    class="fa fa-save cur-point"></i> Lưu lại</button>
                                        </div>
                                    </div>        
                                </form>
                            </div>
                        </div>
                        @if (count($tiktokProfile) > 0)
                        <div class="table-responsive">
                            <table id="table-tiktok-account" class="table hover-button-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-left w-20">Tên tiktok</th>
                                        <th class="text-center">Quốc Gia</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Live</th>
                                        <th class="text-center">IP</th>
                                        <th class="text-center">Số Vi Phạm</th>
                                        <th class="text-right w-25">Chức năng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tiktokProfile as $data)
                                    <tr>
                                        <td class="text-center color-red"><b>{{ $data->id }}</b></td>
                                        <td class="text-left">{{ $data->tiktok_name }}</td>
                                        <td class="text-center">{{ $data->region }}</td>
                                        <td class="text-center ur-status">
                                            @if ($data->status_cookie == 0)
                                            <span class="badge badge-warning">Mới</span>
                                            @elseif($data->status_cookie == 1)
                                            <span class="badge badge-success cur-point"> Đã đồng bộ</span>
                                            @endif
                                            
                                            @if($data->status_v3!="")
                                                @if($data->status_v3=="error")
                                                    <span data-toggle="tooltip" data-placement="top" title="{{$data->v3_tooltip}}" class="badge badge-danger cur-point"> Lỗi kích hoạt v3</span>
                                                @elseif($data->status_v3=="done")
                                                    <span class="badge badge-success cur-point"> Đã kích hoạt v3</span>
                                                @elseif($data->status_v3=="waiting" || $data->status_v3=="kicked")
                                                    <span class="badge badge-warning"> Chờ kích hoạt v3</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-center ur-status">
                                            @if ($data->status_run == 0)
                                            <span class="badge badge-warning">Chưa cấu hình live</span>
                                            @elseif($data->status_run == 1)
                                            <span class="badge badge-success"></i> Đã cấu hình live</span>
                                            @elseif($data->status_run == 2)
                                            <span class="badge badge-violet"></i> Đang live</span>
                                            @endif
                                        </td>
                                        <td class="text-center ip-{{ $data->id }}">{{ $data->ip }}</td>
                                        <td class="text-center">     
                                            @if($data->status_cookie == 1)
                                            <button
                                                class="btn btn-dark btn-sm waves-effect waves-light"
                                                onclick="getViolation({{ $data->id }})"
                           
                                                data-toggle="tooltip" data-placement="top"
                                                title="Bấm để kiểm tra vi phạm">
                                                Kiểm tra</button>
                                            @endif
                                        </td>
                                        <td class="text-right">

                                            @if ($data->status_cookie == 0)
                                            <button
                                                class="btn btn-circle btn-dark btn-sm waves-effect waves-light div_add_studio"
                                                onclick="loginTiktok({{ $data->id }})"
                                                data-toggle="tooltip" data-placement="top"
                                                data-html='true'
                                                title="Studio Đăng nhập<br> Đăng nhập tài khoản trên Tiktok LIVE Studio"><i
                                                    class="fa fa-user cur-point"></i></button>
                                            <button
                                                class="btn btn-circle btn-dark btn-sm waves-effect waves-light div_add_studio"
                                                onclick="commitTiktok({{ $data->id }})"
                                                data-toggle="tooltip" data-placement="top"
                                                data-html='true'
                                                title="Studio Commit<br>Hãy chắc chắn bạn đã đăng nhập vào Tiktok LIVE Studio"><i
                                                    class="fa fa-upload cur-point"></i></button>


                                            <button
                                                class="btn btn-dark btn-sm waves-effect waves-light div_add_web"
                                                onclick="dialogCookie({{ $data->id }})"
                                                
                                                data-type="web"
                                                data-toggle="tooltip" data-placement="top"
                                                title="Thêm tài khoản Tiktok sử dụng cookie trên Website">Thêm tài khoản</button>
                                            @endif
                                            @if ($data->status_cookie == 1)
                                            <button 
                                                class="btn btn-dark btn-sm waves-effect waves-light "
                                                onclick="showPinConfigModal({{ $data->id }})"

                                                data-id="{{ $data->id }}" data-toggle="tooltip"
                                                data-placement="top" title="Cấu hình pin sản phẩm">Pin</button>
                                            @if($data->custom_proxy == null)
                                            <button id="ip-{{ $data->id }}"
                                                    class="btn btn-dark btn-sm waves-effect waves-light "
                                                    onclick="renewIp({{ $data->id }})"
                                                    
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Đổi IP">Đổi IP</button>
                                            @endif
                                            <button id="renew-{{ $data->id }}"
                                                    class="btn btn-dark btn-sm waves-effect waves-light "
                                                    onclick="renewDevice({{ $data->id }})"
                                                   
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Đổi thiết bị">Đổi thiết bị</button>
                                            @if($data->status_v3=="")
                                            <button id="reg-v3-{{$data->id }}"
                                                class="btn btn-dark btn-sm waves-effect waves-light"
                                                onclick="regTiktokV3({{ $data->id }})"
                                                data-toggle="tooltip" data-placement="top"
                                                data-html='true'
                                                title="Đăng ký tiktok V3">Kích V3</button>
                                            @endif
                                            <!--                                            <button id="renew-{{ $data->id }}" class="btn btn-circle btn-dark btn-sm waves-effect waves-light" onclick="renewDevice({{ $data->id }})"
                                                                                    data-id="{{ $data->id }}" data-toggle="tooltip" data-placement="top"
                                                                                    title="Đổi thiết bị"><i class="fa fa-refresh cur-point"></i></button>-->
                                            @endif
                                            <button id="tik-{{ $data->id }}"
                                                    class="btn btn-circle btn-dark btn-sm waves-effect waves-light"
                                                    onclick="deleteTiktok({{ $data->id }})"
                                                    data-id="{{ $data->id }}" data-toggle="tooltip"
                                                    data-placement="top" title="Xóa"><i
                                                    class="fa fa-times-circle cur-point"></i></button>
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
                                        <th class="text-left w-20">Tên tiktok</th>
                                        <th class="text-center">Quốc Gia</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Live</th>
                                        <th class="text-center">IP</th>
                                        <th class="text-center">Số Vi Phạm</th>
                                        <th class="text-right w-25">Chức năng</th>
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
