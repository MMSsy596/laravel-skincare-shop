@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-history me-2"></i>Lịch sử đơn hàng
            </h2>
            
            @if($orders->count() > 0)
                <div class="row">
                    @foreach($orders as $order)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Đơn hàng #{{ $order->id }}</h6>
                                <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'completed' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Ngày đặt:</small><br>
                                        <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted">Tổng tiền:</small><br>
                                        <strong class="text-primary">{{ number_format($order->total) }} VND</strong>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Sản phẩm:</small>
                                    <ul class="list-unstyled mt-2">
                                        @foreach($order->items->take(3) as $item)
                                        <li class="d-flex align-items-center mb-1">
                                            @if($item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" style="width:30px; height:30px; object-fit:cover;" class="me-2">
                                            @endif
                                            <span>{{ $item->product->name }} x{{ $item->quantity }}</span>
                                        </li>
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <li class="text-muted">... và {{ $order->items->count() - 3 }} sản phẩm khác</li>
                                        @endif
                                    </ul>
                                </div>
                                
                                @if($order->status == 'cancelled')
                                    <div class="alert alert-danger mt-2 mb-0">
                                        Đơn hàng đã bị hủy bởi 
                                        @if($order->canceller_id == $order->user_id)
                                            <strong>bạn</strong>
                                        @elseif($order->canceller)
                                            <strong>admin</strong> ({{ $order->canceller->name }})
                                        @else
                                            <strong>hệ thống</strong>
                                        @endif
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Xem chi tiết
                                    </a>
                                    @if($order->status == 'pending')
                                        <form action="{{ route('orders.show', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Hủy đơn</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($orders->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h4>Bạn chưa có đơn hàng nào</h4>
                    <p class="text-muted">Hãy mua sắm để có đơn hàng đầu tiên!</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 