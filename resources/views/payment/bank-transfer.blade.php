@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-university me-2"></i>Thanh toán chuyển khoản
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Thông tin đơn hàng</h5>
                            <div class="mb-3">
                                <strong>Mã đơn hàng:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="mb-3">
                                <strong>Tổng tiền:</strong> 
                                <span class="text-primary fw-bold fs-5">{{ number_format($order->total) }} VNĐ</span>
                            </div>
                            <div class="mb-3">
                                <strong>Địa chỉ giao hàng:</strong><br>
                                {{ $order->shipping_address }}
                            </div>
                            <div class="mb-3">
                                <strong>Số điện thoại:</strong> {{ $order->phone }}
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">Thông tin tài khoản ngân hàng</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Ngân hàng:</strong> {{ $bankInfo['bank_name'] }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Số tài khoản:</strong> 
                                        <span class="text-primary fw-bold">{{ $bankInfo['account_number'] }}</span>
                                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $bankInfo['account_number'] }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Chủ tài khoản:</strong> {{ $bankInfo['account_name'] }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Chi nhánh:</strong> {{ $bankInfo['branch'] }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>Nội dung chuyển khoản:</strong><br>
                                        <span class="text-danger fw-bold">{{ $bankInfo['transfer_content'] }}</span>
                                        <button class="btn btn-sm btn-outline-danger ms-2" onclick="copyToClipboard('{{ $bankInfo['transfer_content'] }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Hướng dẫn thanh toán
                        </h6>
                        <ol class="mb-0">
                            <li>Chuyển khoản chính xác số tiền: <strong>{{ number_format($order->total) }} VNĐ</strong></li>
                            <li>Ghi nội dung chuyển khoản: <strong>{{ $bankInfo['transfer_content'] }}</strong></li>
                            <li>Sau khi chuyển khoản, vui lòng xác nhận bên dưới</li>
                            <li>Chúng tôi sẽ kiểm tra và xử lý đơn hàng trong vòng 30 phút</li>
                        </ol>
                    </div>
                    
                    <form action="{{ route('payment.confirm', $order->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="transaction_id" class="form-label">
                                        <i class="fas fa-receipt me-1"></i>Mã giao dịch
                                    </label>
                                    <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                                           placeholder="Nhập mã giao dịch từ ngân hàng..." required>
                                    <small class="text-muted">Mã giao dịch từ SMS hoặc email xác nhận</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_notes" class="form-label">
                                        <i class="fas fa-sticky-note me-1"></i>Ghi chú
                                    </label>
                                    <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3" 
                                              placeholder="Ghi chú thêm (nếu có)..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Xác nhận thanh toán
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Hiển thị thông báo copy thành công
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.remove('btn-outline-primary', 'btn-outline-danger');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            if (originalHTML.includes('btn-outline-primary')) {
                button.classList.add('btn-outline-primary');
            } else {
                button.classList.add('btn-outline-danger');
            }
        }, 1000);
    });
}
</script>
@endsection 