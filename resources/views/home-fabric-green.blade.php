@extends('layouts.app-fabric')

@section('title', 'Best Online Store - ' . ($globalCompany->company_name ?? 'Your Store'))
@section('meta_description', 'Get Quality Products Online. Discover premium quality products at ' . ($globalCompany->company_name ?? 'Your Store') . '.')

@section('content')

<!-- Hero Banner Section -->
@if($banners->count() > 0)
<section style="background: #f8f9fa; padding: 20px 0;">
    <div class="container">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            @if($banners->count() > 1)
            <div class="carousel-indicators">
                @foreach($banners as $banner)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $loop->index }}" 
                        class="{{ $loop->first ? 'active' : '' }}" aria-label="Slide {{ $loop->iteration }}"></button>
                @endforeach
            </div>
            @endif
            <div class="carousel-inner" style="border-radius: 12px; overflow: hidden;">
                @foreach($banners as $banner)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    @if($banner->image)
                        <img src="{{ $banner->image_url }}" 
                             class="d-block w-100" 
                             alt="{{ $banner->alt_text ?: $banner->title }}" 
                             style="height: 400px; object-fit: cover;">
                    @else
                        <div style="height: 400px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); display: flex; align-items: center; justify-content: center;">
                            <div class="text-center text-white">
                                <h2>{{ $banner->title ?? 'Welcome to Our Store' }}</h2>
                                <p>{{ $banner->description ?? 'Discover amazing products' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
            @if($banners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Promotional Badges Section -->
<section style="padding: 30px 0; background: white;">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🎯</div>
                    <h4 style="color: #155724; margin-bottom: 5px;">Save</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #0b3d0b;">₹29</div>
                    <p style="font-size: 0.9rem; color: #0b3d0b; margin-top: 10px;">Enjoy Discount all types of Products</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">✨</div>
                    <h4 style="color: #0c5460; margin-bottom: 5px;">Discount</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #062c33;">30%</div>
                    <p style="font-size: 0.9rem; color: #062c33; margin-top: 10px;">Enjoy Discount all types of Products</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🏆</div>
                    <h4 style="color: #1565c0; margin-bottom: 5px;">Up to</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #0d47a1;">50%</div>
                    <p style="font-size: 0.9rem; color: #0d47a1; margin-top: 10px;">Enjoy Discount all types of Products</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 25px; border-radius: 12px; text-align: center; height: 100%;">
                    <div style="font-size: 2.5rem; margin-bottom: 10px;">🚚</div>
                    <h4 style="color: #155724; margin-bottom: 5px;">Free</h4>
                    <div style="font-size: 2rem; font-weight: 700; color: #0b3d0b;">SHIP</div>
                    <p style="font-size: 0.9rem; color: #0b3d0b; margin-top: 10px;">Enjoy Free Shipping on Orders</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rest of the content remains the same but with green theme -->
@endsection
