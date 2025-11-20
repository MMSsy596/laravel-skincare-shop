<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function bankTransfer($orderId)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($orderId);
        
        if ($order->payment_method !== 'bank_transfer') {
            return redirect()->route('orders.show', $order->id)->with('error', 'Phương thức thanh toán không hợp lệ.');
        }
        
        // Thông tin ngân hàng từ cấu hình
        $paymentConfig = config('services.payment');
        $bankInfo = [
            'bank_name' => $paymentConfig['bank_name'] ?? 'Vietcombank',
            'account_number' => $paymentConfig['account_number'] ?? '',
            'account_name' => $paymentConfig['account_name'] ?? '',
            'branch' => $paymentConfig['branch'] ?? '',
            'transfer_content' => 'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
        ];
        
        return view('payment.bank-transfer', compact('order', 'bankInfo'));
    }
    
    public function qrCode($orderId)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($orderId);
        
        if ($order->payment_method !== 'qr_code') {
            return redirect()->route('orders.show', $order->id)->with('error', 'Phương thức thanh toán không hợp lệ.');
        }
        
        // Tạo QR code data
        $paymentConfig = config('services.payment');
        $qrData = [
            'bank_name' => $paymentConfig['bank_name'] ?? 'Vietcombank',
            'account_number' => $paymentConfig['account_number'] ?? '',
            'account_name' => $paymentConfig['account_name'] ?? '',
            'amount' => $order->total,
            'content' => 'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
        ];
        
        // Tạo QR code string theo format VietQR (MB Bank cũng dùng chuẩn này)
        $qrString = $this->generateVietcombankQRString($qrData);
        
        return view('payment.qr-code', compact('order', 'qrData', 'qrString'));
    }
    
    public function confirmPayment(Request $request, $orderId)
    {
        $request->validate([
            'transaction_id' => 'required|string|max:255',
            'payment_notes' => 'nullable|string',
        ]);
        
        $order = Order::where('user_id', auth()->id())->findOrFail($orderId);
        
        try {
            DB::beginTransaction();
            
            $order->markAsPaid(
                $request->transaction_id,
                $request->payment_notes
            );
            
            // Xóa giỏ hàng sau khi thanh toán thành công
            $this->clearCart();
            
            DB::commit();
            
            return redirect()->route('payment.success', $order->id)
                ->with('success', 'Xác nhận thanh toán thành công! Chúng tôi sẽ kiểm tra và xử lý đơn hàng của bạn.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function paymentSuccess($orderId)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($orderId);
        
        return view('payment.success', compact('order'));
    }
    
    public function paymentFailed($orderId)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($orderId);
        
        return view('payment.failed', compact('order'));
    }
    
    /**
     * Tạo QR code string theo format Vietcombank
     */
    private function generateVietcombankQRString($data)
    {
        // Format QR code theo chuẩn Vietcombank
        $qrString = "00020101021238";
        $qrString .= "0010A000000727";
        $qrString .= "01270000A000000727012201";
        $qrString .= "011001234567890";
        $qrString .= "0208QRIBFTTA";
        $qrString .= "5303704";
        $qrString .= "5406" . number_format($data['amount'], 0, '', '');
        $qrString .= "5802VN";
        $qrString .= "6208" . $data['content'];
        $qrString .= "6304";
        
        // Tính CRC16
        $crc = $this->calculateCRC16($qrString);
        $qrString .= strtoupper(dechex($crc));
        
        return $qrString;
    }
    
    /**
     * Tính CRC16 cho QR code
     */
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
    
    /**
     * Xóa giỏ hàng sau khi thanh toán thành công
     */
    private function clearCart()
    {
        if (auth()->check()) {
            // Xóa giỏ hàng cho user đã đăng nhập
            \App\Models\Cart::where('user_id', auth()->id())->delete();
        } else {
            // Xóa session cart
            session()->forget('cart');
        }
    }
    
    /**
     * Kiểm tra xem giỏ hàng có chứa sản phẩm từ đơn hàng không
     */
    private function cartContainsOrderItems($order)
    {
        if (auth()->check()) {
            $cartItems = \App\Models\Cart::where('user_id', auth()->id())->get();
            foreach ($cartItems as $cartItem) {
                foreach ($order->items as $orderItem) {
                    if ($cartItem->product_id === $orderItem->product_id) {
                        return true;
                    }
                }
            }
        } else {
            $cart = session()->get('cart', []);
            foreach ($cart as $productId => $cartItem) {
                foreach ($order->items as $orderItem) {
                    if ($productId == $orderItem->product_id) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}