@extends('layouts.app')

@section('title', 'QR Code Thanh toán - Đơn hàng #' . str_pad($order->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        QR Code Thanh toán
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Thông tin đơn hàng</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Mã đơn hàng:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div class="mb-2">
                                        <strong>Tổng tiền:</strong> {{ number_format($order->total) }} VNĐ
                                    </div>
                                    <div class="mb-2">
                                        <strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}
                                    </div>
                                    <div class="mb-2">
                                        <strong>Số điện thoại:</strong> {{ $order->phone }}
                                    </div>
                                    <div class="mb-2">
                                        <strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h6 class="text-warning">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Hướng dẫn thanh toán
                                </h6>
                                <ol class="small">
                                    <li>Mở ứng dụng ngân hàng (Vietcombank Mobile)</li>
                                    <li>Chọn tính năng "Quét mã QR"</li>
                                    <li>Quét mã QR bên cạnh</li>
                                    <li>Kiểm tra thông tin và xác nhận thanh toán</li>
                                    <li>Lưu mã giao dịch và xác nhận bên dưới</li>
                                </ol>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">Mã QR thanh toán</h5>
                            <div class="text-center">
                                <!-- QR Code được tạo bằng JavaScript -->
                                <div class="qr-code-container mb-3">
                                    <div id="qrcode" class="border rounded shadow-sm p-3" style="display: inline-block;"></div>
                                </div>
                                
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Ngân hàng:</strong> {{ $qrData['bank_name'] }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Số tài khoản:</strong> {{ $qrData['account_number'] }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Chủ tài khoản:</strong> {{ $qrData['account_name'] }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Số tiền:</strong> {{ number_format($qrData['amount']) }} VNĐ
                                        </div>
                                        <div class="mb-2">
                                            <strong>Nội dung:</strong> {{ $qrData['content'] }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button class="btn btn-outline-primary btn-sm" onclick="downloadQRCode()">
                                        <i class="fas fa-download me-1"></i>Tải QR Code
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="printQRCode()">
                                        <i class="fas fa-print me-1"></i>In QR Code
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" type="button" onclick="getLocation()">
                                        <i class="fas fa-map-marker-alt me-1"></i>Lấy vị trí hiện tại
                                    </button>
                                    <span id="location-status" class="text-success small ms-2"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-check-circle me-1"></i>
                                Xác nhận thanh toán
                            </h5>
                            <form action="{{ route('payment.confirm', $order->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="transaction_id" class="form-label">Mã giao dịch <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                                                   placeholder="Nhập mã giao dịch từ SMS/Email" required>
                                            <div class="form-text">Mã giao dịch nhận được sau khi thanh toán thành công</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_notes" class="form-label">Ghi chú</label>
                                            <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3" 
                                                      placeholder="Ghi chú thêm (nếu có)"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check me-2"></i>
                                        Xác nhận thanh toán
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Code JavaScript Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>
.qr-code-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

#qrcode {
    background: white;
    padding: 20px;
}

#qrcode img {
    display: block;
    margin: 0 auto;
}

@media print {
    .no-print {
        display: none !important;
    }
    #qrcode {
        padding: 10px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var qrString = '{{ $qrString }}';
    var qrcodeElement = document.getElementById('qrcode');
    qrcodeElement.innerHTML = '';
    new QRCode(qrcodeElement, {
        text: qrString,
        width: 300,
        height: 300,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
});

function downloadQRCode() {
    var qrImg = document.querySelector('#qrcode img');
    if (qrImg) {
        var link = document.createElement('a');
        link.download = 'qr-code-thanh-toan.png';
        link.href = qrImg.src;
        link.click();
    }
}

function printQRCode() {
    var qrImg = document.querySelector('#qrcode img');
    var orderInfo = {
        orderId: '{{ str_pad($order->id, 6, "0", STR_PAD_LEFT) }}',
        amount: '{{ number_format($order->total) }} VNĐ',
        content: '{{ $qrData["content"] }}'
    };
    if (qrImg) {
        var qrImageData = qrImg.src;
        var printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>QR Code Thanh Toán</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                    .qr-container { margin: 20px 0; }
                    .info { margin: 10px 0; font-size: 14px; }
                    .title { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <div class="title">QR Code Thanh Toán</div>
                <div class="qr-container">
                    <img src="${qrImageData}" style="max-width: 300px;">
                </div>
                <div class="info">
                    <strong>Mã đơn hàng:</strong> #${orderInfo.orderId}<br>
                    <strong>Số tiền:</strong> ${orderInfo.amount}<br>
                    <strong>Nội dung:</strong> ${orderInfo.content}
                </div>
                <div class="info">
                    <small>Quét mã QR bằng ứng dụng ngân hàng để thanh toán</small>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

function getLocation() {
    var status = document.getElementById('location-status');
    status.textContent = '';
    if (navigator.geolocation) {
        status.textContent = 'Đang lấy vị trí...';
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var noteField = document.getElementById('payment_notes');
            var text = `Vị trí hiện tại: https://maps.google.com/?q=${lat},${lng}`;
            if (noteField.value) {
                noteField.value += '\n' + text;
            } else {
                noteField.value = text;
            }
            status.textContent = 'Đã lấy vị trí!';
        }, function(error) {
            status.textContent = 'Không thể lấy vị trí!';
        });
    } else {
        status.textContent = 'Trình duyệt không hỗ trợ định vị!';
    }
}
</script>
@endsection 