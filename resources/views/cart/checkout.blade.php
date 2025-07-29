@extends('layouts.app')

@section('content')
<style>
.payment-method-card {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
    height: 100%;
}

.payment-method-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.1);
}

.payment-method-card .form-check-input:checked + .form-check-label {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.payment-method-card .form-check-input:checked + .form-check-label .payment-method-content {
    color: #007bff;
}

.payment-method-content {
    text-align: center;
    transition: all 0.3s ease;
}

.payment-method-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.payment-method-content small {
    font-size: 0.8rem;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}
</style>

<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="mb-0">
                <i class="fas fa-credit-card me-2 text-primary"></i>Thanh toán đơn hàng
            </h2>
            <p class="text-muted">Vui lòng kiểm tra thông tin đơn hàng và điền thông tin giao hàng</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cart.checkout.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Địa chỉ giao hàng
                            </label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" 
                                      placeholder="Nhập địa chỉ giao hàng chi tiết..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Số điện thoại
                            </label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   placeholder="Nhập số điện thoại liên hệ..." required>
                        </div>
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-credit-card me-1"></i>Phương thức thanh toán
                            </label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_cash" value="cash" checked>
                                        <label class="form-check-label" for="payment_cash">
                                            <div class="payment-method-content">
                                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                                <h6>Tiền mặt</h6>
                                                <small class="text-muted">Thanh toán khi nhận hàng</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_bank" value="bank_transfer">
                                        <label class="form-check-label" for="payment_bank">
                                            <div class="payment-method-content">
                                                <i class="fas fa-university fa-2x text-primary mb-2"></i>
                                                <h6>Chuyển khoản</h6>
                                                <small class="text-muted">Ngân hàng Vietcombank</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_qr" value="qr_code">
                                        <label class="form-check-label" for="payment_qr">
                                            <div class="payment-method-content">
                                                <i class="fas fa-qrcode fa-2x text-warning mb-2"></i>
                                                <h6>Quét mã QR</h6>
                                                <small class="text-muted">Thanh toán nhanh</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Lưu ý quan trọng
                            </h6>
                            <ul class="mb-0">
                                <li>Đơn hàng sẽ được xử lý trong vòng 24 giờ</li>
                                <li>Thời gian giao hàng: 2-5 ngày làm việc</li>
                                <li>Thanh toán khi nhận hàng (COD)</li>
                                <li>Kiểm tra hàng trước khi thanh toán</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check-circle me-2"></i>Xác nhận đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của bạn
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        @php $total = 0; $hasStockIssues = false; @endphp
                        @foreach($cart as $id => $item)
                            @php 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                                $stock = $item['stock'] ?? 0;
                                $isOutOfStock = $stock < $item['quantity'];
                                if ($isOutOfStock) $hasStockIssues = true;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-start {{ $isOutOfStock ? 'border-warning' : '' }}">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <img src="{{ $item['image'] ?? 'https://via.placeholder.com/50x50/CCCCCC/FFFFFF?text=Product' }}" 
                                         style="width:50px; height:50px; object-fit:cover;" 
                                         class="rounded me-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $item['name'] }}</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Số lượng: {{ $item['quantity'] }}</span>
                                            <span class="text-primary fw-bold">{{ number_format($item['price']) }} VNĐ</span>
                                        </div>
                                        @if($isOutOfStock)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Chỉ còn {{ $stock }} sản phẩm
                                                </span>
                                            </div>
                                        @elseif($stock <= 5)
                                            <div class="mt-1">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Còn {{ $stock }} sản phẩm
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong>{{ number_format($subtotal) }} VNĐ</strong>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    
                    @if($hasStockIssues)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Cảnh báo về kho hàng
                            </h6>
                            <p class="mb-0">
                                Một số sản phẩm vượt quá số lượng có sẵn. 
                                Vui lòng quay lại giỏ hàng để điều chỉnh.
                            </p>
                        </div>
                    @endif
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($total) }} VNĐ</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Phí vận chuyển:</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Tổng cộng:</strong>
                                <strong class="text-primary fs-5">{{ number_format($total) }} VNĐ</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Bảo mật & Hỗ trợ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <i class="fas fa-lock fa-2x text-success mb-2"></i>
                            <p class="small mb-0">Thanh toán an toàn</p>
                        </div>
                        <div class="col-6">
                            <i class="fas fa-headset fa-2x text-primary mb-2"></i>
                            <p class="small mb-0">Hỗ trợ 24/7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 