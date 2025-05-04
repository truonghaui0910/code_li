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
                        @if($data->package_code=='LIVE1')
                        <h4  class="color-red text-line-through">&nbsp;</h4>
                        @else
                        <h4  class="color-red text-line-through">{{number_format($data->number_live * 200000, 0, ',', '.')}}</h4>
                        @endif
                        <h2  class="plan-price <?php echo $data->discount_per >0?"text-line-through":"";?>">{{number_format($data->price, 0, ',', '.')}}</h2>
                        @if($data->discount_per >0)
                        <h3  class="color-violet">{{number_format($data->price - ($data->price*$data->discount_per/100), 0, ',', '.')}}</h3>
                        @endif
                        <div  class="plan-duration">1 Tháng</div>
                    </div>
                    <ul  class="plan-stats list-unstyled text-center">
                        <li ><i  class="ti-key text-success"></i> {{$data->number_live}} LUỒNG ONLINE</li>
                        <li ><i  class="ti-user text-success"></i> {{$data->number_account}} TÀI KHOẢN QUẢN LÝ</li>
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
                        <h3  class="plan-title">Autolive VIP</h3>
                        <h2  class="plan-price">Thỏa thuận</h2>
                    </div>
                    <ul  class="plan-stats list-unstyled text-center">
                        <li ><i  class="ti-key text-success"></i> THỎA THUẬN</li>
                        <li ><i  class="ti-user text-success"></i> THỎA THUẬN</li>
                        <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                        <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                        <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 4K</li>
                        <li ><i  class="ti-headphone-alt text-success"></i> ĐƯỢC CHĂM SÓC RIÊNG </li>
                        <li ><i  class="ti-money text-success"></i> ƯU ĐÃI KHÔNG GIỚI HẠN</li>
                    </ul>
                    <div class="text-center">
                        <a target="_blank" href="https://www.facebook.com/messages/t/100002470941874" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">LIÊN HỆ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="row">
    <div class="col-lg-12 fadeInDown animated">
        <div class="card-box" style="min-height: 73vh">
            <h4 class="header-title m-t-0 m-b-30">Danh sách gói </h4>
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
                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive 1</h3>
                                    <h4  class="color-red text-line-through">&nbsp;</h4>
                                    <h2  class="plan-price">200.000</h2>
                                    <div  class="plan-duration">1 Tháng</div>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> 1 LUỒNG SONG SONG</li>
                                    <li ><i  class="ti-user text-success"></i> 1 TÀI KHOẢN QUẢN LÝ</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                                </ul>
                                <div class="text-center">
                                    <a href="invoice/live1" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Mua Ngay</a>
                                </div>
                            </div>
                        </div>
                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="ribbon"><span >POPULAR</span></div>
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive 3</h3>
                                    <h4  class="color-red text-line-through">600.000</h4>
                                    <h2  class="plan-price">550.000</h2>
                                    <div  class="plan-duration">1 Tháng</div>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> 3 LUỒNG SONG SONG</li>
                                    <li ><i  class="ti-user text-success"></i> 2 TÀI KHOẢN QUẢN LÝ</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                                </ul>
                                <div class="text-center">
                                    <a href="invoice/live3" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Mua Ngay</a>
                                </div>
                            </div>
                        </div>
                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive 5</h3>
                                    <h4  class="color-red text-line-through">1.000.000</h4>
                                    <h2  class="plan-price">900.000</h2>
                                    <div  class="plan-duration">1 Tháng</div>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> 5 LUỒNG SONG SONG</li>
                                    <li ><i  class="ti-user text-success"></i> 3 TÀI KHOẢN QUẢN LÝ</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                                </ul>
                                <div class="text-center">
                                    <a href="invoice/live5" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Mua Ngay</a>
                                </div>
                            </div>
                        </div>
                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive 10</h3>
                                    <h4  class="color-red text-line-through">2.000.000</h4>
                                    <h2  class="plan-price">1.800.000</h2>
                                    <div  class="plan-duration">1 Tháng</div>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> 10 LUỒNG SONG SONG</li>
                                    <li ><i  class="ti-user text-success"></i> 4 TÀI KHOẢN QUẢN LÝ</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                                </ul>
                                <div class="text-center">
                                    <a href="invoice/live10" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Mua Ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive 20</h3>
                                    <h4  class="color-red text-line-through">4.000.000</h4>
                                    <h2  class="plan-price">3.500.000</h2>
                                    <div  class="plan-duration">1 Tháng</div>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> 20 LUỒNG SONG SONG</li>
                                    <li ><i  class="ti-user text-success"></i> 5 TÀI KHOẢN QUẢN LÝ</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                                </ul>
                                <div class="text-center">
                                    <a href="invoice/live20" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Mua Ngay</a>
                                </div>
                            </div>
                        </div>
                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive 30</h3>
                                    <h4  class="color-red text-line-through">6.000.000</h4>
                                    <h2  class="plan-price">5.200.000</h2>
                                    <div  class="plan-duration">1 Tháng</div>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> 30 LUỒNG SONG SONG</li>
                                    <li ><i  class="ti-user text-success"></i> 6 TÀI KHOẢN QUẢN LÝ</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                                </ul>
                                <div class="text-center">
                                    <a href="invoice/live30" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Mua Ngay</a>
                                </div>
                            </div>
                        </div>
                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive 50</h3>
                                    <h4  class="color-red text-line-through">10.000.000</h4>
                                    <h2  class="plan-price">8.000.000</h2>
                                    <div  class="plan-duration">1 Tháng</div>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> 50 LUỒNG SONG SONG</li>
                                    <li ><i  class="ti-user text-success"></i> 8 TÀI KHOẢN QUẢN LÝ</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 1080p</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> HỖ TRỢ MIỄN PHÍ </li>
                                </ul>
                                <div class="text-center">
                                    <a href="invoice/live50" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Mua Ngay</a>
                                </div>
                            </div>
                        </div>
                        <div  class="pricing-column col-lg-3 col-md-3">
                            <div  class="inner-box card-box pricing-box">
                                <div  class="plan-header text-center">
                                    <h3  class="plan-title">Autolive VIP</h3>
                                    <h2  class="plan-price">Thỏa thuận</h2>
                                </div>
                                <ul  class="plan-stats list-unstyled text-center">
                                    <li ><i  class="ti-key text-success"></i> THỎA THUẬN</li>
                                    <li ><i  class="ti-user text-success"></i> THỎA THUẬN</li>
                                    <li ><i  class="ti-desktop text-success"></i> KHÔNG CẦN VPS</li>
                                    <li ><i  class="ti-cloud text-success"></i> KHÔNG CẦN TREO MÁY</li>
                                    <li ><i  class="ti-alarm-clock text-success"></i> LIVE 24/7 VIDEO 4K</li>
                                    <li ><i  class="ti-headphone-alt text-success"></i> ĐƯỢC CHĂM SÓC RIÊNG </li>
                                    <li ><i  class="ti-money text-success"></i> ƯU ĐÃI KHÔNG GIỚI HẠN</li>
                                </ul>
                                <div class="text-center">
                                    <a target="_blank" href="https://www.facebook.com/messages/t/100002470941874" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">LIÊN HỆ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>-->


@endsection

@section('script')
<script type="text/javascript">

</script>
@endsection