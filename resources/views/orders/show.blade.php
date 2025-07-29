@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-shopping-bag me-2"></i>Chi tiết đơn hàng #{{ $order->id }}
                </h2>
                <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Chi tiết sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            @foreach($order->items as $item)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" style="width:80px; height:80px; object-fit:cover;" class="me-3">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width:80px; height:80px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                                    <p class="text-muted mb-1">{{ Str::limit($item->product->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Số lượng: {{ $item->quantity }}</span>
                                        <span class="text-primary fw-bold">{{ number_format($item->price) }} VND</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong>{{ number_format($item->price * $item->quantity) }} VND</strong>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Trạng thái:</strong>
                                <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'completed' ? 'success' : 'secondary') }} ms-2">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Ngày đặt:</strong><br>
                                <span class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            @if($order->shipping_address)
                            <div class="mb-3">
                                <strong>Địa chỉ giao hàng:</strong><br>
                                <span class="text-muted">{{ $order->shipping_address }}</span>
                            </div>
                            @endif
                            
                            @if($order->phone)
                            <div class="mb-3">
                                <strong>Số điện thoại:</strong><br>
                                <span class="text-muted">{{ $order->phone }}</span>
                            </div>
                            @endif
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Tổng tiền:</h5>
                                <h5 class="mb-0 text-primary">{{ number_format($order->total) }} VND</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 