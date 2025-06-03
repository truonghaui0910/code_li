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
    .preview-vat-btn, .download-vat-btn {
        margin: 2px;
        min-width: 35px;
    }

    .preview-vat-btn:hover {
        background-color: #31b0d5;
    }

    .download-vat-btn:hover {
        background-color: #449d44;
    }    
</style>
<div class="row fadeInDown animated">
    <div class="col-lg-6">
        <div class="card-box">
            <h4 class="header-title m-t-0"><i class="fa fa-filter"></i> {{ trans('label.filterSearch') }}</h4>
            <div class="col-md-12 col-sm-6 col-xs-12">

                <form id="formFilter" class="form-label-left" action="/vatInvoice" method="GET">
                    <input type="hidden" name="limit" id="limit" value="{{ $limit }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">ID/InvoiceId/Username</label>
                                <div class="col-12">
                                    <input id="filter_data" class="form-control" type="text" name="filter_data"
                                           value="{{ $request->filter_data }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Trạng thái</label>
                                <div class="col-12">
                                    <select id="status_vat" name="status_vat" class="form-control">
                                        {!!$status_vat!!}
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
    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-4">
                <div class="card-box">
                    <select id="monthSelect" name="monthSelect" class="form-control col-md-5" style="background: transparent;
                                                                             position: absolute;
                                                                             right: 15px;
                                                                             top: 0px;
                                                                             z-index: 100;"></select>
                    <div class="col-md-12 col-sm-6 col-xs-12">
                        <div class="widget-simple text-center card-box m-b-0">
                            <h3 class="text-pink font-bold mt-0"><span class="counter total">0</span></h3>
                            <p class="text-muted mb-0 total-tran font-14"></p>
                            <p class="text-muted mb-0 font-15">Total Earning</p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-box position-relative">

                    <div class="col-md-12 col-sm-6 col-xs-12">
                        <div class="widget-simple text-center card-box m-b-0">
                            <h3 class="text-success counter font-bold mt-0 moonshots">0</h3>
                            <p class="text-muted mb-0 moonshots-tran"></p>
                            <p class="text-muted mb-0">Total Moonshots</p>
                        </div>

                    </div>
                </div>
            </div>
    <div class="col-lg-4">
        <div class="card-box">
            <div class="col-md-12 col-sm-6 col-xs-12">
                <div class="widget-simple text-center card-box m-b-0">
                    <h3 class="text-primary counter font-bold mt-0 sang_acb">0</h3>
                    <p class="text-muted mb-0 sang_acb-tran"></p>
                    <p class="text-muted mb-0">Total Acb</p>
                </div>

            </div>
        </div>
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
                            VAT declared: <span class="color-green">{{$summary[0]->VAT}}</span>&nbsp;
                            VAT not declared: <span class="color-red">{{$summary[0]->NOT_VAT}}</span>&nbsp;
                        </h4>

                    </div>
                    <div class="col-md-6 text-right">
                        <button id="syncVatBtn" class="btn btn-warning btn-sm">
                            <i class="fa fa-refresh"></i> Đồng bộ mã VAT
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="table-tiktok-account" class="table hover-button-table">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-left">Username</th>
                                <th class="text-left w-15">Invoice Id</th>
                                <th class="text-left w-15">Amount</th>
                                <th class="text-center">Time</th>
                                <th class="text-center">Updated</th>
                                <th class="text-center">Live</th>
                                <th class="text-center">Month</th>
                                <th class="text-center w-20">Vat Code</th>
                                <th class="text-right w-10">Chức năng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                            <tr>
                                <td class="text-center color-red"><b>{{ $data->id }}</b></td>
                                <td class="text-left">{{ $data->user_name }}</td>
                                <td class="text-left">{{ $data->invoice_id }}</td>
                                <td class="text-left">{{number_format($data->payment_money, 0, ',', '.')}}</td>
                                <td class="text-center">{{ App\Common\Utils::timeToStringGmT7($data->system_create_date) }}</td>
                                <td class="text-center">{{ App\Common\Utils::timeText($data->system_update_date) }}</td>
                                <td class="text-center">{{ $data->number_live }}</td>
                                <td class="text-center">{{ $data->month  }}</td>
                                <td class="text-center">
                                    <!-- Hiển thị STL Token nếu có -->
                                    <span class="editable {{ $data->vat_code ? '' : 'd-none' }}" data-id="{{ $data->id }}">{{ $data->vat_code ?? '' }}</span>

                                    <!-- Textbox và nút Save -->
                                    <div class="edit-area {{ $data->vat_code ? 'd-none' : '' }}"
                                         style="display: flex; align-items: center;">
                                        <input type="text" class="edit-input form-control"
                                               data-id="{{ $data->id }}" value="{{ $data->vat_code ?? '' }}"
                                               style="flex: 1; margin-right: 10px;">
                                        <button class="btn btn-sm btn-success save-btn"
                                                data-id="{{ $data->id }}">Save</button>
                                    </div>
                                </td>
                                <td class="text-right">
                                    @if($data->vat_id)
                                        <button class="btn btn-info btn-sm preview-vat-btn" 
                                                data-id="{{ $data->id }}" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Xem hóa đơn VAT">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button class="btn btn-success btn-sm download-vat-btn" 
                                                data-id="{{ $data->id }}" 
                                                data-toggle="tooltip" 
                                                data-placement="top" 
                                                title="Tải hóa đơn VAT">
                                            <i class="fa fa-download"></i>
                                        </button>
                                    @else
                                        <span class="text-muted"></span>
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

