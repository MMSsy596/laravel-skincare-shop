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
    
}
