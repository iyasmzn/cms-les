<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SpmbController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Blog
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
Route::post('/blog/{post}/komentar', [CommentController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('comments.store');

// Tenaga Pendidik
Route::get('/guru', [TeacherController::class, 'index'])->name('teachers.index');
Route::get('/guru/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');

// Halaman Statis
Route::get('/page/{slug}', [StaticPageController::class, 'show'])->name('page.show');

// PPDB / SPMB
Route::get('/ppdb', [SpmbController::class, 'index'])->name('ppdb.index');
Route::get('/ppdb/{institution:slug}', [SpmbController::class, 'show'])->name('ppdb.show');
Route::post('/ppdb/{institution:slug}', [SpmbController::class, 'store'])->middleware('throttle:8,1')->name('ppdb.store');
Route::get('/panitia/ppdb-berkas/{registration}/{field}', [SpmbController::class, 'downloadBerkas'])
    ->middleware('auth')
    ->name('ppdb.berkas');

// Unduhan
Route::get('/unduhan', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/unduhan/{download}/download', [DownloadController::class, 'download'])->name('downloads.download');

// ── Autentikasi Pengguna Umum ────────────────────────────────
Route::middleware(['guest', 'feature:login_register'])->group(function () {
    Route::get('/masuk', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/masuk', [LoginController::class, 'login']);
    Route::get('/daftar', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'register']);
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});
Route::post('/keluar', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Verifikasi Email ─────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// ── Pengaturan Profil & Permintaan Menjadi Author ────────────
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/kata-sandi', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Produk & Keranjang (Toko)
Route::middleware('feature:toko')->group(function () {
    Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/keranjang/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/keranjang', [CartController::class, 'clear'])->name('cart.clear');
});

// Checkout (harus login; nonaktif jika Login & Register dimatikan)
Route::middleware(['auth', 'feature:toko_checkout'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
});

// Kegiatan / Event
Route::get('/kegiatan', [EventController::class, 'index'])->name('events.index');
Route::get('/kegiatan/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Program
Route::get('/program', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/program/{program:slug}', [ProgramController::class, 'show'])->name('programs.show');

// Kontak
Route::get('/kontak', [ContactController::class, 'index'])->name('contact.index');

// Galeri
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery.index');
