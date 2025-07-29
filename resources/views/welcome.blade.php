@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero-section" data-aos="fade-up">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-4">
                    Khám phá vẻ đẹp với 
                    <span class="text-warning">AI</span>
                </h1>
                <p class="lead mb-4">
                    BeautyAI - Nơi công nghệ trí tuệ nhân tạo gặp gỡ vẻ đẹp tự nhiên. 
                    Tư vấn chuyên nghiệp, sản phẩm chất lượng cao, trải nghiệm mua sắm đẳng cấp.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('shop') }}" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-shopping-bag me-2"></i>Mua sắm ngay
                    </a>
                    <a href="#ai-consultation" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-robot me-2"></i>Tư vấn AI
                    </a>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center">
                    <img src="https://images.unsplash.com/photo-1556228720-195a672e8a03?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="Beauty Products" class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- AI Consultation Section -->
<section id="ai-consultation" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold mb-3">
                    <i class="fas fa-robot text-primary me-3"></i>
                    Tư vấn AI Chuyên nghiệp
                </h2>
                <p class="lead text-muted">
                    Công nghệ AI tiên tiến giúp bạn tìm ra sản phẩm phù hợp nhất với làn da
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-brain fa-2x"></i>
                        </div>
                        <h5 class="card-title">Phân tích làn da</h5>
                        <p class="card-text text-muted">
                            AI phân tích tình trạng da và đưa ra gợi ý sản phẩm phù hợp
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="bg-success bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-magic fa-2x"></i>
                        </div>
                        <h5 class="card-title">Gợi ý cá nhân hóa</h5>
                        <p class="card-text text-muted">
                            Đề xuất sản phẩm dựa trên sở thích và nhu cầu riêng biệt
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="bg-warning bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h5 class="card-title">Tư vấn 24/7</h5>
                        <p class="card-text text-muted">
                            Hỗ trợ tư vấn mọi lúc, mọi nơi với chatbot thông minh
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section id="categories" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold mb-3">
                    <i class="fas fa-tags text-primary me-3"></i>
                    Danh mục sản phẩm
                </h2>
                <p class="lead text-muted">
                    Khám phá bộ sưu tập mỹ phẩm đa dạng, chất lượng cao
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="card category-card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-spa fa-lg"></i>
                        </div>
                        <h6 class="card-title">Chăm sóc da</h6>
                        <p class="card-text text-muted small">Serum, kem dưỡng, mặt nạ</p>
                        <a href="{{ route('shop') }}?category=skincare" class="btn btn-outline-primary btn-sm">Xem thêm</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="card category-card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-danger bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-palette fa-lg"></i>
                        </div>
                        <h6 class="card-title">Trang điểm</h6>
                        <p class="card-text text-muted small">Kem nền, son môi, phấn mắt</p>
                        <a href="{{ route('shop') }}?category=makeup" class="btn btn-outline-danger btn-sm">Xem thêm</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="card category-card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-spray-can fa-lg"></i>
                        </div>
                        <h6 class="card-title">Nước hoa</h6>
                        <p class="card-text text-muted small">Nước hoa nam, nữ, unisex</p>
                        <a href="{{ route('shop') }}?category=perfume" class="btn btn-outline-success btn-sm">Xem thêm</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6" data-aos="zoom-in" data-aos-delay="400">
                <div class="card category-card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-cut fa-lg"></i>
                        </div>
                        <h6 class="card-title">Chăm sóc tóc</h6>
                        <p class="card-text text-muted small">Dầu gội, dầu xả, serum</p>
                        <a href="{{ route('shop') }}?category=haircare" class="btn btn-outline-warning btn-sm">Xem thêm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold mb-3">
                    <i class="fas fa-star text-warning me-3"></i>
                    Sản phẩm nổi bật
                </h2>
                <p class="lead text-muted">
                    Những sản phẩm được yêu thích nhất với đánh giá cao từ khách hàng
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            @php
                $featuredProducts = \App\Models\Product::with('reviews')->get()->take(4);
            @endphp
            
            @forelse($featuredProducts as $product)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="card product-card h-100 border-0 shadow-sm">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 250px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column p-4">
                        <div class="category-badge">Mỹ phẩm</div>
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 80) }}</p>
                        
                        <!-- Rating -->
                        <div class="mb-3">
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted ms-2">({{ $product->reviews_count }})</small>
                            </div>
                        </div>
                        
                        <div class="mt-auto">
                            <div class="price-tag mb-3">
                                {{ number_format($product->price) }} VND
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-eye me-2"></i>Xem chi tiết
                                </a>
                                
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4>Chưa có sản phẩm nào</h4>
                <p class="text-muted">Hãy quay lại sau để xem các sản phẩm mới</p>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('shop') }}" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-shopping-bag me-2"></i>Xem tất cả sản phẩm
            </a>
        </div>
    </div>
