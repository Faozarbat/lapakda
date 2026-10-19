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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rate limit: registrasi toko dibatasi 5x per 10 menit -> cegah spam bikin toko fiktif
    Route::middleware('throttle:5,10')->group(function () {
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
// RUTE ADMIN LAPAKDA
// ===================================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');
});

// WAJIB PALING BAWAH
Route::get('/{kecamatan}/{toko}', [PublicStoreController::class, 'show'])->name('store.show');