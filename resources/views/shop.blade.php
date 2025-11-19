@extends('layouts.app')

@section('content')
<!-- Hero Banner -->
<section class="py-5" style="background: var(--gradient-primary); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-shopping-bag me-3"></i>
                    Bộ sưu tập mỹ phẩm
                </h1>
                <p class="lead mb-4">
                    Khám phá hàng nghìn sản phẩm mỹ phẩm chất lượng cao với công nghệ AI tư vấn chuyên nghiệp
                </p>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <i class="fas fa-spa fa-5x opacity-75"></i>
            </div>
        </div>
    </div>
</section>

<!-- AI Recommendation Banner -->
@auth
<div class="container mt-4" data-aos="fade-up">
    <div class="ai-recommendation">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="mb-2">
                    <i class="fas fa-robot me-2"></i>
                    Gợi ý AI cho {{ Auth::user()->name }}
                </h6>
                <p class="mb-0">Dựa trên sở thích của bạn, chúng tôi gợi ý thử kem dưỡng ẩm chuyên sâu và serum Vitamin C.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="#ai-consultation" class="btn btn-light btn-sm">
                    <i class="fas fa-robot me-2"></i>Tư vấn AI
                </a>
            </div>
        </div>
    </div>
</div>
@endauth

<!-- Search and Filter Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row g-3">
            <!-- Search Bar -->
            <div class="col-lg-6">
                <form action="{{ route('shop') }}" method="GET" class="search-bar">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control search-input" 
                               placeholder="Tìm kiếm sản phẩm mỹ phẩm..." 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Sort Options -->
            <div class="col-lg-3">
                <select class="form-select" onchange="window.location.href=this.value">
                    <option value="{{ route('shop') }}">Sắp xếp theo</option>
                    <option value="{{ route('shop', ['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                        <i class="fas fa-sort-amount-up"></i> Giá tăng dần
                    </option>
                    <option value="{{ route('shop', ['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                        <i class="fas fa-sort-amount-down"></i> Giá giảm dần
                    </option>
                    <option value="{{ route('shop', ['sort' => 'name_asc']) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                        <i class="fas fa-sort-alpha-up"></i> Tên A-Z
                    </option>
                    <option value="{{ route('shop', ['sort' => 'name_desc']) }}" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                        <i class="fas fa-sort-alpha-down"></i> Tên Z-A
                    </option>
                    <option value="{{ route('shop', ['sort' => 'rating']) }}" {{ request('sort') == 'rating' ? 'selected' : '' }}>
                        <i class="fas fa-star"></i> Đánh giá cao nhất
                    </option>
                </select>
            </div>
            
            <!-- Category Filter -->
            <div class="col-lg-3">
                <select class="form-select" onchange="window.location.href=this.value">
                    <option value="{{ route('shop') }}">Tất cả danh mục</option>
                    <option value="{{ route('shop', ['category' => 'skincare']) }}" {{ request('category') == 'skincare' ? 'selected' : '' }}>
                        <i class="fas fa-spa"></i> Chăm sóc da
                    </option>
                    <option value="{{ route('shop', ['category' => 'makeup']) }}" {{ request('category') == 'makeup' ? 'selected' : '' }}>
                        <i class="fas fa-palette"></i> Trang điểm
                    </option>
                    <option value="{{ route('shop', ['category' => 'perfume']) }}" {{ request('category') == 'perfume' ? 'selected' : '' }}>
                        <i class="fas fa-spray-can"></i> Nước hoa
                    </option>
                    <option value="{{ route('shop', ['category' => 'haircare']) }}" {{ request('category') == 'haircare' ? 'selected' : '' }}>
                        <i class="fas fa-cut"></i> Chăm sóc tóc
                    </option>
                </select>
            </div>
        </div>
        
        <!-- Active Filters -->
        @if(request('search') || request('category') || request('sort'))
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                    <span class="text-muted">Bộ lọc:</span>
                    @if(request('search'))
                        <span class="badge bg-primary">
                            Tìm kiếm: "{{ request('search') }}"
                            <a href="{{ route('shop', array_merge(request()->except('search'), ['search' => ''])) }}" class="text-white text-decoration-none ms-1">×</a>
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="badge bg-success">
                            Danh mục: {{ ucfirst(request('category')) }}
                            <a href="{{ route('shop', array_merge(request()->except('category'), ['category' => ''])) }}" class="text-white text-decoration-none ms-1">×</a>
                        </span>
                    @endif
                    @if(request('sort'))
                        <span class="badge bg-warning text-dark">
                            Sắp xếp: {{ ucfirst(str_replace('_', ' ', request('sort'))) }}
                            <a href="{{ route('shop', array_merge(request()->except('sort'), ['sort' => ''])) }}" class="text-dark text-decoration-none ms-1">×</a>
                        </span>
                    @endif
                    <a href="{{ route('shop') }}" class="badge bg-secondary text-decoration-none">Xóa tất cả</a>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Products Grid -->
