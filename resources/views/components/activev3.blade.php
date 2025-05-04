@extends('layouts.master')

@section('content')
    <style>
        .edit-area {
            display: flex;
            align-items: center;
            gap: 10px;
            /* Khoảng cách giữa textbox và nút Save */
        }

        .edit-input {
            flex: 1;
            /* Textbox chiếm toàn bộ chiều rộng khả dụng */
        }
        
        .profile-container {
            /*background-color: #000;*/
            /*color: white;*/
            padding: 20px;
        }
        .profile-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }
        .profile-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        .profile-username {
            color: #aaa;
            font-size: 0.9rem;
        }
        .profile-stats {
            color: #aaa;
            font-size: 0.9rem;
        }        
    </style>
    <div class="row fadeInDown animated">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title m-t-0"><i class="fa fa-filter"></i> {{ trans('label.filterSearch') }}</h4>
                <div class="col-md-12 col-sm-6 col-xs-12">

                    <form id="formFilter" class="form-label-left" action="/listv3" method="GET">
                        <input type="hidden" name="limit" id="limit" value="{{ $limit }}">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">Id</label>
                                    <div class="col-12">
                                        <input id="id" class="form-control" type="text" name="id"
                                            value="{{ $request->id }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group row">
                                    <label class="col-12 col-form-label">Trạng thái</label>
                                    <div class="col-12">
                                        <select id="status_kick" name="status_kick" class="form-control">
                                            {!!$status_kick!!}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group row">
                                    <label class="col-8 col-form-label">&nbsp;</label>
                                    <div class="col-12">
                                        <button id="btnSearch" type="submit" class="btn btn-dark btn-micro"><i
                                                class="fa fa-filter"></i> Lọc</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="fadeInDown animated">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <div class="row m-b-10">
                        <div class="col-md-6 display-flex justify-content-between">
                            <h4 class="header-title m-t-0">
                                @foreach($countFakeChannel as $count)
                                    @if($count->status==2)
                                    Kênh còn lại: <span class="color-green">{{$count->number}}</span>&nbsp;
                                    @elseif($count->status==6)
                                       Kênh chưa xử lý: <span class="color-red">{{$count->number}}</span>&nbsp;
                                    @endif
                                @endforeach
                            </h4>
                            
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table id="table-tiktok-account" class="table hover-button-table">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-left">Tài khoản</th>
                                    <th class="text-left w-15">Tên tiktok</th>
                                    <th class="text-left w-15">Acc tiktok</th>
                                    <th class="text-center">Quốc Gia</th>
                                    <th class="text-center w-15">Trạng thái</th>
                                    <th class="text-center">Time</th>
                                    <th class="text-center w-20">STL Token</th>
                                    <th class="text-right w-25">Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $data)
                                    <tr>
                                        <td class="text-center color-red"><b>{{ $data->id }}</b></td>
                                        <td class="text-left">{{ $data->username }}</td>
                                        <td class="text-left">{{ $data->tiktok_name }}</td>
                                        <td class="text-left">{{ $data->tiktok_account }}</td>
                                        <td class="text-center">{{ $data->priority_region }}</td>
                                        <td class="text-center ur-status">
         
                                            @if ($data->stl_token != null)
                                                <span class="badge badge-success cur-point"> done v3</span>
                                            @elseif($data->status_v3 != '')
                                                @if($data->status_v3 == 'error')
                                                    <span data-toggle="tooltip" data-placement="top" title="{{$data->v3_tooltip}}" class="badge badge-danger cur-point"> {{ $data->status_v3 }} {{ $data->action_time }}</span>
                                                @else
                                                    <span data-toggle="tooltip" data-placement="top" title="{{$data->v3_tooltip}}" class="badge badge-warning cur-point"> {{ $data->status_v3 }} {{ $data->action_time }}</span>
                                                
                                                @endif
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <?php
                                            if ($data->active_v3_info != null) {
                                                echo \App\Common\Utils::timeText(json_decode($data->active_v3_info)->time);
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <!-- Hiển thị STL Token nếu có -->
                                            <span class="editable {{ $data->stl_token ? '' : 'd-none' }}" data-id="{{ $data->id }}">{{ $data->stl_token ?? '' }}</span>

                                            <!-- Textbox và nút Save -->
                                            <div class="edit-area {{ $data->stl_token ? 'd-none' : '' }}"
                                                style="display: flex; align-items: center;">
                                                <input type="text" class="edit-input form-control"
                                                    data-id="{{ $data->id }}" value="{{ $data->stl_token ?? '' }}"
                                                    style="flex: 1; margin-right: 10px;">
                                                <button class="btn btn-sm btn-success save-btn"
                                                    data-id="{{ $data->id }}">Save</button>
                                            </div>
                                        </td>
                                        <td class="text-right">

                                            @if ($data->status_cookie == 1)
                                                <button id="check-v3-{{ $data->id }}"
                                                    class="btn btn-dark btn-sm check-cookie" data-id="{{ $data->id }}"
                                                    data-toggle="tooltip"
                                                    data-placement="top" data-html='true'
                                                    title="Kiểm tra trạng thái V3">Check V3</button>
                                                <button id="fake-v3-{{ $data->id }}"
                                                    class="btn btn-dark btn-sm waves-effect waves-light"
                                                    onclick="fakeChannel({{ $data->id }})" data-toggle="tooltip"
                                                    data-placement="top" data-html='true' title="Lấy thông tin ảnh fake">Fake</button>
                                                <button id="kicked-v3-{{ $data->id }}"
                                                    class="btn btn-dark btn-sm waves-effect waves-light"
                                                    onclick="kicked({{ $data->id }})" data-toggle="tooltip"
                                                    data-placement="top" data-html='true' title="Đánh dấu đã kick">Kicked</button>
                                                <button 
                                                    class="btn btn-dark btn-sm waves-effect waves-light"
                                                    onclick="modalErrorV3({{ $data->id }})" data-toggle="tooltip"
                                                    data-placement="top" data-html='true' title="Thông báo lỗi">Error</button>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-md-6 ">
                                <div>
                                    <?php
                                    $info = str_replace('_START_', $datas->firstItem() != null ? $datas->firstItem() : '0', trans('label.title.sInfo'));
                                    $info = str_replace('_END_', $datas->lastItem() != null ? $datas->lastItem() : '0', $info);
                                    $info = str_replace('_TOTAL_', $datas->total(), $info);
                                    echo $info;
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="pull-right display-flex">
                                    <select id="cbbLimit" name="limit" aria-controls="tbl-title"
                                        class="form-control input-sm">
                                        {!! $limitSelectbox !!}
                                    </select>&nbsp;
                                    <?php if (isset($datas)) { ?>
                                    {!! $datas->links() !!}
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    @include('dialog.v3_error')
    @include('dialog.v3_fake_channel')
@endsection

@section('script')
    <script type="text/javascript">
        const countries = ["Lỗi cookie", "Account chưa đủ 30 ngày","Không có quyền" ,"Không rõ nguyên nhân","Your TikTok account is currently under a LIVE restriction"];
        const $input = $('#countryInput');
        const $dropdown = $('#countryDropdown');

      // Khi người dùng nhấp vào textbox
      $input.on('focus', function () {
        $dropdown.empty();
        countries.forEach(country => {
          $dropdown.append(`<a href="#" class="dropdown-item">${country}</a>`);
        });
        $dropdown.show();
      });

      // Khi chọn một quốc gia
      $dropdown.on('click', '.dropdown-item', function (e) {
        e.preventDefault();
        const country = $(this).text();
        $input.val(country); // Điền vào textbox
        $dropdown.hide(); // Ẩn danh sách
      });

      // Ẩn dropdown khi click ra ngoài
      $(document).on('click', function (e) {
        if (!$(e.target).closest('.form-group').length) {
          $dropdown.hide();
        }
      });

      // Tìm kiếm khi nhập vào textbox
      $input.on('input', function () {
        const query = $(this).val().toLowerCase();
        $dropdown.empty();
        const filteredCountries = countries.filter(country =>
          country.toLowerCase().includes(query)
        );
        if (filteredCountries.length) {
          filteredCountries.forEach(country => {
            $dropdown.append(`<a href="#" class="dropdown-item">${country}</a>`);
          });
        } else {
          $dropdown.append('<span class="dropdown-item text-muted">Không tìm thấy</span>');
        }
        $dropdown.show();
      });        
        
        function modalErrorV3(id) {
            $("#countryInput").val("");
            $("#profile_id").val(id);
            $('#dialog_v3_error').modal({
                backdrop: false
            });
        }
        
        function saveEror(){
           var  id = $("#profile_id").val();
           var error = $('#countryInput').val();
            $.ajax({
                url: '/tiktok', // Đường dẫn tới route update
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token
                    id: id,
                    v3_error:1,
                    error: error
                    
                },
                success: function(data) {
                    if (data.status == "success") {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                        $('#dialog_v3_error').modal('hide');
                        location.reload();
                    } else {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    }
                },
                error: function() {
                    $.Notification.autoHideNotify('error', 'top center', notifyTitle, "Đã xảy ra lỗi");
                }
            });
        }
        
        $('.check-cookie').click(function() {
            $('.check-cookie').removeClass('btn-warning').addClass('btn-dark');
//            $(this).removeClass('btn-success').addClass('btn-warning');
            btn = $(this);
            var id = btn.attr("data-id");
            var loadingText = '<i class="ion-load-c fa-spin"></i> Checking...';
            if (btn.html() !== loadingText) {
                btn.data('original-text', btn.html());
                btn.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "/api/tiktok/cookie/check",
                data: {
                    "id": id,
                    "_token": $("input[name=_token]").val()
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    btn.html(btn.data('original-text'));
                    btn.removeClass('btn-dark').addClass('btn-warning');
                    navigator.clipboard.writeText("");
                    if(data==null){
                        $.Notification.autoHideNotify("error", 'top center', notifyTitle,"Kiểm tra kênh thất bại");
                        return;
                    }
                    if (data.code == 0) {
                        if (data.is_v3 == 1) {
                            $.Notification.autoHideNotify("success", 'top center', notifyTitle,
                                "Kênh đã kích v3 thành công");
                        } else {
                            $.Notification.autoHideNotify("success", 'top center', notifyTitle, "Kênh ok");
                            navigator.clipboard.writeText(data.cookies_str);
                        }
                    } else {
                        $.Notification.autoHideNotify("error", 'top center', notifyTitle, data.message);
                    }
                },
                error: function(data) {
                    //                    btn.html($this.data('original-text'));
                    console.log(data);
                }
            });
        });
        

        function fakeChannel(id) {
            $("#fake_content").hide();
            $("#downloadBtn").hide();
            $("#channelImage").attr("src","");
            $("#fake_channel_image").attr("href","");
            $("#fake_channel_avatar").attr("src","");
            $("#fake_channel_url").html("");
            $("#fake_channel_name").html("");
            $("#fake_channel_subs").html("");
            $('#dialog_v3_fake_channel').modal({
                backdrop: false
            });
            $.ajax({
                url: '/api/channel_fake/get', // Đường dẫn tới route update
                method: 'GET',
                data: {
                    id: id,
                },
                success: function(data) {
                    console.log(data);
                     $("#fake_channel_image").attr("href",data.data.fake_image_url);
                     $("#channelImage").attr("src",data.data.fake_image_url);
                     $("#downloadBtn").attr("href",`/downloadImg/${data.data.id}`);
                     $("#fake_channel_avatar").attr("src",data.data.avatar);
                     $("#fake_channel_url").html(`${data.data.channel_id} <i class="fa fa-copy cur-point font-16" onclick="copyText('${data.data.channel_id}')"></i>`);
                     $("#fake_channel_name").html(`${data.data.channel_name} <i class="fa fa-copy cur-point font-20" onclick="copyText('${data.data.channel_name}')"></i>`);
                     $("#fake_channel_handle").html(`${data.data.handle} <i class="fa fa-copy cur-point font-16" onclick="copyText('${data.data.handle}')"></i>`);
                     $("#fake_channel_subs").html(`${data.data.subs} subscribers`);
                     $("#fake_content").show();
                     $("#downloadBtn").show();
                },
                error: function() {
                    $.Notification.autoHideNotify('error', 'top center', notifyTitle, "Đã xảy ra lỗi");
                }
            });
        }
        
        function kicked(id) {
            $.ajax({
                url: '/tiktok', // Đường dẫn tới route update
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token
                    id: id,
                    v3_kicked: 1
                },
                success: function(data) {
                    if (data.status == "success") {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                        location.reload();
                    } else {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);
                    }
                },
                error: function() {
                    $.Notification.autoHideNotify('error', 'top center', notifyTitle, "Đã xảy ra lỗi");
                }
            });
        }

        $(document).on('click', '.editable', function() {
            let parent = $(this).parent(); // Lấy phần tử cha
            $(this).addClass('d-none'); // Ẩn span
            parent.find('.edit-area').removeClass('d-none'); // Hiện textbox và nút Save
        });
        // Khi nhấn nút Save
        $(document).on('click', '.save-btn', function() {
            let button = $(this);
            let parent = button.parent(); // Lấy div chứa input và button
            let input = parent.find('.edit-input'); // Textbox
            let id = input.data('id'); // ID của row
            let value = input.val(); // Giá trị mới

            // Gửi AJAX để cập nhật
            $.ajax({
                url: '/tiktok', // Đường dẫn tới route update
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token
                    id: id,
                    stl_token: value
                },
                success: function(data) {
                    if (data.status == "success") {
                        // Cập nhật giao diện sau khi lưu
                        parent.addClass('d-none'); // Ẩn div edit-area
                        parent.siblings('.editable').text(value).removeClass(
                        'd-none'); // Hiện span với giá trị mới
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data
                            .message);
                        location.reload();
                    } else {
                        $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data
                            .message);
                    }
                },
                error: function() {
                    $.Notification.autoHideNotify('error', 'top center', notifyTitle, "Đã xảy ra lỗi");
                }
            });
        });
        // Khi nhấn ra ngoài textbox mà không nhấn Save
        $(document).on('blur', '.edit-input', function() {
            let parent = $(this).parent();
            if(parent.siblings('.editable').html()!=""){
            setTimeout(() => {
                if (!parent.find('.save-btn:focus').length) {
                    parent.addClass('d-none'); // Ẩn div edit-area
                    parent.siblings('.editable').removeClass('d-none'); // Hiện lại span
                }
            }, 200); // Đợi 200ms để kiểm tra focus nút Save
                
            }
        });
        $('#downloadImage').on('click', function () {
            const link = document.createElement('a');
            link.href = $("#channelImage").attr("src");
            link.download = 'channel_image.jpg';
            link.click();
        });
        
        copyText = function(text) {
            navigator.clipboard.writeText(text);
            $.Notification.notify('success', 'top center', 'Notification', 'Copied');
        }
    </script>
@endsection
