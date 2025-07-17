<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

return redirect()->intended(RouteServiceProvider::redirectUser());

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (session()->has('cart')) {
            foreach (session('cart') as $productId => $item) {
                $cartItem = Cart::firstOrNew([
                    'user_id' => auth()->id(),
                    'product_id' => $productId,
                ]);
                $cartItem->quantity += $item['quantity'];
                $cartItem->save();
            }
            session()->forget('cart');
        }

        return redirect()->intended(RouteServiceProvider::redirectUser());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Chuyển hướng về trang login hoặc shop
        return redirect()->route('login'); // hoặc route('shop')
    }
}
