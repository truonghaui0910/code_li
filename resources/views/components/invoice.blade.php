@extends('layouts.master')

@section('content')

<div class="row fadeInDown animated">
    <div class="col-lg-12">
        <div class="card-box">
            <h4 class="header-title m-t-0"><i class="fa fa-filter"></i> {{ trans('label.filterSearch') }}</h4>
            <div class="col-md-12 col-sm-6 col-xs-12">

                <form id="formFilter" class="form-label-left" action="/invoice" method="GET">
                    <input type="hidden" name="limit" id="limit" value="{{$limit}}">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">Invoice Id</label>
                                <div class="col-12">
                                    <input id="tit" class="form-control" type="text" name="id" value="{{$request->id}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-12 col-form-label">Status</label>
                                <div class="col-12">
                                    <select id="cbbStatus" name="s" class="form-control">
                                        {!!$status!!}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">From</label>
                                <div class="col-12">
                                    <input class="form-control" type="text" name="from" value="{{$request->from}}" data-mask="9999/99/99">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">To</label>
                                <div class="col-12">
                                    <input class="form-control" type="text" name="to" value="{{$request->to}}" data-mask="9999/99/99">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group row">
                                <label class="col-8 col-form-label">&nbsp;</label>
                                <div class="col-12">
                                    <button id="btnSearch" type="submit" class="btn btn-dark btn-micro"><i class="fa fa-filter"></i> Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<div class="row fadeInDown animated">
    <div class="col-md-12">
        <div class="card-box">
            <table style="width: 100%">
                <tr>
                    <td><h4 class="header-title m-t-0 m-b-30">LIST INVOICES ({{$datas->total()}})</h4></td>
                    <td><h4 class="header-title m-t-0 m-b-30 pull-right">{{number_format($totalMoney, 0, ',', '.')}} &#8363;</h4></td>
                </tr>
            </table>
            <div style="overflow: auto;padding-right: 2px;">
                <form id="formTable" style="max-width: 99%">
                    {{ csrf_field() }}
                    <div class="row table-responsive">
                        <table class="table table-striped table-bordered table-hover mobile-table-width">
                            <thead>
                                <tr align="center">
                                    <th  style="width: 5%;text-align: center">@sortablelink('id','ID')</th>
                                    <th  style="width: 27%;">@sortablelink('invoice_id',trans('label.title.invoiceId'))</th>
                                    <th  style="width: 5%;text-align: center">Platform</th>
                                    <th  style="width: 5%;text-align: center">Month</th>
                                    <th  style="width: 5%;text-align: center">Live</th>
                                    <th  style="width: 10%;text-align: center">@sortablelink('user_id','User Name')</th>
                                    <th  style="width: 12%;text-align: center">@sortablelink('system_update_date','Created')</th>
                                    <th  style="width: 12%;text-align: center">Bank</th>
                                    <!--<th  style="width: 12%;text-align: center">@sortablelink('due_date', 'Due Date')</th>-->
                                    <th  style="width: 10%;text-align: center">@sortablelink('payment_money', trans('label.col.payment_money'))</th>
                                    <th  style="width: 7%;text-align: center">@sortablelink('user_approve', trans('label.col.user_approve'))</th>
                                    <th  style="width: 12%;text-align: center" style="width: 10%;text-align: center">@sortablelink('status', trans('label.col.status'))</th>
                                    <th  style="width: 10%;text-align: center">{{trans('label.col.function')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $i = 1; ?>
                                @foreach($datas as $data => $invoice)
                                <tr class="odd gradeX" align="center">
                                    <td><?php echo $invoice->id; ?></td>
                                    <td style="text-align: left;color: <?php echo ($invoice->due_date <= time() && $invoice->status == 0) ? 'red' : '#98a6ad'; ?>">
                                        {{$invoice->invoice_id}}
                                        <?php
                                        $link = '';
                                        foreach ($dataFacebook as $facebook) {
                                            if ($invoice->user_name == $facebook->user_name) {
                                                if (strpos($facebook->fb_id, "facebook") == false) {
                                                    $link = 'https://www.facebook.com/' . $facebook->fb_id;
                                                } else {
                                                    $link = $facebook->fb_id;
                                                }
                                                break;
                                            }
                                        }
                                        $open = "copyToClipboard('$link')";
                                        echo " <span data-toggle='tooltip' data-placement='top' data-original-title='" . trans('label.tooltip.copyClipboad') . "'><i onclick=$open class='fa fa-eye cus-point'></i> </span>";
                                        ?>
                                    </td>
                                    <td style="text-align: center">
                                        @if($invoice->platform==1)
                                            Youtube
                                        @elseif($invoice->platform==2)
                                            Titkok
                                        @elseif($invoice->platform==3)
                                            Shopee
                                        @endif
                                    </td>
                                    <td style="text-align: center">{{$invoice->month}}</td>
                                    <td style="text-align: center">{{$invoice->number_live}}</td>
                                    <td style="text-align: center">{{$invoice->user_name}}</td>
                                    <td>{{gmdate('d/m/Y H:i:s',$invoice->system_update_date + $user_login->timezone *3600)}}</td>
                                    <td style="text-align: center">{{$invoice->bank}}</td>
                                    <!--<td>{{gmdate('d/m/Y H:i:s',$invoice->due_date + $user_login->timezone *3600)}}</td>-->
                                    <td><span data-toggle="tooltip" data-placement="top"  data-original-title="{{$invoice->note}}">{{number_format($invoice->payment_money, 0, ',', '.')}}</span></td>
                                    <td>{{$invoice->user_approve}}</td>
                                    <td>
                                        <?php
                                        $status = $invoice->status;
                                        if ($status == 0) {
                                            echo trans('label.value.new');
                                        } else if ($status == 1) {
                                            echo trans('label.value.paid');
                                        }
                                        ?>
                                    </td>
                                    <td>

                                        @if ($status == 0)
                                        <button  style="width:80px" class="btn btn-success btn-xs action-invoice" data-type="1" value="{{$invoice->id}}"> <i class="fa fa-usd fa-fw"></i> {{trans('label.col.confirm')}}</button>
                                        @elseif ($status == 1 || $status == 2)
                                        <button style="width:80px" class="btn btn-danger btn-xs action-invoice" data-type="0" value="{{$invoice->id}}"><i class="fa fa-rotate-left fa-fw"></i> {{trans('label.col.reconfirm') }}</button>
                                        @endif

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                                <select id="cbbLimit" name="limit" aria-controls="tbl-title" class="form-control input-sm">
                                    {!!$limitSelectbox!!}
                                </select>&nbsp;
                                <?php if (isset($datas)) { ?>
                                    {!!$datas->links()!!}
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(".action-invoice").click(function (e) {
        e.preventDefault();
//        var dialog = bootbox.dialog({
//            message: '<p class="text-center mb-0"><i class="fa fa-spin fa-cog"></i> Please wait while we do something...</p>',
//            closeButton: false,
//            size: 'small',
//            className:'bootbox-cus-live'
//        });
//        dialog.init(function () {
//            setTimeout(function () {
//                dialog.modal('hide');
//            }, 3000);
//        });



        var $this = $(this);
        var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
        if ($(this).html() !== loadingText) {
            $this.data('original-text', $(this).html());
            $this.html(loadingText);
        }
        $this.attr("disabled", true);
        var type = $this.attr("data-type");
        var id = $this.val();
        $.ajax({
            type: "GET",
            url: "/action/invoice",
            data: {"type": type, "id": id},
            dataType: 'json',
            success: function (data) {
                $this.html($this.data('original-text'));
                $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                location.reload();
            },
            error: function (data) {
                $this.html($this.data('original-text'));
                $this.attr("disabled", false);
            }
        });
    });
</script>
@endsection

