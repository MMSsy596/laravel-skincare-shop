# Hướng dẫn sử dụng QR Code Thanh toán - BeautyAI Shop

## 📱 QR Code Thanh toán là gì?

QR Code thanh toán là một mã vạch hai chiều chứa thông tin thanh toán, cho phép khách hàng thanh toán nhanh chóng bằng cách quét mã từ ứng dụng ngân hàng.

## 🏦 Hỗ trợ ngân hàng

Hiện tại hệ thống hỗ trợ QR Code thanh toán cho:
- **Vietcombank** (chính thức)
- Các ngân hàng khác (sẽ được thêm sau)

## 📋 Thông tin QR Code

### Format QR Code Vietcombank
```
00020101021238
0010A000000727
01270000A000000727012201
011001234567890
0208QRIBFTTA
5303704
5406[AMOUNT]
5802VN
6208[CONTENT]
6304[CRC16]
```

### Thông tin chứa trong QR Code
- **Ngân hàng**: Vietcombank
- **Số tài khoản**: 1234567890
- **Chủ tài khoản**: BEAUTY AI SHOP
- **Số tiền**: Tự động tính theo đơn hàng
- **Nội dung**: DH + Mã đơn hàng (VD: DH000123)

## 📱 Cách sử dụng QR Code

### Bước 1: Mở ứng dụng ngân hàng
- Mở ứng dụng Vietcombank Mobile
- Hoặc ứng dụng ngân hàng khác hỗ trợ QR Code

### Bước 2: Chọn tính năng quét QR
- Tìm tính năng "Quét mã QR" hoặc "Scan QR"
- Thường nằm ở menu chính hoặc tab thanh toán

### Bước 3: Quét mã QR
- Đưa camera vào mã QR trên màn hình
- Hoặc chụp ảnh mã QR
- Ứng dụng sẽ tự động nhận diện

### Bước 4: Kiểm tra thông tin
- Kiểm tra số tiền có đúng không
- Kiểm tra nội dung chuyển khoản
- Kiểm tra thông tin người nhận

### Bước 5: Xác nhận thanh toán
- Nhập mật khẩu hoặc xác thực sinh trắc học
- Xác nhận giao dịch

### Bước 6: Lưu mã giao dịch
- Lưu lại mã giao dịch từ SMS/Email
- Quay lại website để xác nhận

## 🎯 Tính năng QR Code

### ✅ Đã hoàn thành
- [x] Tạo QR Code theo chuẩn Vietcombank
- [x] Hiển thị QR Code trong giao diện (JavaScript)
- [x] Tải QR Code về máy
- [x] In QR Code
- [x] Thông tin chi tiết đơn hàng
- [x] Xác nhận thanh toán
- [x] Không cần PHP extension

### 🔄 Đang phát triển
- [ ] Hỗ trợ nhiều ngân hàng
- [ ] QR Code động (thay đổi theo thời gian)
- [ ] Tích hợp webhook xác nhận tự động
- [ ] QR Code cho từng sản phẩm

## 🛠️ Kỹ thuật

### Thư viện sử dụng
```html
<!-- JavaScript QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
```

### Cấu hình QR Code JavaScript
```javascript
QRCode.toCanvas(element, qrString, {
    width: 300,
    margin: 2,
    color: {
        dark: '#000000',
        light: '#FFFFFF'
    },
    errorCorrectionLevel: 'H'
});
```

### CRC16 Calculation (PHP)
```php
private function calculateCRC16($data)
{
    $crc = 0xFFFF;
    $length = strlen($data);
    
    for ($i = 0; $i < $length; $i++) {
        $crc ^= ord($data[$i]) << 8;
        for ($j = 0; $j < 8; $j++) {
            if ($crc & 0x8000) {
                $crc = ($crc << 1) ^ 0x1021;
            } else {
                $crc = $crc << 1;
            }
        }
    }
    
    return $crc & 0xFFFF;
}
```

## 🔒 Bảo mật

### Tính năng bảo mật
- QR Code chỉ chứa thông tin cần thiết
- Không lưu thông tin nhạy cảm
- Mã giao dịch được mã hóa
- Xác thực người dùng trước khi tạo QR
- Sử dụng JavaScript client-side để tạo QR (không cần server processing)

### Lưu ý bảo mật
- Không chia sẻ QR Code với người khác
- Kiểm tra thông tin trước khi thanh toán
- Sử dụng ứng dụng ngân hàng chính thức
- Báo cáo ngay nếu có giao dịch lạ

## 📞 Hỗ trợ

### Liên hệ hỗ trợ
- **Hotline**: 1900-xxxx
- **Email**: support@beauty-ai-shop.com
- **Zalo**: BeautyAI Shop

### FAQ
**Q: QR Code không quét được?**
A: Kiểm tra độ sáng màn hình, đảm bảo mã QR rõ nét

**Q: Thông tin hiển thị sai?**
A: Liên hệ ngay với chúng tôi để được hỗ trợ

**Q: Thanh toán thành công nhưng chưa cập nhật?**
A: Vui lòng xác nhận thanh toán với mã giao dịch

**Q: Tại sao không cần cài đặt PHP extension?**
A: Chúng tôi sử dụng JavaScript library để tạo QR Code, giúp giảm tải cho server và dễ dàng triển khai

---

**Lưu ý**: QR Code thanh toán chỉ có hiệu lực trong thời gian nhất định. Vui lòng thanh toán ngay sau khi nhận được mã QR. 