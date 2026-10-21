<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDashboardController;
use App\Http\Controllers\ProductDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicStoreController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreDirectoryController;
use App\Http\Controllers\OtpController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pencarian', [App\Http\Controllers\SearchController::class, 'index'])->name('search.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/dashboard/pesanan', [App\Http\Controllers\OrderDashboardController::class, 'allOrders'])->name('dashboard.order.all');
    Route::post('/push-subscribe', [App\Http\Controllers\PushSubscriptionController::class, 'subscribe'])->name('push.subscribe');

    
    // Rate limit: registrasi toko dibatasi 5x per 10 menit -> cegah spam bikin toko fiktif
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/toko/daftar', [StoreController::class, 'create'])->name('store.create');
        Route::post('/toko/daftar', [StoreController::class, 'store'])->name('store.store');
    });

    Route::get('/dashboard/preview/{store}', [StoreController::class, 'preview'])->name('store.preview');

    Route::get('/dashboard/toko/{store}/edit', [StoreController::class, 'edit'])->name('store.edit');
    Route::patch('/dashboard/toko/{store}', [StoreController::class, 'update'])->name('store.update');

    Route::get('/dashboard/toko/{store}/produk', [ProductDashboardController::class, 'index'])->name('dashboard.produk.index');
    Route::get('/dashboard/toko/{store}/produk/tambah', [ProductDashboardController::class, 'create'])->name('dashboard.produk.create');
    Route::post('/dashboard/toko/{store}/produk', [ProductDashboardController::class, 'store'])->name('dashboard.produk.store');
    Route::get('/dashboard/toko/{store}/produk/{product}/edit', [ProductDashboardController::class, 'edit'])->name('dashboard.produk.edit');
    Route::patch('/dashboard/toko/{store}/produk/{product}', [ProductDashboardController::class, 'update'])->name('dashboard.produk.update');
    Route::delete('/dashboard/toko/{store}/produk/{product}', [ProductDashboardController::class, 'destroy'])->name('dashboard.produk.destroy');

    // Rute Langganan
    Route::get('/dashboard/subscription', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('dashboard.subscription.index');
    Route::post('/dashboard/subscription/{subscription}/upload-proof', [App\Http\Controllers\SubscriptionController::class, 'uploadProof'])->name('dashboard.subscription.upload');

    Route::get('/dashboard/toko/{store}/order', [OrderDashboardController::class, 'index'])->name('dashboard.order.index');
    Route::patch('/dashboard/toko/{store}/order/{order}', [OrderDashboardController::class, 'updateStatus'])->name('dashboard.order.updateStatus');
});

require __DIR__.'/auth.php';

// ===================================================================
// RUTE LAPAKDA — ditambahkan manual di luar scaffold Breeze.
// ===================================================================

// Rate limit: cegah spam tambah item ke cart tanpa henti
Route::middleware('throttle:30,1')->post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/{store}', [CartController::class, 'show'])->name('cart.show');
Route::patch('/cart/item/{cartItem}', [CartController::class, 'updateQty'])->name('cart.update');
Route::delete('/cart/item/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout/{store}', [OrderController::class, 'checkout'])->name('checkout.show');
Route::get('/riwayat-pesanan', [OrderController::class, 'riwayat'])->name('order.riwayat');
Route::view('/kebijakan-privasi', 'kebijakan-privasi')->name('privacy.policy');

Route::view('/syarat-ketentuan', 'syarat-ketentuan')->name('terms');

Route::get('/jelajah/{kategoriSlug}', [StoreDirectoryController::class, 'index'])->name('jelajah');

// Rate limit: cegah spam order (bot checkout berkali-kali otomatis)
Route::middleware('throttle:10,1')->post('/checkout/{store}', [OrderController::class, 'store'])->name('checkout.store');

Route::get('/verifikasi-otp', [OtpController::class, 'show'])->name('otp.show');
Route::post('/verifikasi-otp', [OtpController::class, 'verify'])->name('otp.verify');
Route::middleware('throttle:1,1')->post('/verifikasi-otp/kirim-ulang', [OtpController::class, 'resend'])->name('otp.resend');

Route::post('/guest-register', [App\Http\Controllers\GuestAuthController::class, 'register'])->name('guest.register');

Route::post('/order/{order}/review', [App\Http\Controllers\ReviewController::class, 'store'])->name('review.store')->middleware('auth');

// ===================================================================
// RUTE ADMIN LAPAKDA (Khusus Subdomain)
// ===================================================================
Route::domain('admin.lapakda.com')->group(function () {
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Tagihan
        Route::get('/subscriptions', [App\Http\Controllers\AdminSubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscriptions/{subscription}/approve', [App\Http\Controllers\AdminSubscriptionController::class, 'approve'])->name('subscription.approve');
        
        // Pengaturan & Voucher
        Route::get('/settings', [App\Http\Controllers\AdminSettingController::class, 'index'])->name('setting.index');
        Route::post('/settings', [App\Http\Controllers\AdminSettingController::class, 'update'])->name('setting.update');
        
        Route::get('/vouchers', [App\Http\Controllers\AdminVoucherController::class, 'index'])->name('voucher.index');
        Route::post('/vouchers', [App\Http\Controllers\AdminVoucherController::class, 'store'])->name('voucher.store');
        Route::patch('/vouchers/{voucher}', [App\Http\Controllers\AdminVoucherController::class, 'update'])->name('voucher.update');
        Route::delete('/vouchers/{voucher}', [App\Http\Controllers\AdminVoucherController::class, 'destroy'])->name('voucher.destroy');
        
        // Manajemen Toko & User
        Route::get('/stores', [App\Http\Controllers\AdminStoreController::class, 'index'])->name('store.index');
        Route::patch('/stores/{store}/toggle', [App\Http\Controllers\AdminStoreController::class, 'toggleStatus'])->name('store.toggle');
        Route::get('/stores/export', [App\Http\Controllers\AdminStoreController::class, 'exportCsv'])->name('store.export');
        
        Route::get('/users', [App\Http\Controllers\AdminUserController::class, 'index'])->name('user.index');
        
        // Pencarian Admin
        Route::get('/search', [App\Http\Controllers\AdminSearchController::class, 'index'])->name('search');
    });
});

// WAJIB PALING BAWAH
Route::get('/{kecamatan}/{toko}', [PublicStoreController::class, 'show'])->name('store.show');