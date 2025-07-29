@extends('layouts.admin')

@section('page-title', 'Chi tiết đánh giá')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Chi tiết đánh giá #{{ $review->id }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Thông tin khách hàng</h6>
                <p><strong>Tên:</strong> {{ $review->user->name }}</p>
                <p><strong>Email:</strong> {{ $review->user->email }}</p>
                <p><strong>Ngày đánh giá:</strong> {{ $review->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="col-md-6">
                <h6>Thông tin sản phẩm</h6>
                <p><strong>Tên sản phẩm:</strong> {{ $review->product->name }}</p>
                <p><strong>Giá:</strong> {{ number_format($review->product->price) }} VND</p>
                @if($review->product->image)
                    <img src="{{ asset('storage/' . $review->product->image) }}" style="max-width:150px; max-height:150px;" class="img-thumbnail">
                @endif
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-12">
                <h6>Nội dung đánh giá</h6>
                <div class="mb-3">
                    <strong>Đánh giá:</strong>
                    <div class="mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="ms-2">({{ $review->rating }}/5)</span>
                    </div>
                </div>
                
                @if($review->comment)
                    <div class="mb-3">
                        <strong>Bình luận:</strong>
                        <p class="mt-2">{{ $review->comment }}</p>
                    </div>
                @else
                    <p class="text-muted">Không có bình luận</p>
                @endif
            </div>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                    <i class="fas fa-trash me-1"></i> Xóa đánh giá
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 