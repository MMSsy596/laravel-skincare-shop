@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="text-danger mb-3">Thanh toán thất bại!</h2>
                    <p class="text-muted mb-4">
                        Rất tiếc, có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại hoặc liên hệ với chúng tôi để được hỗ trợ.
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
                                    <strong>Trạng thái:</strong><br>
                                    <span class="text-danger">{{ $order->payment_status_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        @if($order->payment_method === 'bank_transfer')
                            <a href="{{ route('payment.bank-transfer', $order->id) }}" class="btn btn-primary">
                                <i class="fas fa-university me-2"></i>Thử lại chuyển khoản
                            </a>
                        @elseif($order->payment_method === 'qr_code')
                            <a href="{{ route('payment.qr-code', $order->id) }}" class="btn btn-warning">
                                <i class="fas fa-qrcode me-2"></i>Thử lại QR Code
                            </a>
                        @endif
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-2"></i>Xem chi tiết đơn hàng
                        </a>
                        <a href="{{ route('orders.history') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Xem tất cả đơn hàng
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-headset me-2"></i>Cần hỗ trợ?
                        </h6>
                        <p class="mb-0">
                            Nếu bạn gặp vấn đề với thanh toán, vui lòng liên hệ với chúng tôi qua:
                            <br><strong>Hotline:</strong> 1900-xxxx
                            <br><strong>Email:</strong> support@beauty-ai-shop.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 