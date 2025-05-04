
<div class="topbar" >

    <!-- LOGO -->
    <div class="topbar-left">
        <div class="text-center">
            <!--            <a href="/" class="logo">
                            <img src="images/logo_live5.png" style="width: 40px">
                            <span>Autolive</span></a>-->
            <a href="/" class="logo">
                <img src="images/Autolive_logo.png" style="width: 40px">
                <span><img src="images/Autolive_logo_text.png" style="width: 92px;margin-top: -4px;"></span></a>
        </div>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <nav class="navbar-custom">

        <ul class="list-inline float-right mb-0 right-menu">

<!--            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <i class="mdi mdi-bell noti-icon"></i>
                    <span class="badge badge-pink noti-icon-badge">0</span>
                </a>                
                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg" aria-labelledby="Preview">
     
                    <div class="dropdown-item noti-title">
                        <h5 class="font-16"><span class="badge badge-danger float-right">0</span>Thông báo</h5>
                    </div>

                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <div class="notify-icon bg-danger"><i class="mdi mdi-airplane"></i></div>
                        <p class="notify-details">Chào mừng bạn đến với Autolive <b>Admin</b><small class="text-muted">1 min ago</small></p>
                    </a>

        
                    <a href="javascript:void(0);" class="dropdown-item notify-item notify-all">
                        Xem tất cả
                    </a>

                </div>
            </li>-->

            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user btn-dialog-bonus theme-dark" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <svg data-toggle="tooltip" data-placement="top"  data-original-title="Nhập mã nhận thưởng" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gift" viewBox="0 0 16 16">
                        <path d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43a.522.522 0 0 0 .023.07M9 3h2.932a.56.56 0 0 0 .023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0zM1 4v2h6V4zm8 0v2h6V4zm5 3H9v8h4.5a.5.5 0 0 0 .5-.5zm-7 8V7H2v7.5a.5.5 0 0 0 .5.5z"/>
                    </svg>

                </a>
     
            </li>
            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user btn-change-theme theme-dark" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                     <!--<i class="fa fa-moon-o"></i>--> 
                     <svg data-toggle="tooltip" data-placement="top"  data-original-title="Chế độ sáng tối" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-moon-fill" viewBox="0 0 16 16">
                        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
                      </svg>
                </a>
     
            </li>
            <li class="list-inline-item dropdown notification-list">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <img src="images/default-avatar.png" alt="user" class="rounded-circle"> @if(isset($user_login)){{$user_login->user_name}}@endif
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown " aria-labelledby="Preview">
                    <!-- item-->
                    <!--                    <a href="/profile" class="dropdown-item notify-item">
                                            <i class="mdi mdi-account-star-variant"></i> <span>Profile</span>
                                        </a>-->
                    <!-- item-->
                    
                    <a onclick="showProductSetModal(1)" class="dropdown-item notify-item cur-point">
                        <i class="fas fa-layer-group"></i> <span>Product</span>
                    </a>
                    <a href="logout" class="dropdown-item notify-item">
                        <i class="mdi mdi-logout"></i> <span>Logout</span>
                    </a>

                </div>
            </li>

        </ul>

        <ul class="list-inline menu-left mb-0 d-flex align-items-center">
            <li class="float-left">
                <button class="button-menu-mobile open-left waves-light waves-effect">
                    <i class="mdi mdi-menu"></i>
                </button>
            </li>
<!--            <li class="float-left hide-phone">
                <span>Bạn vui lòng truy cập: <a href="https://autolive.me/login" class="">Autolive.me</a> để dùng tool mượt hơn.</span>
            </li>-->

        </ul>

    </nav>

</div>
