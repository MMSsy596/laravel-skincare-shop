<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;

class CheckStockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra stock cho giỏ hàng của user đã đăng nhập
        if (auth()->check()) {
            $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();
            $stockErrors = [];
            
            foreach ($cartItems as $item) {
                $product = $item->product;
                if ($product && !$product->hasEnoughStock($item->quantity)) {
                    $stockErrors[] = "Sản phẩm {$product->name} chỉ còn {$product->stock} sản phẩm trong kho (bạn đã chọn {$item->quantity})";
                }
            }
            
            if (!empty($stockErrors)) {
                return redirect()->route('cart.index')->with('error', implode('<br>', $stockErrors));
            }
        }
        
        // Kiểm tra stock cho session cart
        if (!auth()->check()) {
            $cart = session()->get('cart', []);
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
        }
        
        return $next($request);
    }
}
