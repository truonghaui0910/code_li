@extends('layouts.master')
@section('content')
@if($isAdmin)
<div class="row">
    <div class="col-lg-6 col-xl-3">
        <div class="widget-bg-color-icon card-box fadeInDown animated">
            <div class="bg-icon bg-icon-violet pull-left">
                <i class=" ti-user text-info"></i>
            </div>
            <div class="text-right">
                <h3 class="text-dark m-t-10"><b class="counter">{{number_format($customer[0]->count, 0, '.', ',')}}</b>
                </h3>
                <p class="text-muted mb-0">Total Active Customers</p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-3">
        <div class="widget-bg-color-icon card-box fadeInDown animated" style="padding: 13px">
            <div class="bg-icon bg-icon-violet pull-left">
                <i class=" ti-money text-info"></i>
            </div>
            <div class="text-right">
                <h4 class="text-dark font-14"><span class="text-muted">Live</span><b class="counter"> {{number_format($currRevLive, 0, '.', ',')}}</b></h4>
                <h4 class="text-dark font-14"><span class="text-muted">Tiktok</span><b class="counter"> {{number_format($currRevTiktok, 0, '.', ',')}}</b></h4>
                <h4 class="text-dark font-14"><span class="text-muted">Shopee</span><b class="counter"> {{number_format($currRevShopee, 0, '.', ',')}}</b></h4>
                <h4 class="text-dark font-15"><span class="text-muted">Total Revenue</span><b class="counter color-violet"> {{number_format($currRevTiktok + $currRevLive + $currRevShopee, 0, '.', ',')}}</b></h4>
                <!--<p class="text-muted mb-0">Total Revenue This Month</p>-->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-3">
        <div class="widget-bg-color-icon card-box fadeInDown animated" style="padding: 13px">
            <div class="bg-icon bg-icon-violet pull-left">
                <i class="ti-cloud-up text-info"></i>
            </div>
<!--            <div class="text-right">
               <h3 class="text-dark m-t-10"><b class="counter">{{number_format($live[0]->count, 0, '.', ',')}}</b></h3>
                <p class="text-muted mb-0">Total Live Running</p>
            </div>-->
            <div class="text-right">
                @foreach($livePlatform as $liveCount)
                <h4 class="text-dark font-14"><span class="text-muted">{{$liveCount->platform}}</span><b class="counter"> {{number_format($liveCount->count, 0, '.', ',')}}</b></h4>
<!--                <h4 class="text-dark font-14"><span class="text-muted">Tiktok</span><b class="counter"> {{number_format($currRevTiktok, 0, '.', ',')}}</b></h4>
                <h4 class="text-dark font-14"><span class="text-muted">Shopee</span><b class="counter"> {{number_format($currRevShopee, 0, '.', ',')}}</b></h4>-->
                @endforeach
                <h4 class="text-dark font-15"><span class="text-muted">Total Living</span><b class="counter "> {{number_format($live[0]->count, 0, '.', ',')}}</b></h4>
                <!--<p class="text-muted mb-0">Total Revenue This Month</p>-->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-3">
        <div class="widget-bg-color-icon card-box fadeInDown animated">
            <div class="bg-icon bg-icon-violet pull-left">
                <i class="ti-server text-info"></i>
            </div>
            <div class="text-right">
                <h3 class="text-dark m-t-10"><b class="counter">{{number_format($client[0]->count, 0, '.', ',')}} - {{number_format($client[1]->count, 0, '.', ',')}} clients</b>
                </h3>
                <p class="text-muted mb-0">{{number_format($thread[0]->used, 0, '.', ',')}}/{{number_format($thread[0]->max, 0, '.', ',')}} threads - {{number_format($thread[0]->free, 0, '.', ',')}} GB free</p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 fadeInDown animated">
        <div class="card-box "  style="height: 640px">
            <h4 class="m-t-0 m-b-20 header-title"><b>Customers are about to expire ({{count($customersToExpire)}})</b>
            </h4>

            <div class="inbox-widget nicescroll mx-box" style="min-height: 542px">
                @foreach($customersToExpire as $data)
                <?php 
                    $live = 0;
                    foreach($living as $l){
                        if($data->user_code == $l->user_id){
                            $live = $l->total;
                        }
                    }
                ?>
                <div class="inbox-item">
                    <div class="inbox-item-img"><img src="images/default-avatar.png" class="rounded-circle" alt="">
                    </div>
                    <p class="inbox-item-author"><a target="_blank"
                                                    href="customer?limit=10&username={{$data->user_name}}&s=-1">{{$data->user_name}}</a> <span
                                                    class="font-13"><i> </i></span> {{$data->package_code}} - {{$live}} living</p>
                    <p class="inbox-item-text">Contact <a target="_blank" href="{{$data->facebook}}">Facebook</a>&nbsp;&nbsp;@if($data->phone!=null)<a target="_blank" href="https://zalo.me/{{$data->phone}}">Zalo</a>@endif</p>
                    <p class="inbox-item-date">{{Utils::countDayLeft($data->package_end_date)}}</p>
                    <p class="inbox-item-online">Online {{\App\Common\Utils::timeText($data->last_activity)}}</p>
                </div>

                @endforeach

            </div>
        </div>

    </div> <!-- end col -->
    <div class="col-lg-8 fadeInDown animated">
        <div class="card-box " style="height: 640px">
            <div class="filter-date-hover">
                <table style="width: 100%">
                    <tr>
                        <!--<td><h4 class="header-title m-t-0 m-b-20">Report Revenue</h4></td>-->
                        <td>
                            <div class="row disp-none filter-date">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <input id="startDate" class="form-control" type="text" data-mask="9999/99/99" placeholder="From date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <!--<label class="col-8 col-form-label">To</label>-->
                                        <div class="col-12">
                                            <input id="endDate" class="form-control" type="text"data-mask="9999/99/99" placeholder="To date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><h4 class="header-title m-t-0 m-b-20 pull-right"><span id="customers" class="fadeInDown animated"></span></h4></td>
                        <td><h4 class="header-title m-t-0 m-b-20 pull-right"><span id="money" class="fadeInDown animated"></span></h4></td>
                    </tr>
                </table>
                <!--<h4 class="header-title m-t-0 m-b-20 ">Report Revenue</h4>-->
                <!--                <div class="row disp-none filter-date">
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <input class="form-control" type="text" name="from" data-mask="9999/99/99" placeholder="From date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group row">
                                            <label class="col-8 col-form-label">To</label>
                                            <div class="col-12">
                                                <input class="form-control" type="text" name="to" data-mask="9999/99/99" placeholder="To date">
                                            </div>
                                        </div>
                                    </div>
                                </div>-->
            </div>
            <div id="sub-chart-container">
                <div class="loader-1"></div>
            </div>
        </div>

    </div>