@endsection

@section('script')
<script type="text/javascript">
    // Get the current date
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth() + 1; // Months are 0-based

    // Generate the options for the previous 12 months
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const $monthSelect = $('#monthSelect');

    for (let i = 0; i < 12; i++) {
        const month = ((currentMonth - i - 1 + 12) % 12) + 1; // Calculate month (1-12)
        const year = currentYear - Math.floor((12 - currentMonth + i) / 12); // Adjust year if needed
        const value = `${year}${month.toString().padStart(2, '0')}`;
        const label = `${monthNames[month - 1]}-${year}`;
        $monthSelect.append(`<option value="${value}">${label}</option>`);
    }

    // Set the default selected value to the current month
    const formattedValue = `${currentYear}${currentMonth.toString().padStart(2, '0')}`;
    $monthSelect.val(formattedValue);

    $("#monthSelect").change(function () {
        $.ajax({
            url: '/vatStats', // Đường dẫn tới route update
            method: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                period: $("#monthSelect").val()
            },
            success: function (data) {
                console.log(data);
                $(".moonshots").html(0);
                $(".sang_acb").html(0);
                $(".total").html(0);
                $.each(data, function(key, value) {
                    console.log(value);
                    $(`.${value.bank_name}`).html(`${ number_format(value.total_payment_money, 0, '.', ',')}`);
                    $(`.${value.bank_name}-tran`).html(`${ value.total_count} transactions`);
                });
                if (data.status == "success") {

                }
            },
            error: function () {
                $.Notification.autoHideNotify('error', 'top center', notifyTitle, "Đã xảy ra lỗi");
            }
        });
    });
    $("#monthSelect").change();
    $(document).on('click', '.editable', function () {
        let parent = $(this).parent(); // Lấy phần tử cha
        $(this).addClass('d-none'); // Ẩn span
        parent.find('.edit-area').removeClass('d-none'); // Hiện textbox và nút Save
    });
    // Khi nhấn nút Save
    $(document).on('click', '.save-btn', function () {
        let button = $(this);
        let parent = button.parent(); // Lấy div chứa input và button
        let input = parent.find('.edit-input'); // Textbox
        let id = input.data('id'); // ID của row
        let value = input.val(); // Giá trị mới
        var loadingText = '<i class="ion-load-c fa-spin"></i> Checking...';
        if (button.html() !== loadingText) {
            button.data('original-text', button.html());
            button.html(loadingText);
        }
        // Gửi AJAX để cập nhật
        $.ajax({
            url: '/vatInvoice', // Đường dẫn tới route update
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}', // CSRF token
                id: id,
                vat_code: value
            },
            success: function (data) {
                button.html(button.data('original-text'));
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
            error: function () {
                $.Notification.autoHideNotify('error', 'top center', notifyTitle, "Đã xảy ra lỗi");
            }
        });
    });
    // Khi nhấn ra ngoài textbox mà không nhấn Save
    $(document).on('blur', '.edit-input', function () {
        let parent = $(this).parent();
        if (parent.siblings('.editable').html() != "") {
            setTimeout(() => {
                if (!parent.find('.save-btn:focus').length) {
                    parent.addClass('d-none'); // Ẩn div edit-area
                    parent.siblings('.editable').removeClass('d-none'); // Hiện lại span
                }
            }, 200); // Đợi 200ms để kiểm tra focus nút Save

        }
    });

    // Xử lý nút Preview VAT
    $(document).on('click', '.preview-vat-btn', function() {
        let invoiceId = $(this).data('id');
        let url = `/invoice/vat/preview/${invoiceId}`;

        // Mở trong tab mới
        window.open(url, '_blank');
    });

    // Xử lý nút Download VAT
    $(document).on('click', '.download-vat-btn', function() {
        let button = $(this);
        let invoiceId = button.data('id');
        let originalHtml = button.html();

        // Hiệu ứng loading
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        button.prop('disabled', true);

        // Tạo link download
        let url = `/invoice/vat/download/${invoiceId}`;

        // Tạo element a ẩn để download
        let link = document.createElement('a');
        link.href = url;
        link.download = `hoadon_vat_${invoiceId}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Reset button sau 2 giây
        setTimeout(function() {
            button.html(originalHtml);
            button.prop('disabled', false);
        }, 2000);
    });
    
$(document).on('click', '#syncVatBtn', function() {
    let button = $(this);
    let originalHtml = button.html();
    
    // Hiệu ứng loading
    button.html('<i class="fa fa-spinner fa-spin"></i> Đang đồng bộ...');
    button.prop('disabled', true);
    
    // Gọi API sync
    $.ajax({
        url: '/invoice/vat/sync',
        method: 'GET',
        success: function(response) {
            button.html(originalHtml);
            button.prop('disabled', false);
            
            if (response.status === 'success') {
                $.Notification.autoHideNotify('success', 'top center', 'Thành công', response.message);
                // Reload trang để cập nhật dữ liệu mới
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                $.Notification.autoHideNotify('error', 'top center', 'Lỗi', response.message);
            }
        },
        error: function(xhr) {
            button.html(originalHtml);
            button.prop('disabled', false);
            
            let errorMsg = 'Đã xảy ra lỗi';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $.Notification.autoHideNotify('error', 'top center', 'Lỗi', errorMsg);
        }
    });
});    
</script>
@endsection
