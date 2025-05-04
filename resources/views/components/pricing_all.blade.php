@extends('layouts.master')

@section('content')
  <style>
    :root {
      --primary-color: #6a0dad;
      --primary-light: #8a2be2;
      --primary-dark: #4b0082;
      --accent-color: #9370db;
      --text-color: #333;
      --text-light: #666;
      --bg-color: #f8f9fa;
      --card-bg: #fff;
      --card-border: rgba(0, 0, 0, 0.05);
      --border-color: #eee;
    }

    .theme--dark {
      --primary-color: #8e44ad;
      --primary-light: #9b59b6;
      --primary-dark: #6c3483;
      --accent-color: #a569bd;
      --text-color: #f1f1f1;
      --text-light: #ccc;
      --bg-color: #121212;
      --card-bg: #1e1e1e;
      --card-border: rgba(255, 255, 255, 0.1);
      --border-color: #333;
    }
    
/*    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: all 0.3s ease;
    }*/
    
    .pricing-header {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
      color: white;
      text-align: center;
      padding: 3rem 0;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
    }
    
    .theme-switch {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 1000;
    }
    
    .theme-switch button {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .theme-switch button:hover {
      background-color: var(--primary-dark);
    }
    
    .service-tabs {
      display: flex;
      justify-content: center;
      margin-bottom: 40px;
    }
    
    .service-tab {
      background-color: var(--card-bg);
      color: var(--text-color);
      border: 2px solid var(--primary-color);
      border-radius: 30px;
      padding: 10px 30px;
      margin: 0 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 600;
    }
    
    .service-tab.active {
      background-color: var(--primary-color);
      color: white;
    }
    
    .service-tab:hover:not(.active) {
      background-color: var(--primary-light);
      color: white;
    }
    
    .service-content {
      display: none;
    }
    
    .service-content.active {
      display: block;
    }
    
    .pricing-card {
      background-color: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 15px;
      transition: transform 0.3s, box-shadow 0.3s;
      overflow: hidden;
      height: 100%;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .pricing-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
      background-color: var(--primary-color);
      color: white;
      text-align: center;
      padding: 20px 0;
      font-size: 24px;
      font-weight: bold;
    }
    
    .price {
      font-size: 42px;
      font-weight: bold;
      color: var(--primary-color);
      margin: 20px 0;
    }
    
    .price-period {
      font-size: 16px;
      color: var(--text-light);
    }
    
    .feature-list {
      list-style: none;
      padding-left: 0;
      margin: 30px 0;
    }
    
    .feature-list li {
      padding: 10px 0;
      border-bottom: 1px solid var(--border-color);
      color: var(--text-color);
    }
    
    .feature-list li:last-child {
      border-bottom: none;
    }
    
    .feature-icon {
      color: var(--primary-color);
      margin-right: 10px;
    }
    
    .btn-subscribe {
      /*background-color: var(--primary-color);*/
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: bold;
      transition: background-color 0.3s;
      margin-top: 20px;
    }
    
    .btn-subscribe:hover {
      /*background-color: var(--primary-dark);*/
      color: white;
      transform: scale(1.05);
    }
    
    .card-footer {
      background-color: var(--card-bg);
      border-top: none;
      text-align: center;
      padding-bottom: 30px;
    }
    
    .badge-feature {
      background-color: var(--accent-color);
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      margin-left: 5px;
    }
    
    .service-description {
      text-align: center;
      margin-bottom: 40px;
      color: var(--text-color);
    }
    
    @media (max-width: 768px) {
      .pricing-card {
        margin-bottom: 30px;
      }
      
      .service-tabs {
        flex-direction: column;
        align-items: center;
      }
      
      .service-tab {
        margin-bottom: 10px;
        width: 80%;
      }
    }
  </style>
<div class="row justify-content-center">
    <div class="col-md-8">
        @if(session('message'))
        <div class="alert alert-pink text-center">
            {{session('message')}}
        </div>
        @endif  
    </div>
  
</div>

<div class="container py-5">
    <div class="service-tabs">
      <div class="service-tab" data-service="youtube">Live Youtube</div>
      <div class="service-tab" data-service="tiktok">Live TikTok</div>
      <div class="service-tab" data-service="shopee">Live Shopee</div>
    </div>

    <!-- Youtube Service -->
    <div id="youtube-service" class="service-content">
      <div class="service-description">
        <h2>Dịch vụ Live Youtube/Facebook</h2>
        <p>Giải pháp live stream chuyên nghiệp cho Youtube với nhiều lựa chọn phù hợp với mọi nhu cầu</p>
      </div>
      
      <div class="row">
        @foreach($datas as $data)
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="pricing-card card">
            <div class="card-header">
              {{$data->package_code}}
            </div>
            <div class="card-body text-center">
            @if($data->package_code=='LIVE1')
            <h4  class="color-red text-line-through">&nbsp;</h4>
            @else
            <h4  class="color-red text-line-through">{{number_format($data->number_live * 200000, 0, ',', '.')}}</h4>
            @endif
            <div class="price">{{number_format($data->price, 0, ',', '.')}} <span class="price-period">/ tháng</span></div>
            <!--<p>Gói cơ bản cho người mới bắt đầu</p>-->
            <ul class="feature-list">
              <li><i class="feature-icon">✓</i> Live cùng lúc <strong>{{$data->number_live}}</strong> luồng</li>
              <li><i class="feature-icon">✓</i> <strong>{{$data->number_account}}</strong> tài khoản quản lý</li>
              <li><i class="feature-icon">✓</i> Live Youtube liên tục <strong>24/7</strong></li>
              <li><i class="feature-icon">✓</i> <strong>không cần</strong> Treo máy</li></li>
              <li><i class="feature-icon">✓</i> <strong>không cần</strong> VPS</li>
              <li><i class="feature-icon">✓</i> Chất lượng <strong>1080p</strong> <span class="badge-feature">Full HD</span></li>
              <li><i class="feature-icon">✓</i> Hỗ trợ <strong>24/7</strong></li>
            </ul>
            </div>
            <div class="card-footer">
              <a href="invoice/{{strtolower($data->package_code)}}" class="btn {{$data->btn_class}} btn-subscribe ">{{$data->btn_text}}</a>
            </div>
          </div>
        </div>
        @endforeach
        <!-- LIVE50 -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="pricing-card card">
            <div class="card-header">
              LIVE VIP
            </div>
            <div class="card-body text-center">
              <div class="price">Thỏa thuận</div>
              <p>Gói dành cho doanh nghiệp lớn</p>
              <ul class="feature-list">
                <li><i class="feature-icon">✓</i> <strong>thỏa thuận</strong> số luồng live cùng lúc</li>
                <li><i class="feature-icon">✓</i> <strong>thỏa thuận</strong> số tài khoản quản lý</li>
                <li><i class="feature-icon">✓</i> Live Youtube liên tục <strong>24/7</strong></li>
                <li><i class="feature-icon">✓</i> <strong>không cần</strong> VPS</li>
                <li><i class="feature-icon">✓</i> <strong>không cần</strong> Treo máy</li>
                <li><i class="feature-icon">✓</i> Chất lượng <strong>4K</strong> <span class="badge-feature">Ultra HD</span></li>
                <li><i class="feature-icon">✓</i> Hỗ trợ ưu tiên <strong>24/7</strong></li>
              </ul>
            </div>
            <div class="card-footer">
              <a target="_blank" href="https://www.facebook.com/messages/t/100002470941874" class="btn btn-violet btn-subscribe">Liên Hệ</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TikTok Service -->
    <div id="tiktok-service" class="service-content">
      <div class="service-description">
        <h2>Dịch vụ Live TikTok</h2>
        <p>Giải pháp live stream TikTok chuyên nghiệp cho người sáng tạo nội dung</p>
      </div>
      
    <div class="row">
        @foreach($datasTt as $data)
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="pricing-card card">
            <div class="card-header">
              {{$data->package_code}}
            </div>
            <div class="card-body text-center">
            @if($data->package_code=='LIVE1')
            <h4  class="color-red text-line-through">&nbsp;</h4>
            @else
            <h4  class="color-red text-line-through">{{number_format($data->number_live * 200000, 0, ',', '.')}}</h4>
            @endif
            <div class="price">{{number_format($data->price, 0, ',', '.')}} <span class="price-period">/ tháng</span></div>
            <!--<p>Gói cơ bản cho người mới bắt đầu</p>-->
            <ul class="feature-list">
              <li><i class="feature-icon">✓</i> Live cùng lúc <strong>{{$data->number_live}}</strong> luồng</li>
              <li><i class="feature-icon">✓</i> <strong>{{$data->number_account}}</strong> tài khoản tiktok</li>
              <li><i class="feature-icon">✓</i> Lách vi phạm, vượt captcha</li>
              <li><i class="feature-icon">✓</i> Địa chỉ ip cư dân</li>
              <li><i class="feature-icon">✓</i> Reset thiết bị</li>
              <li><i class="feature-icon">✓</i> Thông báo vi phạm</li>
              <li><i class="feature-icon">✓</i> <strong>Không cần</strong> Treo máy</li></li>
              <li><i class="feature-icon">✓</i> <strong>Không cần</strong> VPS</li>
              <li><i class="feature-icon">✓</i> Chất lượng <strong>1080p</strong> <span class="badge-feature">Full HD</span></li>
              <li><i class="feature-icon">✓</i> Hỗ trợ <strong>24/7</strong></li>
            </ul>
            </div>
            <div class="card-footer">
              <a href="invoice/{{strtolower($data->package_code)}}" class="btn {{$data->btn_class}} btn-subscribe ">{{$data->btn_text}}</a>
            </div>
          </div>
        </div>
        @endforeach
        <!-- LIVE50 -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="pricing-card card">
            <div class="card-header">
              TIKTOK VIP
            </div>
            <div class="card-body text-center">
              <div class="price">Thỏa thuận</div>
              <p>Gói dành cho doanh nghiệp lớn</p>
              <ul class="feature-list">
                <li><i class="feature-icon">✓</i> <strong>Thỏa thuận</strong> số luồng live cùng lúc</li>
                <li><i class="feature-icon">✓</i> <strong>Thỏa thuận</strong> tài khoản tiktok</li>
                <li><i class="feature-icon">✓</i> Lách vi phạm, vượt captcha</li>
                <li><i class="feature-icon">✓</i> Địa chỉ ip cư dân</li>
                <li><i class="feature-icon">✓</i> Reset thiết bị</li>
                <li><i class="feature-icon">✓</i> Thông báo vi phạm</li>
                <li><i class="feature-icon">✓</i> <strong>Không cần</strong> Treo máy</li></li>
                <li><i class="feature-icon">✓</i> <strong>Không cần</strong> VPS</li>
                <li><i class="feature-icon">✓</i> Chất lượng <strong>4K</strong> <span class="badge-feature">Ultra HD</span></li>
                <li><i class="feature-icon">✓</i> Hỗ trợ ưu tiên <strong>24/7</strong></li>

              </ul>
            </div>
            <div class="card-footer">
              <a target="_blank" href="https://www.facebook.com/messages/t/100002470941874" class="btn btn-violet btn-subscribe">Liên Hệ</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Shoppe Service -->
    <div id="shopee-service" class="service-content">
      <div class="service-description">
        <h2>Dịch vụ Live Shoppe</h2>
        <p>Giải pháp live stream Shoppe chuyên nghiệp cho shop</p>
      </div>
      <div class="row">
        @foreach($datasSp as $data)
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="pricing-card card">
            <div class="card-header">
              {{$data->package_code}}
            </div>
            <div class="card-body text-center">
            @if($data->package_code=='LIVE1')
            <h4  class="color-red text-line-through">&nbsp;</h4>
            @else
            <h4  class="color-red text-line-through">{{number_format($data->number_live * 200000, 0, ',', '.')}}</h4>
            @endif
            <div class="price">{{number_format($data->price, 0, ',', '.')}} <span class="price-period">/ tháng</span></div>
            <!--<p>Gói cơ bản cho người mới bắt đầu</p>-->
            <ul class="feature-list">
              <li><i class="feature-icon">✓</i> Live cùng lúc <strong>{{$data->number_live}}</strong> luồng</li>
              <li><i class="feature-icon">✓</i> <strong>{{$data->number_account}}</strong> tài khoản quản lý</li>
              <li><i class="feature-icon">✓</i> Live Youtube liên tục <strong>24/7</strong></li>
              <li><i class="feature-icon">✓</i> <strong>Không cần</strong> Treo máy</li></li>
              <li><i class="feature-icon">✓</i> <strong>Không cần</strong> VPS</li>
              <li><i class="feature-icon">✓</i> Chất lượng <strong>1080p</strong> <span class="badge-feature">Full HD</span></li>
              <li><i class="feature-icon">✓</i> Hỗ trợ <strong>24/7</strong></li>
            </ul>
            </div>
            <div class="card-footer">
              <a href="invoice/{{strtolower($data->package_code)}}" class="btn {{$data->btn_class}} btn-subscribe ">{{$data->btn_text}}</a>
            </div>
          </div>
        </div>
        @endforeach
        <!-- LIVE50 -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="pricing-card card">
            <div class="card-header">
              SHOPPE VIP
            </div>
            <div class="card-body text-center">
              <div class="price">Thỏa thuận</div>
              <p>Gói dành cho doanh nghiệp lớn</p>
              <ul class="feature-list">
                <li><i class="feature-icon">✓</i> <strong>Thỏa thuận</strong> số luồng live cùng lúc</li>
                <li><i class="feature-icon">✓</i> <strong>Thỏa thuận</strong> số tài khoản quản lý</li>
                <li><i class="feature-icon">✓</i> Live Youtube liên tục <strong>24/7</strong></li>
                <li><i class="feature-icon">✓</i> <strong>Không cần</strong> VPS</li>
                <li><i class="feature-icon">✓</i> <strong>Không cần</strong> Treo máy</li>
                <li><i class="feature-icon">✓</i> Chất lượng <strong>4K</strong> <span class="badge-feature">Ultra HD</span></li>
                <li><i class="feature-icon">✓</i> Hỗ trợ ưu tiên <strong>24/7</strong></li>
              </ul>
            </div>
            <div class="card-footer">
              <a target="_blank" href="https://www.facebook.com/messages/t/100002470941874" class="btn btn-violet btn-subscribe">Liên Hệ</a>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
  $('.service-tab').on('click', function () {
//    // Xóa lớp active khỏi tất cả tab
//    $('.service-tab').removeClass('active');
//    
//    // Ẩn tất cả nội dung dịch vụ
//    $('.service-content').removeClass('active');
//    
//    // Thêm lớp active vào tab được chọn
//    $(this).addClass('active');
//    
//    // Hiển thị nội dung tương ứng
//    var service = $(this).data('service');
//    $('#' + service + '-service').addClass('active');

  });
    let defaultTab = "{{$default}}"; // Đổi thành "tiktok" hoặc "shopee" nếu muốn tab khác làm mặc định
  
  $(".service-tab").removeClass("active");
  $(".service-content").removeClass("active");

  $(`.service-tab[data-service="${defaultTab}"]`).addClass("active");
  $(`#${defaultTab}-service`).addClass("active");

  $(".service-tab").click(function () {
    let service = $(this).attr("data-service");

    $(".service-tab").removeClass("active");
    $(".service-content").removeClass("active");

    $(this).addClass("active");
    $(`#${service}-service`).addClass("active");
  });
</script>
@endsection