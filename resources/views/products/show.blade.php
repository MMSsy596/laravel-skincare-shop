@extends('layouts.app')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop') }}">Shop</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop', ['category' => $product->category]) }}">{{ $product->category_name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <div class="main-image mb-3">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                         class="img-fluid rounded-3 shadow-sm" id="mainImage">
                </div>
                <div class="thumbnail-images d-flex gap-2">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                         class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                         onclick="changeMainImage(this.src)">
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="product-info">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-primary me-2">{{ $product->category_name }}</span>
                    @if($product->is_featured)
                        <span class="badge bg-warning">Nổi bật</span>
                    @endif
                </div>

                <h1 class="h2 fw-bold mb-2">{{ $product->name }}</h1>
                <p class="text-muted mb-3">{{ $product->brand }}</p>

                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="stars me-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <span class="text-muted">({{ $product->reviews_count }} đánh giá)</span>
                </div>

                <!-- Price -->
                <div class="price-section mb-4">
                    <h3 class="text-primary fw-bold mb-0">{{ number_format($product->price) }} VNĐ</h3>
                    @if($product->stock <= 10 && $product->stock > 0)
                        <small class="text-danger">Chỉ còn {{ $product->stock }} sản phẩm!</small>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="stock-status mb-4">
                    @if($product->stock > 0)
                        <div class="d-flex align-items-center text-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>Còn hàng ({{ $product->stock }} sản phẩm)</span>
                        </div>
                    @else
                        <div class="d-flex align-items-center text-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <span>Hết hàng</span>
                        </div>
                    @endif
                </div>

                <!-- Add to Cart -->
                @if($product->stock > 0)
                    @auth
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="row g-3">
                                <div class="col-auto">
                                    <label for="quantity" class="form-label">Số lượng:</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                           value="1" min="1" max="{{ $product->stock }}" style="width: 80px;">
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <strong>Đăng nhập để mua hàng</strong><br>
                                    <small>Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('login') }}" class="btn btn-primary me-2">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus me-2"></i>Đăng ký
                                </a>
                            </div>
                        </div>
                    @endauth
                @else
                    <button class="btn btn-secondary btn-lg w-100 mb-4" disabled>
                        <i class="fas fa-times me-2"></i>Hết hàng
                    </button>
                @endif

                <!-- Quick Actions -->
                <div class="quick-actions d-flex gap-2 mb-4">
                    <button class="btn btn-outline-primary flex-fill" onclick="addToWishlist({{ $product->id }})">
                        <i class="fas fa-heart me-2"></i>Yêu thích
                    </button>
                    <button class="btn btn-outline-secondary flex-fill" onclick="shareProduct()">
                        <i class="fas fa-share me-2"></i>Chia sẻ
                    </button>
                </div>

                <!-- Product Description -->
                <div class="description mb-4">
                    <h5 class="fw-bold mb-3">Mô tả sản phẩm</h5>
                    <p class="text-muted">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Analysis Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Phân tích AI
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-check-circle me-2"></i>Phù hợp với
                            </h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-star text-warning me-2"></i>{{ $product->skin_type_name }}</li>
                                <li><i class="fas fa-users text-info me-2"></i>{{ $product->age_group_name }}</li>
                                @if($product->ingredients)
                                    <li><i class="fas fa-flask text-success me-2"></i>Thành phần an toàn</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-lightbulb me-2"></i>Lợi ích chính
                            </h6>
                            <ul class="list-unstyled">
                                @if(strpos(strtolower($product->ingredients), 'hyaluronic acid') !== false)
                                    <li><i class="fas fa-tint text-primary me-2"></i>Dưỡng ẩm sâu</li>
                                @endif
                                @if(strpos(strtolower($product->ingredients), 'vitamin c') !== false)
                                    <li><i class="fas fa-sun text-warning me-2"></i>Làm sáng da</li>
                                @endif
                                @if(strpos(strtolower($product->ingredients), 'retinol') !== false)
                                    <li><i class="fas fa-clock text-info me-2"></i>Chống lão hóa</li>
                                @endif
                                @if(strpos(strtolower($product->ingredients), 'salicylic acid') !== false)
                                    <li><i class="fas fa-bug text-success me-2"></i>Trị mụn</li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    @if($product->ingredients)
                        <div class="mt-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-flask me-2"></i>Thành phần chính
                            </h6>
                            <div class="ingredients-tags">
                                @foreach(explode(',', $product->ingredients) as $ingredient)
                                    <span class="badge bg-light text-dark me-2 mb-2">{{ trim($ingredient) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($product->usage_instructions)
                        <div class="mt-4">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Hướng dẫn sử dụng
                            </h6>
                            <p class="text-muted">{{ $product->usage_instructions }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Product Specifications -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Thông số kỹ thuật
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Thương hiệu:</strong><br>
                            <span class="text-muted">{{ $product->brand ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Mã SKU:</strong><br>
                            <span class="text-muted">{{ $product->sku ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Trọng lượng:</strong><br>
                            <span class="text-muted">{{ $product->weight ?: 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Hạn sử dụng:</strong><br>
                            <span class="text-muted">{{ $product->shelf_life ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Đánh giá khách hàng
                    </h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="fas fa-plus me-2"></i>Viết đánh giá
                    </button>
                </div>
                <div class="card-body">
                    @if($product->reviews->count() > 0)
                        @foreach($product->reviews as $review)
                            <div class="review-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $review->user->name }}</h6>
                                        <div class="stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-4">Chưa có đánh giá nào cho sản phẩm này.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Products -->
    @if($similarProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="fw-bold mb-4">
                    <i class="fas fa-thumbs-up me-2"></i>Sản phẩm tương tự
                </h4>
                <div class="row">
                    @foreach($similarProducts as $similarProduct)
                        <div class="col-md-3 mb-4">
                            <div class="card product-card h-100 border-0 shadow-sm">
                                <img src="{{ $similarProduct->image_url }}" class="card-img-top" alt="{{ $similarProduct->name }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $similarProduct->name }}</h6>
                                    <p class="text-primary fw-bold">{{ number_format($similarProduct->price) }} VNĐ</p>
                                    <a href="{{ route('product.show', $similarProduct->id) }}" class="btn btn-outline-primary btn-sm">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Viết đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Đánh giá:</label>
                        <div class="rating-input">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="d-none">
                                <label for="star{{ $i }}" class="star-label">
                                    <i class="fas fa-star"></i>
                                </label>
                            @endfor
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Nhận xét:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
}

function addToWishlist(productId) {
    // Implement wishlist functionality
    showToast('Đã thêm vào danh sách yêu thích!', 'success');
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $product->name }}',
            text: '{{ $product->description }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        showToast('Đã sao chép link!', 'success');
    }
}

// Rating stars interaction
document.querySelectorAll('.star-label').forEach((label, index) => {
    label.addEventListener('click', () => {
        // Remove active class from all stars
        document.querySelectorAll('.star-label').forEach(l => l.classList.remove('active'));
        
        // Add active class to clicked star and all previous stars
        for (let i = 0; i <= index; i++) {
            document.querySelectorAll('.star-label')[i].classList.add('active');
        }
    });
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.appendChild(toast);
    document.body.appendChild(container);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(container);
    });
}
</script>

@endsection 