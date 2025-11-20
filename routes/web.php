<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShippingController;

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

// AI Chat Page (public)
Route::get('/ai/chat', [AIController::class, 'chat'])->name('ai.chat');
Route::post('/ai/chat/gemini', [AIController::class, 'chatWithGemini'])->name('ai.chat.gemini');
Route::post('/ai/chat/standard', [AIController::class, 'chatStandard'])->name('ai.chat.standard');
Route::get('/ai/chat/history', [AIController::class, 'getChatHistoryApi'])->name('ai.chat.history');
Route::delete('/ai/chat/history', [AIController::class, 'clearChatHistory'])->name('ai.chat.clear');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public routes (không cần đăng nhập)
Route::get('/shop', [ProductController::class, 'index'])->name('shop');

// Trang chi tiết sản phẩm (public)
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

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
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.apply-voucher');
    Route::post('/cart/remove-voucher', [CartController::class, 'removeVoucher'])->name('cart.remove-voucher');

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
        Route::post('/admin/products/bulk-activate', [AdminProductController::class, 'bulkActivate'])
            ->name('admin.products.bulk-activate');
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

        // Quản lý voucher
        Route::resource('/admin/vouchers', AdminVoucherController::class)->names([
            'index' => 'admin.vouchers.index',
            'create' => 'admin.vouchers.create',
            'store' => 'admin.vouchers.store',
            'show' => 'admin.vouchers.show',
            'edit' => 'admin.vouchers.edit',
            'update' => 'admin.vouchers.update',
            'destroy' => 'admin.vouchers.destroy',
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
        Route::get('/product-analysis', [AIController::class, 'getProductAnalysis'])->name('ai.product-analysis');
        Route::get('/personalized', [AIController::class, 'getPersonalizedRecommendations'])->name('ai.personalized');
    });
});

// API công khai: tính phí vận chuyển
Route::post('/shipping/calculate', [ShippingController::class, 'calculateShipping'])->name('shipping.calculate');

// AI Routes công khai (không cần đăng nhập)
Route::prefix('ai')->group(function () {
    Route::get('/recommendations', [AIController::class, 'getRecommendations'])->name('ai.recommendations');
    Route::get('/stock-check', [AIController::class, 'checkStock'])->name('ai.stock-check');
    Route::get('/skin-analysis', [AIController::class, 'getSkinAnalysis'])->name('ai.skin-analysis');
    Route::get('/trending', [AIController::class, 'getTrendingProducts'])->name('ai.trending');
});

require __DIR__.'/auth.php';
