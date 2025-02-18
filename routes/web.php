<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Define the 'about' route
Route::get('/about', function () {
    return view('about');
})->name('about');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/dashboard', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::post('/cart/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'removeFromCart'])->name('cart.remove');

});



require __DIR__ . '/auth.php';
