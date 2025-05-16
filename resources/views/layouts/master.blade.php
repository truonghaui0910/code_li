<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description"
        content="AutoLive.VIP Phần Mềm Tự Động Live Stream Youtube, Live Stream Facebook, Live Stream Twitch. Live Stream Đơn Giản, Dễ Sử Dụng, Live Stream Ổn Định Chất Lượng Video 4K">
    <meta name="author" content="Autowin Team">

    <link rel="shortcut icon" href="images/Autolive_logo.png">

    <title>Auto Live Stream 24/7 Youtube - Facebook - Twitch</title>
    <base href="{{ asset('') }}">
    <link href="assets/mt/dark/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />
    <link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
    <link href="assets/plugins/summernote/summernote.css" rel="stylesheet" />
    <link href="assets/plugins/custombox/dist/custombox.min.css" rel="stylesheet" />
    <link href="assets/plugins/tablesaw/dist/tablesaw.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/mt/dark/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="assets/mt/landing/css/animate.css">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="css/threedot.css" rel="stylesheet" type="text/css">
    <link href="css/style.css?v=37" rel="stylesheet" type="text/css">

    <script src="assets/js/modernizr.min.js"></script>


</head>

<body class="fixed-left">
    <!--        <div class="preloader">
                <i class="status fa fa-spinner fa-spin fa-2x"></i>
            </div>-->
    <!-- Begin page -->
    <div id="wrapper" class="theme--dark">

        <!-- Top Bar Start -->
        @include('layouts.topbar')
        <!-- Top Bar End -->

        <!-- Left Sidebar Start -->
        @include('layouts.leftsidebar')
        <!-- Left Sidebar End -->

        <!-- Start right content -->
        <div class="content-page ">
            <!-- Start content -->
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                    @include('dialog.use_bonus')
                </div>
                <!-- end container -->
            </div>
            <!-- end content -->

            <footer class="footer">
                2022 © Autowin Team <span class="hide-phone">- Autolive.vip</span>
            </footer>
            <div class="modal fade " id="dialog_tb" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="card-box m-b-0 pa-15">
                            <div class="modal-header pa-5">
                                <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-info-circle fa-fw"></i></span> <span>Thông báo</span></h4>                        
                            </div>

                            <div class="pa-10 modal-body ">
                                <p class="font-20">Truy cập <a href="https://autolive.me/login" class="">https://autolive.me</a> để sử dụng website tool không giật lag. Xin cảm ơn!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(isset($user_login))
            @if(($user_login->package_code!="LIVETEST" && $user_login->package_end_date > time() && $user_login->package_end_date  - 2 * 86400 < time()) 
            || ($user_login->tiktok_package!="TIKTOKTEST" && $user_login->tiktok_end_date > time() && $user_login->tiktok_end_date - 2 * 86400 < time()))
            <div class="modal fade " id="dialog_expire" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="card-box m-b-0 pa-15">
                            <div class="modal-header pa-5">
                                <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-info-circle fa-fw"></i></span> <span>Thông báo</span></h4>                        
                            </div>

                            <div class="pa-10 modal-body ">
                                @if($user_login->package_code!="LIVETEST" && $user_login->package_end_date > time() && $user_login->package_end_date  - 3 * 86400 < time())
                                    <p class="font-20">Gói cước {{$user_login->package_code}} sắp hết hạn, hãy gia hạn sớm <a href="/invoice/{{$user_login->package_code}}" class=""><b>GIA HẠN</b></a></p>
                                @elseif($user_login->tiktok_package!="TIKTOKTEST" && $user_login->tiktok_end_date > time() && $user_login->tiktok_end_date - 3 * 86400 < time())
                                    <p class="font-20">Gói cước {{$user_login->tiktok_package}} sắp hết hạn, hãy gia hạn sớm <a href="/invoice/{{$user_login->tiktok_package}}" class=""><b>GIA HẠN</b></a></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif
        </div>
        <!-- End right content -->

        <!-- Right Sidebar -->
        <!--include('layouts.rightsidebar')-->
        <!-- /Right-bar -->

    </div>
    <!-- END wrapper -->

    <script>
        var resizefunc = [];
    </script>

    <!-- Plugins  -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/detect.js"></script>
    <script src="assets/js/fastclick.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/jquery.blockUI.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/plugins/switchery/switchery.min.js"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="assets/mt/dark/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js" type="text/javascript">
    </script>
    <script src="assets/plugins/summernote/summernote.min.js"></script>
    <!-- Notification js -->
    <script src="assets/plugins/notifyjs/dist/notify.min.js"></script>
    <script src="assets/plugins/notifications/notify-metro.js"></script>

    <!-- Modal-Effect -->
    <script src="assets/plugins/custombox/dist/custombox.min.js"></script>
    <script src="assets/plugins/custombox/dist/legacy.min.js"></script>
    <!--custom qr-->
    <script src="js/bundle_qr.js?v=1.01"></script>
    <!--custom javascript-->
    <!--<script src="js/camera_config.js?v=1.00"></script>-->
    <script src="js/script.js?version=8"></script>
    <script src="js/bootbox.all.min.js"></script>

    