</div>
<div class="row">
    <div class="col-lg-4 fadeInLeft animated">
        <div class="card-box">
            <!--<h4 class="header-title m-t-0 m-b-20">Cost</h4>-->
            <table style="width: 100%">
                <tbody>
                    <tr>
                        <td><h4 class="header-title m-t-0">Cost</h4></td>
                        <td><h4 class="header-title m-t-0  pull-right"><button  style="width:90px" class="btn btn-dark btn-xs pull-right btn-add-cost" data-type="1" value=""> <i class="fa fa-plus fa-fw"></i> {{trans('Thêm cost')}}</button></h4></td>
                    </tr>
                </tbody>
            </table>
            <div class="table-responsive">
                <table id="tbl-cost" class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Period</th>
                            <th class="text-center">Cost</th>
                            <th class="text-center">Note</th>
                            <th class="text-center">Function</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costs as $index => $data)
                        <?php $index++; ?>
                        <tr>
                            <td class="text-center">{{$data->period}}</td>
                            <td class="text-center">{{number_format($data->cost, 0, '.', ',')}}</td>
                            <td class="text-center">{{$data->description}}</td>
                            <td class="text-center">
                                <button onclick="deleteCost({{$data->id}})" class="btn btn-dark btn-sm waves-effect waves-light cost{{$data->id}}"
                                        data-toggle="tooltip" data-placement="top" title="Xóa"><i class="fa fa-times-circle cur-point"></i></button>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-8 fadeInRight animated">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-20">Revenue</h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Period</th>
                            <!--<th class="text-center">Platform</th>-->
                            <th class="text-center">Revenue</th>
                            <th class="text-center">Customers</th>
                            <th class="text-center">Cost</th>
                            <th class="text-center">% Profit</th>
                            <th class="text-center">Profit</th>
                            <th class="text-center">40%</th>
                            <th class="text-center">20%</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $index => $data)
                        <?php $index++; ?>
                        <tr>
                            <td class="text-center">{{$index }}</td>
                            <td class="text-center">{{$data->period}}</td>

                            <td class="text-center" data-toggle="tooltip" data-placement="top" data-html="true" 
                                title="Live: {{number_format($data->live_rev, 0, '.', ',')}}<br>
                                Tiktok: {{number_format($data->tiktok_rev, 0, '.', ',')}}<br>
                                Shopee: {{number_format($data->shopee_rev, 0, '.', ',')}}">{{number_format($data->revenue, 0, '.', ',')}}</td>
                            <td class="text-center" data-toggle="tooltip" data-placement="top" data-html="true" 
                                title="Live: {{$data->live_count}}<br>
                                Tiktok: {{$data->tiktok_count}}<br>
                                Shopee: {{$data->shopee_count}}">{{$data->customers}}</td>
                            <td class="text-center">{{number_format($data->cost, 0, '.', ',')}}</td>
                            <td class="text-center">{{round($data->profit/$data->revenue *100,0)}}%</td>
                            <td class="text-center"><b>{{number_format($data->profit, 0, '.', ',')}}</b></td>
                            <td class="text-center"><b>{{number_format($data->profit *40/100, 0, '.', ',')}}</b></td>
                            <td class="text-center"><b>{{number_format($data->profit*20/100, 0, '.', ',')}}</b></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endif
