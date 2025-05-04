@extends('layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        @if(session('message'))
        <div class="alert alert-pink text-center">
            {{session('message')}}
        </div>
        @endif  
    </div>
    <div class="col-md-10">
        <div class="row">
            @foreach($datas as $data)
            <div  class="pricing-column col-lg-3 col-md-3">
                @if($data->discount_per >0)
                <div class="ribbon"><span>Giảm -{{$data->discount_per}}%</span></div>
                @endif
                <div  class="inner-box card-box">
                    <div  class="plan-header text-center">
                        <h3  class="plan-title">{{$data->package_code}}</h3>
                        @if($data->package_code=='TIKTOK1')
                        <h4  class="color-red text-line-through">&nbsp;</h4>
                        @else
                        <h4  class="color-red text-line-through">{{number_format($data->number_live * 500000, 0, ',', '.')}}</h4>
                        @endif
                        <h2  class="plan-price <?php echo $data->discount_per >0?"text-line-through":"";?>">{{number_format($data->price, 0, ',', '.')}}</h2>
                        @if($data->discount_per >0)
                        <h3  class="color-violet">{{number_format($data->price - ($data->price*$data->discount_per/100), 0, ',', '.')}}</h3>
                        @endif
                        <div  class="plan-duration">1 Tháng</div>
                    </div>
                    <ul  class="plan-stats list-unstyled text-center">
                        <li ><i  class="ti-key text-success"></i> {{$data->number_live}} LUỒNG ONLINE</li>
                        <li ><i  class="ti-user text-success"></i> {{$data->number_account}} TÀI KHOẢN TIKTOK</li>
                        <li ><i  class="ti-unlock text-success"></i> LÁCH VI PHẠM, VƯỢT CAPTCHA</li>
                        <li ><i  class="ion-earth text-success"></i> ĐỊA CHỈ IP CƯ DÂN</li>
                        <li ><i  class="ion-monitor text-success"></i> RESET THIẾT BỊ</li>
                        <li ><i  class="ion-volume-high text-success"></i> THÔNG BÁO VI PHẠM</li>
                        <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                        <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                        <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                        <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                    </ul>
                    <div class="text-center">
                        <a href="invoice/{{strtolower($data->package_code)}}" class="btn {{$data->btn_class}} btn-bordred btn-rounded waves-effect waves-light">{{$data->btn_text}}</a>
                    </div>
                </div>
            </div>
            @endforeach
            <div  class="pricing-column col-lg-3 col-md-3">
                <div  class="inner-box card-box">
                    <div  class="plan-header text-center">
                        <h3  class="plan-title">TIKTOK VIP</h3>
                        <h2  class="plan-price">1.000.000</h2>
                        <div  class="plan-duration">1 Tháng</div>
                    </div>
                    <ul  class="plan-stats list-unstyled text-center">
                        <li ><i  class="ti-key text-success"></i> 1 LUỒNG ONLINE</li>
                        <li ><i  class="ti-user text-success"></i> 10 TÀI KHOẢN TIKTOK</li>
                        <li ><i  class="ti-server text-success"></i> SERVER RIÊNG THEO QUỐC GIA </li>
                        <li ><i  class="ion-earth text-success"></i> IP CƯ DÂN KHÔNG GIỚI HẠN </li>
                        <li ><i  class="ion-monitor text-success"></i> RESET THIẾT BỊ KHÔNG GIỚI HẠN</li>
                        <li ><i  class="ion-volume-high text-success"></i> THÔNG BÁO VI PHẠM</li>
                        <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                        <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                        <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                        <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                    </ul>
                    <div class="text-center">
                        <a target="_blank" href="https://www.facebook.com/messages/t/100002470941874" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">LIÊN HỆ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">

</script>
@endsection