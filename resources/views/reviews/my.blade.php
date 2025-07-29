@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-star me-2"></i>Đánh giá của tôi
            </h2>
            
            @if($reviews->count() > 0)
                <div class="row">
                    @foreach($reviews as $review)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    @if($review->product->image)
                                        <img src="{{ asset('storage/' . $review->product->image) }}" style="width:80px; height:80px; object-fit:cover;" class="me-3">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width:80px; height:80px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $review->product->name }}</h6>
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="ms-2 text-muted">({{ $review->rating }}/5)</span>
                                        </div>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                
                                @if($review->comment)
                                    <div class="mb-3">
                                        <strong>Bình luận:</strong>
                                        <p class="mb-0 mt-1">{{ $review->comment }}</p>
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('product.show', $review->product->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Xem sản phẩm
                                    </a>
                                    <small class="text-muted">{{ number_format($review->product->price) }} VND</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($reviews->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reviews->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h4>Bạn chưa có đánh giá nào</h4>
                    <p class="text-muted">Hãy mua sắm và đánh giá sản phẩm!</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 