@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-6">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                    <i class="fas fa-image fa-5x text-muted"></i>
                </div>
            @endif
        </div>
        
        <!-- Thông tin sản phẩm -->
        <div class="col-md-6">
            <h1 class="mb-3">{{ $product->name }}</h1>
            
            <!-- Rating -->
            <div class="mb-3">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
                <span class="ms-2">{{ number_format($product->average_rating, 1) }}/5</span>
                <span class="text-muted">({{ $product->reviews_count }} đánh giá)</span>
            </div>
            
            <p class="text-muted mb-3">{{ $product->description }}</p>
            
            <h3 class="text-primary fw-bold mb-4">{{ number_format($product->price) }} VND</h3>
            
            <!-- Thêm vào giỏ hàng -->
            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                </button>
            </form>
        </div>
    </div>
    
    <!-- Đánh giá sản phẩm -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Đánh giá sản phẩm</h3>
            
            @auth
                <!-- Form đánh giá -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Viết đánh giá của bạn</h5>
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label">Đánh giá:</label>
                                <div class="rating-input">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="d-none">
                                        <label for="star{{ $i }}" class="fas fa-star fa-2x text-muted" style="cursor: pointer;"></label>
                                    @endfor
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="comment" class="form-label">Bình luận:</label>
                                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá sản phẩm.
                </div>
            @endauth
            
            <!-- Danh sách đánh giá -->
            <div class="reviews-list">
                @forelse($product->reviews()->with('user')->latest()->get() as $review)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $review->user->name }}</h6>
                                    <div class="mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        <span class="ms-2 text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($review->comment)
                                <p class="mb-0">{{ $review->comment }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #ffc107 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-input input');
    const starLabels = document.querySelectorAll('.rating-input label');
    
    stars.forEach((star, index) => {
        star.addEventListener('change', function() {
            starLabels.forEach((label, labelIndex) => {
                if (labelIndex < 5 - index) {
                    label.classList.remove('text-muted');
                    label.classList.add('text-warning');
                } else {
                    label.classList.remove('text-warning');
                    label.classList.add('text-muted');
                }
            });
        });
    });
});
</script>
@endsection 