@include('dialog.add_cost')
@endsection

@section('script')
<script type="text/javascript">
    $(".btn-add-cost").click(function (e) {
        e.preventDefault();
        $('#dialog_add_cost').modal({
            backdrop: false
        });
    });
    $(".btn-save-cost").click(function (e) {
        e.preventDefault();
        var form = $("#formAddCost").serialize();
        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $.ajax({
            type: "POST",
            url: "/addCost",
            data: form,
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                var tr = '<tr><td class="text-center">' + data.cost.period + '</td>';
                tr += '<td class="text-center">' + data.cost.cost + '</td>';
                tr += '<td class="text-center">' + data.cost.description + '</td>';
                tr += '<td class="text-center">';
                tr += '<button onclick="deleteCost(' + data.cost.id + ')" class="btn btn-dark btn-sm waves-effect waves-light cost' + data.cost.id + '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Xóa">';
                tr += '<i class="fa fa-times-circle cur-point"></i></button></td></tr>';
                $("#tbl-cost tbody").prepend(tr);
            },
            error: function (data) {
                $this.html($this.data('original-text'));
            }
        });
    });
    function deleteCost(id) {
        $.ajax({
            type: "GET",
            url: "/deleteCost/" + id,
            data: {},
            dataType: 'json',
            success: function (data) {
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                $(".cost"+id).closest("tr").hide();
            },
            error: function (data) {

            }
        });
    }

    $("#startDate").keyup(function () {
        var startDate = $(this).val();
        var endDate = $("#endDate").val();
        if (moment(startDate, "YYYY/MM/DD", true).isValid() && moment(endDate, "YYYY/MM/DD", true).isValid()) {
            console.log("chay");
            updateDataViewChart(startDate, endDate);
        }
//        console.log(moment(startDate, "YYYY/MM/DD", true).isValid());
//        console.log(moment(endDate, "YYYY/MM/DD", true).isValid());
    });
    $("#endDate").keyup(function () {
        var endDate = $(this).val();
        var startDate = $("#startDate").val();
        if (moment(startDate, "YYYY/MM/DD", true).isValid() && moment(endDate, "YYYY/MM/DD", true).isValid()) {
            updateDataViewChart(startDate, endDate);
        }
        console.log(moment(startDate, "YYYY/MM/DD", true).isValid());
        console.log(moment(endDate, "YYYY/MM/DD", true).isValid());
    });
    $("#startDate").val(moment().subtract(29, 'days').format('YYYY/MM/DD'));
    $("#endDate").val(moment().format('YYYY/MM/DD'));
    updateDataViewChart(moment().subtract(29, 'days').format('YYYYMMDD'), moment().format('YYYYMMDD'));
    function updateDataViewChart(startDate, endDate) {

        var url = "/getDailyInvoiceChart";
        var title = "Daily Revenue";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                "start": startDate,
                "end": endDate
            },
            dataType: 'json',
            success: function (data) {
                $(".filter-date").addClass("display-flex");
                $("#money").html(number_format(Math.round(data.money * 100) / 100, 0, ',', '.') + "&#8363;");
                $("#customers").html(data.customers + " customers");
                var html =
                        '<div class="row"><div class="col-md-12" style="height:500px"><canvas id="chart-total-views-daily"></canvas></div></div>';
                $("#sub-chart-container").html(html);
                var label = new Array();
                var dataTotal = new Array();
                var dataDaily = new Array();
                var colors = ['#ac3bda', '#0071d1', '#63b300', '#e6990e', '#912fc0', '#d94d6c', '#0f8071', '#f270b9',
                    '#f2c428', '#2fa5cb', '#5b54d5', '#00b4a2', '#eb3c97'
                ];
                var formatDate = '';
                var datasets = new Array();
                var label = new Array();
                var footer = new Array();
                var revenue = new Array();
                $.each(data.charts, function (k, v) {
                    formatDate = (v.date.toString().substring(0, 4) + "/" + v.date.toString().substring(
                            4, 6) + "/" + v.date.toString().substring(6, 8));
                    //                    formatDate = (v.date);
                    label.push(formatDate);
                    //                    channels.push(v.revenue);
                    revenue.push({
                        "x": v.date,
                        "y": v.revenue
                    });
                    footer.push([v.customers + ' customers']);
                });
                var dataset = {
                    label: "Daily Revenue",
                    data: revenue,
                    footer: footer,
                    fill: false,
                    borderColor: colors[0],
                    backgroundColor: colors[0],
                    borderWidth: 1

                };
                datasets.push(dataset);
                drawLineCharts('chart-total-views-daily', label, datasets);
            },
            error: function (data) {}

        });
    }
</script>
@endsection