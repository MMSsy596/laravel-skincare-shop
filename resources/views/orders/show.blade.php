@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-shopping-bag me-2"></i>Chi tiết đơn hàng #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </h2>
                <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-box me-2"></i>Chi tiết sản phẩm
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($order->items as $item)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                @if($item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" style="width:80px; height:80px; object-fit:cover;" class="me-3 rounded">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center me-3 rounded" style="width:80px; height:80px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                                    <p class="text-muted mb-1">{{ Str::limit($item->product->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Số lượng: {{ $item->quantity }}</span>
                                        <span class="text-primary fw-bold">{{ number_format($item->price) }} VNĐ</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong>{{ number_format($item->price * $item->quantity) }} VNĐ</strong>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Trạng thái đơn hàng:</strong>
                                <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'delivered' ? 'success' : 'secondary') }} ms-2">
                                    {{ $order->order_status_name }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Phương thức thanh toán:</strong><br>
                                <span class="badge bg-info me-2">
                                    <i class="{{ $order->payment_method_icon }} me-1"></i>
                                    {{ $order->payment_method_name }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Trạng thái thanh toán:</strong><br>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }} ms-2">
                                    {{ $order->payment_status_name }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Ngày đặt:</strong><br>
                                <span class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            @if($order->paid_at)
                            <div class="mb-3">
                                <strong>Ngày thanh toán:</strong><br>
                                <span class="text-muted">{{ $order->paid_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                            
                            @if($order->transaction_id)
                            <div class="mb-3">
                                <strong>Mã giao dịch:</strong><br>
                                <span class="text-muted">{{ $order->transaction_id }}</span>
                            </div>
                            @endif
                            
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
                            
                            @if($order->payment_notes)
                            <div class="mb-3">
                                <strong>Ghi chú thanh toán:</strong><br>
                                <span class="text-muted">{{ $order->payment_notes }}</span>
                            </div>
                            @endif
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Tổng tiền:</h5>
                                <h5 class="mb-0 text-primary">{{ $order->formatted_total }}</h5>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Actions -->
                    @if($order->payment_status === 'pending' && $order->payment_method !== 'cash')
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Thanh toán
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($order->payment_method === 'bank_transfer')
                                <a href="{{ route('payment.bank-transfer', $order->id) }}" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-university me-2"></i>Thanh toán chuyển khoản
                                </a>
                            @elseif($order->payment_method === 'qr_code')
                                <a href="{{ route('payment.qr-code', $order->id) }}" class="btn btn-warning w-100 mb-2">
                                    <i class="fas fa-qrcode me-2"></i>Thanh toán QR Code
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Order Actions -->
                    @if($order->status === 'pending')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-times-circle me-2"></i>Hủy đơn hàng
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('orders.update', $order->id) }}" 
                                  onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Hủy đơn hàng
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 