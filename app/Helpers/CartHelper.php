<?php

namespace App\Helpers;

use App\Models\Cart;

class CartHelper
{
    public static function getCartCount()
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->sum('quantity');
        } else {
            $cart = session()->get('cart', []);
            return array_sum(array_column($cart, 'quantity'));
        }
    }
} 