</section>

<!-- AI Recommendation Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="display-5 fw-bold mb-4">
                    <i class="fas fa-robot text-primary me-3"></i>
                    Gợi ý AI Thông minh
                </h2>
                <p class="lead mb-4">
                    Hệ thống AI của chúng tôi phân tích sở thích và nhu cầu của bạn để đưa ra những gợi ý sản phẩm phù hợp nhất.
                </p>
                
                <div class="ai-recommendation mb-4">
                    <h6><i class="fas fa-lightbulb me-2"></i>Gợi ý cho bạn:</h6>
                    <p class="mb-0">Dựa trên lịch sử mua sắm, chúng tôi gợi ý bạn thử kem dưỡng ẩm chuyên sâu cho làn da khô.</p>
                </div>
                
                <div class="d-flex gap-3">
                    <a href="#ai-consultation" class="btn btn-primary btn-lg">
                        <i class="fas fa-robot me-2"></i>Tư vấn AI
                    </a>
                    <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Khám phá
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1571781926291-c477ebfd024b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                         alt="AI Beauty Consultation" class="img-fluid rounded-3 shadow-lg">
                    
                    <!-- Floating AI Elements -->
                    <div class="position-absolute top-0 start-0 bg-primary text-white p-3 rounded-3 shadow" style="transform: translate(-20px, -20px);">
                        <i class="fas fa-brain fa-2x"></i>
                    </div>
                    <div class="position-absolute bottom-0 end-0 bg-success text-white p-3 rounded-3 shadow" style="transform: translate(20px, 20px);">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold mb-3">
                    <i class="fas fa-heart text-danger me-3"></i>
                    Khách hàng nói gì
                </h2>
                <p class="lead text-muted">
                    Những đánh giá chân thực từ khách hàng đã sử dụng sản phẩm của chúng tôi
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="rating-stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Sản phẩm chất lượng tuyệt vời! AI tư vấn rất chính xác và hữu ích. Tôi đã tìm được sản phẩm phù hợp với làn da của mình."</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Nguyễn Thị Anh</h6>
                                <small class="text-muted">Khách hàng thân thiết</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="rating-stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Giao diện website đẹp, dễ sử dụng. Chatbot AI tư vấn rất thông minh và hữu ích. Sẽ tiếp tục mua sắm tại đây!"</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Trần Văn Bình</h6>
                                <small class="text-muted">Khách hàng mới</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="rating-stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Dịch vụ giao hàng nhanh, đóng gói cẩn thận. Sản phẩm chính hãng, giá cả hợp lý. Rất hài lòng với trải nghiệm mua sắm!"</p>
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Lê Thị Cẩm</h6>
                                <small class="text-muted">Khách hàng VIP</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5" style="background: var(--gradient-secondary); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="display-5 fw-bold mb-3">
                    <i class="fas fa-envelope me-3"></i>
                    Đăng ký nhận tin
                </h2>
                <p class="lead mb-0">
                    Nhận thông tin về sản phẩm mới, khuyến mãi đặc biệt và tư vấn chăm sóc da từ chuyên gia.
                </p>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="input-group input-group-lg">
                    <input type="email" class="form-control" placeholder="Email của bạn">
                    <button class="btn btn-light" type="button">
                        <i class="fas fa-paper-plane me-2"></i>Đăng ký
                    </button>
                </div>
                <small class="text-light opacity-75">Chúng tôi cam kết không spam email của bạn</small>
            </div>
        </div>
    </div>
</section>
@endsection
