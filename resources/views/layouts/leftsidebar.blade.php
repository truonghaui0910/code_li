<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">
        <!--- Divider -->
        <div id="sidebar-menu">
            <ul>
                <!--<li class="menu-title">Main</li>-->
                @if($isAdmin)
                <li>
                    <a href="/dashboard" class="waves-effect waves-primary"><i class="ti-home"></i><span> Dashboard </span></a>
                </li>
                @endif
<!--                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary d-flex justify-content-start">
                        <i class="mdi mdi-new-box color-red" style="font-size: 25px;"></i>
                        <svg style="margin-left: 5px;margin-right: 20px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                            <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/>
                        </svg>
                        <span> Live Tiktok 2.0</span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a href="/tiktok" class="waves-effect waves-primary"> <span>Live Tiktok 2.0</span></a></li>
                        <li><a href="/ttpricing" class="waves-effect waves-primary"><span>Gói Cước Tiktok 2.0</span></a></li>

                    </ul>
                </li>-->
<!--                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="mdi mdi-new-box color-red" style="font-size: 25px;"></i><span> Shopee </span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a href="/shopee" class="waves-effect waves-primary"> <span>Live Shopee</span></a></li>
                        <li><a href="/sppricing" class="waves-effect waves-primary"><span>Gói Cước Shopee</span></a></li>

                    </ul>
                </li>-->
   

<!--                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i class="ti-video-camera"></i><span> Live Youtube </span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a href="/live" class="waves-effect waves-primary"><span>Live Youtube/Facebook</span></a></li>
                        <li><a href="/pricing" class="waves-effect waves-primary"><span>Gói Cước Youtube/Facebook</span></a></li>

                    </ul>
                </li>-->
                <li><a href="/pricing" class="waves-effect waves-primary"><i class="ti-package"></i><span>Bảng giá</span></a></li>
                <li><a href="/live" class="waves-effect waves-primary"><i class="ti-video-camera"></i><span>Live Youtube/Facebook</span></a></li>
                <li><a href="/tiktok" class="waves-effect waves-primary"><svg style="margin-left: 5px;margin-right: 20px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16">
                            <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/>
                        </svg><span>Live Tiktok 2.0</span></a></li>
                <li><a href="/shopee" class="waves-effect waves-primary"> <i class="mdi mdi-new-box color-red" style="font-size: 25px;"></i><span>Live Shopee</span></a></li>
                <li><a href="/profile" class="waves-effect waves-primary"><i class="ti-info-alt"></i><span>Cá Nhân</span></a></li>

                <!--                <li>
                                    <a target="_blank" href="https://youtu.be/4M3tEabb1SI" class="waves-effect waves-primary"><i class=" ti-book"></i><span>Hướng Dẫn</span></a>
                                </li>-->
                @if($isTiktok)
                <!--                <li>
                                    <a target="_blank" href="https://youtu.be/yFdMwUSYjBY" class="waves-effect waves-primary"><i class=" ti-book"></i><span>Hướng Dẫn Tiktok</span></a>
                                </li>-->
                @endif
                @if($isAdmin)

                <li>
                    <a href="/customer" class="waves-effect waves-primary"><i class="ti-user"></i><span>Customer</span></a>
                </li>
                @if($isAdmin || $isTax)
                <li>
                    <a href="/vatInvoice" class="waves-effect waves-primary"><i class=" fa fa-tags"></i><span>Tax Invoice</span>
                        @if(isset($count_tax))
                            <span class=" badge badge-pink noti-icon-badge" style="position: absolute;right: 20px;">{{$count_tax}}</span>
                        @endif
                    </a>
                </li>
                @endif
                <li>
                    <a href="/invoice" class="waves-effect waves-primary"><i class="ti-credit-card"></i><span>Invoice</span></a>
                </li>
                <li>
                    <a href="/listv3" class="waves-effect waves-primary"><i class=" ti-list"></i><span>Tiktok List V3</span>
                        @if(isset($count_waiting_v3))
                            <span class=" badge badge-pink noti-icon-badge" style="position: absolute;right: 20px;">{{$count_waiting_v3}}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a target="_blank" href="/padmin" class="waves-effect waves-primary"><i class="fa fa-database"></i><span>DB</span></a>
                </li>
                @endif
                <li>
                    <a href="/suport" class="waves-effect waves-primary"><i class="fa fa-phone"></i><span>Hỗ Trợ</span></a>
                </li>
                <li>
                    <a href="/guidle" class="waves-effect waves-primary"><i class="fa fa-graduation-cap"></i><span>Hướng dẫn</span></a>
                </li>
                <li class="has_sub">
                    <a href="javascript:void(0);" class="waves-effect waves-primary"><i
                            class="ti-light-bulb"></i><span> Video Hướng Dẫn </span> <span class="menu-arrow"></span> </a>
                    <ul class="list-unstyled">
                        <li><a target="_blank" href="https://youtu.be/-xf5g_lVE2s">Hướng dẫn Shopee</a></li>
                        <li><a target="_blank" href="https://youtu.be/4M3tEabb1SI">Hướng Dẫn Live</a></li>
                        <li><a target="_blank" href="https://youtu.be/yFdMwUSYjBY">Hướng Dẫn Tiktok</a></li>
                        <li><a target="_blank" href="https://youtu.be/mI4dS2Fhkbs?t=259">Hướng dẫn live Tiktok</a></li>
                        <li><a target="_blank" href="https://youtu.be/mI4dS2Fhkbs?t=1190">Hướng dẫn lách vi phạm live tiktok</a></li>
                        <li><a target="_blank" href="https://youtu.be/mI4dS2Fhkbs?t=1645">Hướng dẫn thêm sản phẩm</a></li>

                    </ul>
                </li>
                <li>
                    <a href="/privacy" class="waves-effect waves-primary"><i class="ti-shield"></i><span>Chính Sách</span></a>
                </li>

                
            </ul>

            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

