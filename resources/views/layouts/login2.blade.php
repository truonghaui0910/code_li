<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="description" content="Autolive.win">
        <meta name="author" content="Autowin Team">

        <link rel="shortcut icon" href="images/Autolive_logo.png">

        <title>Autolive.win</title>
        <base href="{{asset('')}}">

        <link href="assets/plugins/switchery/switchery.min.css" rel="stylesheet" />
        <link href="assets/plugins/custombox/dist/custombox.min.css" rel="stylesheet" />
        <link href="assets/plugins/tablesaw/dist/tablesaw.css" rel="stylesheet" />
        <link href="assets/plugins/jquery-circliful/css/jquery.circliful.css" rel="stylesheet" type="text/css" />

        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="assets/css/style.css" rel="stylesheet" type="text/css">
        <link href="css/style.css" rel="stylesheet" type="text/css">

        <script src="assets/js/modernizr.min.js"></script>


    </head>
    <body class="body-style">
        <div class="bg-overlay"></div>
        <div class="wrapper-page">

            <div class="text-center">
                <a href="/" class="logo-lg">
                    <!--<img src="images/logo.png" style="width: 40px">--> 
                    <span class="color-violet">AUTOLIVE.WIN</span> </a>
            </div>

            <form class="form-horizontal m-t-20" action="/login" method="POST">
                {{ csrf_field() }}
                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="mdi mdi-account"></i></span>
                            <input id="user_name" class="form-control" name="user_name" type="text"  placeholder="Username" value="{{ old('user_name') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-addon"><i class=" mdi mdi-lock"></i></span>
                            <input id="password" class="form-control" name="password" type="password" placeholder="Password">
                        </div>
                    </div>
                </div>



                <div class="form-group text-right m-t-20">
                    <div class="col-xs-12 p-0">
                        <button class="btn btn-violet btn-custom w-md waves-effect waves-light bg-violet" type="submit">{{trans('Đăng nhập')}}</button>
                    </div>
                </div>
                <div class="form-group row m-t-30">
                    <div class="col-sm-7">
                        <a href="https://www.facebook.com/messages/t/100002470941874" class="text-muted"><i class="fa fa-lock m-r-5"></i> {{trans('Quên mật khẩu?')}}</a>
                    </div>
                    <div class="col-sm-5 text-right">
                        <a href="/register" class="text-muted">{{trans('Tạo tài khoản mới')}}</a>
                    </div>
                </div>

            </form>
            @if ($errors->any())
            <div class="alert alert-violet">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if(session('message'))
            <div class="alert alert-violet">
                {!!session('message')!!}
            </div>
            @endif  
        </div>

        <script>
var resizefunc = [];
        </script>

        <!-- Plugins  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
        <script src="assets/js/bootstrap.min.js"></script>
        <!-- <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script> -->
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <!-- <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="assets/plugins/switchery/switchery.min.js"></script> -->

        <!-- Notification js -->
        <!-- <script src="assets/plugins/notifyjs/dist/notify.min.js"></script>
        <script src="assets/plugins/notifications/notify-metro.js"></script> -->

        <!-- Modal-Effect -->
        <!-- <script src="assets/plugins/custombox/dist/custombox.min.js"></script>
        <script src="assets/plugins/custombox/dist/legacy.min.js"></script> -->

        <!-- Counter Up  -->
        <!-- <script src="assets/plugins/waypoints/lib/jquery.waypoints.min.js"></script>
        <script src="assets/plugins/counterup/jquery.counterup.min.js"></script> -->

        <!-- circliful Chart -->
        <!-- <script src="assets/plugins/jquery-circliful/js/jquery.circliful.min.js"></script>
        <script src="assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script> -->

        <!-- skycons -->
        <!-- <script src="assets/plugins/skyicons/skycons.min.js" type="text/javascript"></script> -->

        <!-- Page js  -->
        <!-- <script src="assets/pages/jquery.dashboard.js"></script> -->

        <!-- Custom main Js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>


        <script type="text/javascript">
jQuery(document).ready(function ($) {

});

        </script>
        @yield('script')

    </body>
</html>