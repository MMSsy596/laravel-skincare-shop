<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart; // Added Cart model import
use Illuminate\Support\Facades\Schema; // Added Schema import
use Illuminate\Database\Schema\Blueprint; // Added Blueprint import

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
                    'quantity' => $item->quantity
                ];
            }
        } else {
            $cart = session()->get('cart', []);
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

    public function add($id)
    {
        if (auth()->check()) {
            $cartItem = Cart::firstOrNew([
                'user_id' => auth()->id(),
                'product_id' => $id,
            ]);
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            // session logic như cũ
            $product = Product::findOrFail($id);
            $cart = session()->get('cart', []);

            $cart[$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => ($cart[$id]['quantity'] ?? 0) + 1
            ];
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index');
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
        if (auth()->check()) {
            $cartItem = \App\Models\Cart::where('user_id', auth()->id())->where('product_id', $id)->first();
            if ($cartItem) {
                $cartItem->quantity = max(1, (int)$request->input('quantity', 1));
                $cartItem->save();
            }
        } else {
            $cart = session()->get('cart', []);
            if(isset($cart[$id])) {
                $cart[$id]['quantity'] = max(1, (int)$request->input('quantity', 1));
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
                    'image' => $item->product->image
                ];
            }
        } else {
            $cart = session()->get('cart', []);
        }
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }
        return view('cart.checkout', compact('cart'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);
        if (auth()->check()) {
            $cartItems = \App\Models\Cart::with('product')->where('user_id', auth()->id())->get();
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->image
                ];
            }
        } else {
            $cart = session()->get('cart', []);
        }
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }
        // Tính tổng tiền
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        // Tạo đơn hàng
        $order = \App\Models\Order::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'phone' => $request->phone,
        ]);
        // Tạo chi tiết đơn hàng
        foreach ($cart as $productId => $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }
        // Xóa giỏ hàng
        if (auth()->check()) {
            \App\Models\Cart::where('user_id', auth()->id())->delete();
        } else {
            session()->forget('cart');
        }
        return redirect()->route('orders.history')->with('success', 'Đặt hàng thành công!');
    }
}
