@extends('layouts.master')

@section('content')
<style>
     .card-box {
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        background-color: #fff;
        height: 100%;
        transition: transform 0.3s;
        position: relative;
        padding-top: 30px;
    }
    
    .card-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .category-badge {
        border-top-left-radius: 5px;
        position: absolute;
        top: 0;
        left: 0;
        padding: 5px 15px 5px 10px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        background-color: #dc3545;
        text-transform: uppercase;
        z-index: 1;
        clip-path: polygon(0 0, 100% 0, 85% 100%, 0 100%);
    }
    
    .guide-title {
        font-size: 28px;
    }
    
    .animated {
        animation-duration: 1s;
        animation-fill-mode: both;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -20px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    
    .fadeInDown {
        animation-name: fadeInDown;
    }
</style>
 <h3 class="mb-4">Hướng dẫn sử dụng</h3>
    <div class="row animated fadeInDown">
        @foreach($guidesData as $guide)
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card-box">
                <span class="category-badge">{{ $guide['category'] }}</span>
                <a href="{{ $guide['url'] }}" target="_blank" class="text-decoration-none">
                    <div class="p-3" style="padding-top:20px">
                        <h4 class="font-weight-bold mb-3" style="height: 60px; overflow: hidden;">{{ $guide['title'] }}</h4>
                        <p class="text-muted" style="height: 95px; overflow: hidden;">{{ str_limit($guide['content'], 120) }}</p>
                        <div class="guide-meta small text-muted">
                            <i class="fa fa-calendar"></i> <span>{{ $guide['date'] }}</span>
                            <br>
                            <i class="fa fa-user"></i> <span class="font-weight-bold">{{ $guide['author'] }}</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>

@endsection

@section('script')

@endsection