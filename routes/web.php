<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\Auth\GoogleController;

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public routes (không cần đăng nhập)
Route::get('/shop', function () {
    $query = \App\Models\Product::query();
    
    // Tìm kiếm
    if (request('search')) {
        $query->where('name', 'like', '%' . request('search') . '%')
              ->orWhere('description', 'like', '%' . request('search') . '%');
    }
    
    // Sắp xếp
    switch (request('sort')) {
        case 'price_asc':
            $query->orderBy('price', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('price', 'desc');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        default:
            $query->latest();
    }
    
    $products = $query->paginate(12);
    return view('shop', compact('products'));
})->name('shop');

// Trang chi tiết sản phẩm (public)
Route::get('/product/{id}', function ($id) {
    $product = \App\Models\Product::findOrFail($id);
    
    // Lấy sản phẩm tương tự
    $similarProducts = \App\Models\Product::where('category', $product->category)
        ->where('id', '!=', $product->id)
        ->where('is_active', true)
        ->limit(4)
        ->get();
    
    return view('products.show', compact('product', 'similarProducts'));
})->name('product.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/checkout', [CartController::class, 'checkoutForm'])->name('cart.checkout')->middleware('check.stock');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout.submit')->middleware('check.stock');

    Route::middleware('is_admin')->group(function () {
        Route::resource('/admin/products', AdminProductController::class)->names([
            'index' => 'admin.products.index',
            'create' => 'admin.products.create',
            'store' => 'admin.products.store',
            'show' => 'admin.products.show',
            'edit' => 'admin.products.edit',
            'update' => 'admin.products.update',
            'destroy' => 'admin.products.destroy',
        ]);
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        
        // Quản lý đánh giá
        Route::resource('/admin/reviews', AdminReviewController::class)->names([
            'index' => 'admin.reviews.index',
            'show' => 'admin.reviews.show',
            'destroy' => 'admin.reviews.destroy',
        ]);

        // Quản lý đơn hàng
        Route::resource('/admin/orders', AdminOrderController::class)->names([
            'index' => 'admin.orders.index',
            'show' => 'admin.orders.show',
            'update' => 'admin.orders.update',
        ]);
    });

    // Đánh giá sản phẩm
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/my', [ReviewController::class, 'my'])->name('reviews.my');
    
    // Lịch sử đơn hàng
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');

    // Payment Routes
    Route::prefix('payment')->group(function () {
        Route::get('/bank-transfer/{orderId}', [PaymentController::class, 'bankTransfer'])->name('payment.bank-transfer');
        Route::get('/qr-code/{orderId}', [PaymentController::class, 'qrCode'])->name('payment.qr-code');
        Route::post('/confirm/{orderId}', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');
        Route::get('/success/{orderId}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
        Route::get('/failed/{orderId}', [PaymentController::class, 'paymentFailed'])->name('payment.failed');
    });

    // AI Routes (cần đăng nhập)
    Route::prefix('ai')->group(function () {
        Route::get('/recommendations', [AIController::class, 'getRecommendations'])->name('ai.recommendations');
        Route::get('/stock-check', [AIController::class, 'checkStock'])->name('ai.stock-check');
        Route::get('/skin-analysis', [AIController::class, 'getSkinAnalysis'])->name('ai.skin-analysis');
        Route::get('/product-analysis', [AIController::class, 'getProductAnalysis'])->name('ai.product-analysis');
        Route::get('/trending', [AIController::class, 'getTrendingProducts'])->name('ai.trending');
        Route::get('/personalized', [AIController::class, 'getPersonalizedRecommendations'])->name('ai.personalized');
    });
});

require __DIR__.'/auth.php';