<!--    <script type="text/javascript" src="https://139.59.112.231/players/js/adapter-7.4.0.min.js"></script>
    <script type="text/javascript" src="https://139.59.112.231/players/js/srs.sdk.js"></script>
    <script type="text/javascript" src="https://139.59.112.231/players/js/winlin.utility.js"></script>
    <script type="text/javascript" src="https://139.59.112.231/players/js/srs.page.js"></script>    -->
    <!-- Page js  -->
    <!--<script src="assets/pages/jquery.dashboard.js"></script>-->

    <!-- Custom main Js -->
    <script src="assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>
    <script src="assets/mt/dark/js/bootstrap-datetimepicker.min.js"></script>
    <script src="js/Chart.bundle.min.js"></script>
    <script src="assets/mt/dark/plugins/moment/moment.js"></script>
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/js/jquery.app.js?v=3"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>


    <script type="text/javascript">

    copyDownloadImg = function(id,type){
        const content = document.getElementById(id);
                    html2canvas(content).then(async (canvas) => {
                // Xuất canvas thành hình ảnh
                const imgData = canvas.toDataURL('image/png');

                // Chuyển đổi canvas thành Blob
                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));

                // Sao chép ảnh vào clipboard (nếu được trình duyệt hỗ trợ)
                if(type=="copy" || type=="all"){
                try {
                    await navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob })
                    ]);
                    $.Notification.autoHideNotify("success", 'top center', 'Notify', "Copied");
                } catch (error) {
                    console.error('Không thể sao chép ảnh vào clipboard:', error);
                }
            }
                if(type=="download" || type=="all"){
                    // Tải ảnh về máy
                    const link = document.createElement('a');
                    link.href = imgData;
                    link.download = $(".ivid").html()+".png";
                    link.click();
                }
            });
    };
//        $(window).on('load', function () {
//            $('.status').fadeOut();
//            $('.preloader').delay(50).fadeOut('fast');
//        });

        if (document.getElementById('dialog_expire')) {
            if(!window.location.href.includes("/invoice")){
             $('#dialog_expire').modal('show');
            }
        }