<section class="py-5">
    <div class="container">
        <!-- Results Count -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        {{ $products->total() }} sản phẩm
                        @if(request('search'))
                            cho "{{ request('search') }}"
                        @endif
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted">Hiển thị:</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" onclick="setViewMode('grid')">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="setViewMode('list')">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="row g-4" id="productsContainer">
            @forelse($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                <div class="card product-card h-100 border-0 shadow-sm">
                    <!-- Product Image -->
                    <div class="position-relative">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 class="card-img-top" alt="{{ $product->name }}" 
                                 style="height: 280px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 280px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <!-- Quick Actions Overlay -->
                        <div class="position-absolute top-0 end-0 p-2">
                            <button class="btn btn-light btn-sm rounded-circle shadow-sm" 
                                    onclick="addToWishlist({{ $product->id }})" 
                                    title="Thêm vào yêu thích">
                                <i class="fas fa-heart text-muted"></i>
                            </button>
                        </div>
                        
                        <!-- Category Badge -->
                        <div class="position-absolute top-0 start-0 p-2">
                            <span class="category-badge">Mỹ phẩm</span>
                        </div>
                        
                        <!-- Rating Badge -->
                        @if($product->average_rating > 0)
                        <div class="position-absolute bottom-0 start-0 p-2">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star me-1"></i>{{ number_format($product->average_rating, 1) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Product Info -->
                    <div class="card-body d-flex flex-column p-4">
                        <h6 class="card-title fw-bold mb-2">{{ $product->name }}</h6>
                        <p class="card-text text-muted small mb-3">{{ Str::limit($product->description, 80) }}</p>
                        
                        <!-- Rating Stars -->
                        <div class="mb-3">
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <small class="text-muted ms-2">({{ $product->reviews_count }})</small>
                            </div>
                        </div>
                        
                        <!-- Price -->
                        <div class="mt-auto">
                            <div class="price-tag mb-3">
                                {{ number_format($product->price) }} VND
                            </div>
                            
                            <!-- Action Buttons -->
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
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">Không tìm thấy sản phẩm nào</h4>
                    <p class="text-muted mb-4">
                        @if(request('search'))
                            Không có sản phẩm nào phù hợp với từ khóa "{{ request('search') }}"
                        @else
                            Hiện tại chưa có sản phẩm nào trong danh mục này
                        @endif
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('shop') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Về trang chủ
                        </a>
                        <a href="#ai-consultation" class="btn btn-outline-primary">
                            <i class="fas fa-robot me-2"></i>Tư vấn AI
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- AI Consultation CTA -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h3 class="fw-bold mb-3">
                    <i class="fas fa-robot text-primary me-3"></i>
                    Không tìm thấy sản phẩm phù hợp?
                </h3>
                <p class="lead mb-4">
                    Hãy để AI của chúng tôi tư vấn và gợi ý sản phẩm phù hợp nhất với nhu cầu của bạn.
                </p>
                <div class="d-flex gap-3">
                    <a href="#ai-consultation" class="btn btn-primary btn-lg">
                        <i class="fas fa-robot me-2"></i>Tư vấn AI ngay
                    </a>
                    <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-sync-alt me-2"></i>Xem tất cả sản phẩm
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center" data-aos="fade-left">
                <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                    <i class="fas fa-brain fa-4x"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// View mode toggle
function setViewMode(mode) {
    const container = document.getElementById('productsContainer');
    const buttons = document.querySelectorAll('.btn-group .btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    if (mode === 'list') {
        container.classList.add('list-view');
        container.querySelectorAll('.col-lg-3').forEach(col => {
            col.className = 'col-12 mb-3';
        });
    } else {
        container.classList.remove('list-view');
        container.querySelectorAll('.col-12').forEach(col => {
            col.className = 'col-lg-3 col-md-4 col-sm-6';
        });
    }
}

// Wishlist functionality
function addToWishlist(productId) {
    // Implement wishlist functionality
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    
    if (icon.classList.contains('text-muted')) {
        icon.classList.remove('text-muted');
        icon.classList.add('text-danger');
        button.classList.add('btn-danger');
        button.classList.remove('btn-light');
        showToast('Đã thêm vào danh sách yêu thích!', 'success');
    } else {
        icon.classList.remove('text-danger');
        icon.classList.add('text-muted');
        button.classList.remove('btn-danger');
        button.classList.add('btn-light');
        showToast('Đã xóa khỏi danh sách yêu thích!', 'info');
    }
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toast);
    });
}

// Smooth scroll to AI consultation
document.querySelectorAll('a[href="#ai-consultation"]').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const element = document.getElementById('ai-consultation');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
</script>

@endsection 