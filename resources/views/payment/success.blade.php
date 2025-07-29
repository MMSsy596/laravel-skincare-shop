@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="text-success mb-3">Thanh toán thành công!</h2>
                    <p class="text-muted mb-4">
                        Cảm ơn bạn đã thanh toán. Đơn hàng của bạn đã được xác nhận và sẽ được xử lý trong thời gian sớm nhất.
                    </p>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title">Thông tin đơn hàng</h6>
                            <div class="row text-start">
                                <div class="col-6">
                                    <strong>Mã đơn hàng:</strong><br>
                                    <span class="text-primary">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Tổng tiền:</strong><br>
                                    <span class="text-primary">{{ $order->formatted_total }}</span>
                                </div>
                            </div>
                            <div class="row text-start mt-2">
                                <div class="col-6">
                                    <strong>Phương thức:</strong><br>
                                    <span>{{ $order->payment_method_name }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Ngày thanh toán:</strong><br>
                                    <span>{{ $order->paid_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Xem chi tiết đơn hàng
                        </a>
                        <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Xem tất cả đơn hàng
                        </a>
                        <a href="{{ route('shop') }}" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 