//        document.addEventListener("DOMContentLoaded", function() {
//            var currentDomain = window.location.hostname;
//            var targetDomain = ["autolive.me","autolive.one"];
//            console.log(currentDomain);
//            if (!targetDomain.includes(currentDomain)) {
//                $('#dialog_tb').modal('show');
//            }
//
//        });
        jQuery(document).ready(function($) {
        $(".btn-dialog-bonus").click(function (e) {
            e.preventDefault();
            $('#dialog_use_bonus').modal({
                backdrop: false
            });
        });
        $(".btn-use-bonus").click(function (e) {
            e.preventDefault();
            var form = $("#formUseBonus").serialize();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            $.ajax({
                type: "POST",
                url: "/useBonus",
                data: form,
                dataType: 'json',
                success: function (data) {
                    $this.html($this.data('original-text'));
                    console.log(data);
                    $.Notification.autoHideNotify(data.status, 'top center', notifyTitle, data.message);

                },
                error: function (data) {
                    $this.html($this.data('original-text'));
                }
            });
        });
            
            
            if (Notification.permission !== 'granted') {
                Notification.requestPermission();
            }
//            Pusher.logToConsole = true;


            var isActiveCus = '<?php echo $isActiveCus; ?>';
            console.log("isActiveCus",isActiveCus);
            var us = '<?php echo $user_login->user_name; ?>';
            if(isActiveCus==1){
                var pusher = new Pusher('f4a56fb4a7d911625576', {
                    encrypted: true,
                    cluster: 'ap1'
                });

                var channel = pusher.subscribe('my-channel');
                channel.bind('my-event', function(data) {
                    if (data.users.includes(us)) {
                        notify(data.message, 'https://autolive.vip/live',
                            "https://autolive.vip/images/Autolive_logo.png");
                        if (data.message.includes("Đơn")) {
                            $(".payment-pending").html(
                                '<strong>Trạng thái: </strong> <span class="badge badge-success">Thành công</span>'
                                );
                        }
                    }
                });
                
            }
            
            window.addEventListener("beforeunload", function() {
                pusher.disconnect();
            });


            $('.slimscrollText').slimScroll({
                height: 'auto',
                width: '100%',
                position: 'right',
                size: "5px",
                color: '#98a6ad',
                wheelStep: 5,
                allowPageScroll: false
            });
            var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome');
            if (is_chrome > -1) {
                $(".logo").css({
                    "line-height": "71px"
                });
            } else {
                $(".logo").css({
                    "line-height": "70px"
                });
            }
            $("#cbbLimit").change(function() {
                $("#limit").val($(this).val());
                $('#formFilter').submit();
            });
            //        $('.counter').counterUp({
            //            delay: 100,
            //            time: 1200
            //        });
            //        $('.circliful-chart').circliful();
        });


        $(".mark-number").each((i, ele) => {
            let clone = $(ele).clone(false);
            clone.attr("type", "text");
            let ele1 = $(ele);
            clone.val(Number(ele1.val()).toLocaleString("en"));
            $(ele).after(clone);
            $(ele).hide();
            clone.mouseenter(() => {

                ele1.show();
                clone.hide();
            });
            setInterval(() => {
                let newv = Number(ele1.val()).toLocaleString("en");
                if (clone.val() !== newv) {
                    clone.val(newv);
                }
            }, 10);
            $(ele).mouseleave(() => {
                $(clone).show();
                $(ele1).hide();
            });
        });
        checkboxShowDiv('is_auto_pin', true);
        checkboxShowDiv('radio_time', false);
        checkboxShowDiv('chk_date_end', true);
        checkboxShowDiv('change_pass', true);
        checkboxShowDiv('live_repeat', true);
        checkboxShowDiv('chk_proxy', true);
        checkboxShowDiv('chk_proxy_pass', true);
        checkboxShowDiv('chk_extra', true);
        checkboxShowDiv('is_multi_live', true);

        function checkboxShowDiv(checkBox, status) {
            var check = $('#' + checkBox);
            $(check).change(function() {
                if (check[0].checked === status) {
                    $('.div_' + checkBox).removeClass("disp-none fadeOutUp").addClass("animated wow fadeInDown");
                } else {
                    $('.div_' + checkBox).removeClass("fadeInDown").addClass("fadeOutUp");
                }
            });
        }
        
        showNotification = function (type = 'info', message) {
            // Create notification element if it doesn't exist
            if ($('#notification-container').length === 0) {
                $('body').append(
                    '<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>'
                );
            }

            // Generate a unique ID for this notification
            const id = 'notification-' + Date.now();

            // Create the notification HTML
            let bgColor = '';
            let icon = '';

            switch (type) {
                case 'success':
                    bgColor = '#38b000';
                    icon = '<i class="fas fa-check-circle mr-2"></i>';
                    break;
                case 'error':
                    bgColor = '#ef233c';
                    icon = '<i class="fas fa-exclamation-circle mr-2"></i>';
                    break;
                case 'warning':
                    bgColor = '#fb8500';
                    icon = '<i class="fas fa-exclamation-triangle mr-2"></i>';
                    break;
                case 'info':
                default:
                    bgColor = '#3a86ff';
                    icon = '<i class="fas fa-info-circle mr-2"></i>';
                    break;
            }

            const notificationHTML = `
            <div id="${id}" class="notification" style="
                background-color: ${bgColor};
                color: white;
                padding: 15px 20px;
                border-radius: 4px;
                margin-bottom: 10px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-width: 300px;
                opacity: 0;
                transform: translateX(100px);
                transition: all 0.3s ease;
            ">
                <div>${icon}${message}</div>
                <button class="close-btn" style="
                    background: none;
                    border: none;
                    color: white;
                    font-size: 16px;
                    cursor: pointer;
                    margin-left: 10px;
                    padding: 0;
                ">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            // Add the notification to the container
            $('#notification-container').append(notificationHTML);

            // Show the notification with animation
            setTimeout(() => {
                $(`#${id}`).css({
                    'opacity': '1',
                    'transform': 'translateX(0)'
                });
            }, 10);

            // Set up the close button
            $(`#${id} .close-btn`).click(function() {
                removeNotification(id);
            });

            // Auto-remove after 5 seconds
            setTimeout(() => {
                removeNotification(id);
            }, 5000);
        };

        // Function to remove a notification with animation
        removeNotification = function(id) {
            $(`#${id}`).css({
                'opacity': '0',
                'transform': 'translateX(100px)'
            });

            setTimeout(() => {
                $(`#${id}`).remove();
            }, 300);
        };        
    </script>
    @yield('script')

</body>

</html>
