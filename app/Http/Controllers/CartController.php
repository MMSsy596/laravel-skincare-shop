<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CartController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $cartItems = \App\Models\Cart::with('product')->where('user_id', auth()->id())->get();
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'stock' => $item->product->stock,
                    'image' => $item->product->image
                ];
            }
        } else {
            $cart = session()->get('cart', []);
            // Cập nhật thông tin stock cho session cart
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $cart[$productId]['stock'] = $product->stock;
                    $cart[$productId]['image'] = $product->image;
                }
            }
        }
        return view('cart.index', compact('cart'));
    }

    public function getCartCount()
    {
        if (auth()->check()) {
            return \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
        } else {
            $cart = session()->get('cart', []);
            return array_sum(array_column($cart, 'quantity'));
        }
    }

    public function add(Request $request, $id)
    {
        $quantity = $request->input('quantity', 1);
        $product = Product::findOrFail($id);
        
        // Kiểm tra stock trước khi thêm vào giỏ hàng
        if (!$product->hasEnoughStock($quantity)) {
            return redirect()->back()->with('error', "Sản phẩm {$product->name} chỉ còn {$product->stock} sản phẩm trong kho!");
        }
        
        if (auth()->check()) {
            $cartItem = Cart::firstOrNew([
                'user_id' => auth()->id(),
                'product_id' => $id,
            ]);
            
            // Kiểm tra tổng số lượng trong giỏ hàng + số lượng muốn thêm
            $totalQuantity = $cartItem->quantity + $quantity;
            if (!$product->hasEnoughStock($totalQuantity)) {
                return redirect()->back()->with('error', "Sản phẩm {$product->name} chỉ còn {$product->stock} sản phẩm trong kho!");
            }
            
            $cartItem->quantity = $totalQuantity;
            $cartItem->save();
        } else {
            $cart = session()->get('cart', []);
            
            $currentQuantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
            $totalQuantity = $currentQuantity + $quantity;
            
            if (!$product->hasEnoughStock($totalQuantity)) {
                return redirect()->back()->with('error', "Sản phẩm {$product->name} chỉ còn {$product->stock} sản phẩm trong kho!");
            }

            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $totalQuantity;
            } else {
                $cart[$id] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'stock' => $product->stock,
                    'image' => $product->image
                ];
            }
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
    }

    public function remove($id)
    {
        if (auth()->check()) {
            \App\Models\Cart::where('user_id', auth()->id())->where('product_id', $id)->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index');
    }

    public function update(Request $request, $id)
    {
        $quantity = max(1, (int)$request->input('quantity', 1));
        $product = Product::findOrFail($id);
        
        // Kiểm tra stock trước khi cập nhật
        if (!$product->hasEnoughStock($quantity)) {
            return redirect()->back()->with('error', "Sản phẩm {$product->name} chỉ còn {$product->stock} sản phẩm trong kho!");
        }
        
        if (auth()->check()) {
            $cartItem = \App\Models\Cart::where('user_id', auth()->id())->where('product_id', $id)->first();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        } else {
            $cart = session()->get('cart', []);
            if(isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
                session()->put('cart', $cart);
            }
        }
        return redirect()->route('cart.index');
    }
    
    public function checkoutForm()
    {
        if (auth()->check()) {
            $cartItems = \App\Models\Cart::with('product')->where('user_id', auth()->id())->get();
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'stock' => $item->product->stock,
                    'image' => $item->product->image
                ];
            }
        } else {
            $cart = session()->get('cart', []);
            // Cập nhật thông tin stock cho session cart
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $cart[$productId]['stock'] = $product->stock;
                    $cart[$productId]['image'] = $product->image;
                }
            }
        }
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }
        
        // Kiểm tra stock trước khi cho phép thanh toán
        $stockErrors = [];
        $hasStockIssues = false;
        
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && !$product->hasEnoughStock($item['quantity'])) {
                $stockErrors[] = "Sản phẩm {$item['name']} chỉ còn {$product->stock} sản phẩm trong kho (bạn đã chọn {$item['quantity']})";
                $hasStockIssues = true;
            }
        }
        
        if ($hasStockIssues) {
            return redirect()->route('cart.index')->with('error', implode('<br>', $stockErrors));
        }
        
        // Tính tổng tiền tạm tính
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Lấy voucher từ session nếu có
        $voucherCode = session()->get('voucher_code');
        $voucher = null;
        $voucherDiscount = 0;
        
        if ($voucherCode) {
            $voucher = Voucher::where('code', $voucherCode)->first();
            if ($voucher && $voucher->isValid($subtotal)) {
                $voucherDiscount = $voucher->calculateDiscount($subtotal);
            }
        }
        
        // Tính phí vận chuyển (mặc định 30000 VNĐ, miễn phí nếu đơn hàng >= 500000)
        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        
        // Lấy danh sách voucher công khai (public) còn hiệu lực
        $publicVouchers = Voucher::where('is_public', true)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->where(function($query) use ($subtotal) {
                $query->whereNull('usage_limit')
                      ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where('min_order', '<=', $subtotal)
            ->orderBy('value', 'desc')
            ->get()
            ->filter(function($v) use ($subtotal) {
                return $v->isValid($subtotal);
            });
        
        return view('cart.checkout', compact('cart', 'subtotal', 'voucher', 'voucherDiscount', 'shippingFee', 'publicVouchers'));
    }
    
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0',
        ]);
        
        $voucher = Voucher::where('code', $request->code)->first();
        
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã voucher không tồn tại!',
                'discount' => 0
            ]);
        }
        
        if (!$voucher->isValid($request->order_total)) {
            $message = 'Mã voucher không hợp lệ!';
            if (!$voucher->is_active) {
                $message = 'Voucher đã bị vô hiệu hóa!';
            } elseif (now()->lt($voucher->valid_from)) {
                $message = 'Voucher chưa đến thời gian sử dụng!';
            } elseif (now()->gt($voucher->valid_until)) {
                $message = 'Voucher đã hết hạn!';
            } elseif ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
                $message = 'Voucher đã hết lượt sử dụng!';
            } elseif ($request->order_total < $voucher->min_order) {
                $message = 'Đơn hàng tối thiểu ' . number_format($voucher->min_order) . ' VNĐ để sử dụng voucher này!';
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'discount' => 0
            ]);
        }
        
        $discount = $voucher->calculateDiscount($request->order_total);
        
        return response()->json([
            'success' => true,
            'message' => 'Áp dụng voucher thành công! Giảm ' . number_format($discount) . ' VNĐ',
            'discount' => $discount
        ]);
    }
    
    public function removeVoucher()
    {
        session()->forget('voucher_code');
        session()->forget('voucher_id');
        
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa voucher!'
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cash,bank_transfer,qr_code',
            'voucher_code' => 'nullable|string',
            'voucher_discount' => 'nullable|numeric|min:0',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'distance' => 'nullable|numeric|min:0',
        ]);
        
        if (auth()->check()) {
            $cartItems = \App\Models\Cart::with('product')->where('user_id', auth()->id())->get();
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'stock' => $item->product->stock,
                    'image' => $item->product->image
                ];
            }
        } else {
            $cart = session()->get('cart', []);
            // Cập nhật thông tin stock cho session cart
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                if ($product) {
                    $cart[$productId]['stock'] = $product->stock;
                    $cart[$productId]['image'] = $product->image;
                }
            }
        }
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }
        
        // Kiểm tra stock một lần nữa trước khi tạo đơn hàng
        $stockErrors = [];
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && !$product->hasEnoughStock($item['quantity'])) {
                $stockErrors[] = "Sản phẩm {$item['name']} chỉ còn {$product->stock} sản phẩm trong kho (bạn đã chọn {$item['quantity']})";
            }
        }
        
        if (!empty($stockErrors)) {
            return redirect()->route('cart.index')->with('error', implode('<br>', $stockErrors));
        }
        
        // Sử dụng transaction để đảm bảo tính nhất quán
        try {
            DB::beginTransaction();
            
            // Tính tổng tiền
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            
            // Xử lý voucher từ form
            $voucherCode = $request->voucher_code;
            $voucherDiscount = $request->voucher_discount ?? 0;
            $voucher = null;
            
            if ($voucherCode && $voucherDiscount > 0) {
                $voucher = Voucher::where('code', $voucherCode)->first();
                if ($voucher && $voucher->isValid($subtotal)) {
                    // Validate lại discount để đảm bảo an toàn
                    $calculatedDiscount = $voucher->calculateDiscount($subtotal);
                    if (abs($calculatedDiscount - $voucherDiscount) > 0.01) {
                        // Nếu discount không khớp, tính lại
                        $voucherDiscount = $calculatedDiscount;
                    }
                    // Tăng số lần sử dụng voucher
                    $voucher->incrementUsage();
                } else {
                    // Voucher không hợp lệ, bỏ qua
                    $voucherCode = null;
                    $voucherDiscount = 0;
                }
            } else {
                $voucherCode = null;
                $voucherDiscount = 0;
            }
            
            // Tính phí vận chuyển dựa trên khoảng cách
            $orderTotalAfterDiscount = $subtotal - $voucherDiscount;
            $distance = $request->distance;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            
            if ($distance && $distance > 0) {
                // Tính phí ship: 3k/km, tối thiểu 10k
                $shippingFee = max(round($distance * 3000), 10000);
            } else {
                // Nếu không có khoảng cách, tính lại từ địa chỉ hoặc tọa độ
                if ($latitude && $longitude) {
                    // Tính khoảng cách từ tọa độ
                    $distance = $this->calculateDistance($latitude, $longitude, 21.0285, 105.8048);
                    $shippingFee = max(round($distance * 3000), 10000);
                } else {
                    // Sử dụng phí mặc định nếu không tính được
                    $shippingFee = 30000;
                    $distance = null;
                }
            }
            
            // Tính tổng cuối cùng
            $total = $orderTotalAfterDiscount + $shippingFee;
            if ($total < 0) {
                $total = 0;
            }
            
            // Xử lý thanh toán theo phương thức
            $paymentStatus = 'pending';
            $transactionId = null;
            $paymentNotes = null;
            
            switch ($request->payment_method) {
                case 'cash':
                    $paymentStatus = 'pending'; // Chờ thanh toán khi nhận hàng
                    $paymentNotes = 'Thanh toán tiền mặt khi nhận hàng (COD)';
                    break;
                    
                case 'bank_transfer':
                    $paymentStatus = 'pending';
                    $paymentNotes = 'Chuyển khoản ngân hàng - Chờ xác nhận';
                    break;
                    
                case 'qr_code':
                    $paymentStatus = 'pending';
                    $paymentNotes = 'Quét mã QR - Chờ xác nhận';
                    break;
            }
            
            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'pending',
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'transaction_id' => $transactionId,
                'payment_notes' => $paymentNotes,
                'voucher_code' => $voucherCode,
                'voucher_discount' => $voucherDiscount,
                'shipping_fee' => $shippingFee,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance' => $distance,
            ]);
            
            // Tạo chi tiết đơn hàng và giảm stock
            foreach ($cart as $productId => $item) {
                $product = Product::find($productId);
                
                // Kiểm tra stock một lần nữa trong transaction
                if (!$product->hasEnoughStock($item['quantity'])) {
                    throw new \Exception("Sản phẩm {$item['name']} không đủ số lượng trong kho!");
                }
                
                // Tạo order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                
                // Giảm stock sử dụng method mới
                if (!$product->reserveStock($item['quantity'])) {
                    throw new \Exception("Không thể giảm stock cho sản phẩm {$item['name']}!");
                }
            }
            
            // Xóa giỏ hàng sau khi tạo order thành công
            if (auth()->check()) {
                // Xóa giỏ hàng cho user đã đăng nhập
                \App\Models\Cart::where('user_id', auth()->id())->delete();
            } else {
                // Xóa session cart
                session()->forget('cart');
            }
            // Xóa voucher từ session
            session()->forget('voucher_code');
            session()->forget('voucher_id');
            
            DB::commit();
            
            // Redirect dựa trên phương thức thanh toán
            if ($request->payment_method === 'cash') {
                return redirect()->route('orders.show', $order->id)->with('success', 'Đặt hàng thành công! Vui lòng chuẩn bị tiền mặt khi nhận hàng.');
            } elseif ($request->payment_method === 'bank_transfer') {
                return redirect()->route('payment.bank-transfer', $order->id);
            } elseif ($request->payment_method === 'qr_code') {
                return redirect()->route('payment.qr-code', $order->id);
            }
            
            return redirect()->route('orders.history')->with('success', 'Đặt hàng thành công! Đơn hàng của bạn đã được xử lý.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cart.index')->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * Tính khoảng cách giữa 2 điểm (Haversine formula)
     * Trả về khoảng cách tính bằng km
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Bán kính Trái Đất tính bằng km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
