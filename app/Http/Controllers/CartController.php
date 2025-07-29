<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
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
        
        return view('cart.checkout', compact('cart'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cash,bank_transfer,qr_code',
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
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
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
            
            // KHÔNG xóa giỏ hàng ngay - chỉ xóa khi thanh toán thành công
            // Giỏ hàng sẽ được xóa trong PaymentController khi xác nhận thanh toán
            
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
}
