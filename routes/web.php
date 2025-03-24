<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\SearchController;

// Public Routes
Route::get('/', fn() => view('welcome'))->name('welcome');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/check-availability/{product}/{quantity}', [ProductController::class, 'checkAvailability'])->name('check.availability');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => view('welcome'))->middleware('verified')->name('dashboard');

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'viewCart'])->name('view');
        Route::match(['get', 'post'], '/add/{product}', [CartController::class, 'add'])->name('add');
        Route::post('/remove/{product}', [CartController::class, 'removeFromCart'])->name('remove');
        Route::post('/update/{id}', [CartController::class, 'update'])->name('update');
    });

    // Orders Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    // Stripe Routes
    Route::prefix('stripe')->name('stripe.')->group(function () {
        Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
        Route::get('/success', [StripeController::class, 'success'])->name('success');
    });

    // Quotation Routes
    Route::prefix('quotation')->name('quotation.')->group(function () {
        Route::get('/request/{product}', [QuotationController::class, 'request'])->name('request');
        Route::post('/store', [QuotationController::class, 'store'])->name('store');
        Route::get('/confirmation/{product}', [QuotationController::class, 'confirmation'])->name('confirmation');
        Route::get('/{product}', [QuotationController::class, 'form'])->name('form');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('web')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        // News Management
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);

        // Product Management
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('status.update');
        });

        // Category Management
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('store');
            Route::delete('/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('destroy');
        });
    });
});

// Test Mail Route
Route::get('/test-mail', function () {
    try {
        $order = Order::firstOrCreate([
            'user_id' => 1,
            'order_number' => 'TEST-' . uniqid(),
            'total_amount' => 99.99,
            'status' => 'pending',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Test City',
            'shipping_state' => 'Test State',
            'shipping_zipcode' => '12345',
            'shipping_phone' => '1234567890',
        ]);

        Mail::to('cs@maxmedme.com')->send(new App\Mail\OrderPlaced($order));

        return 'Test email sent! Check logs for details.';
    } catch (\Exception $e) {
        Log::error('Mail test failed: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
})->name('test.mail');

// Categories Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
    Route::get('/{category}/{subcategory}', [CategoryController::class, 'showSubcategory'])->name('subcategory.show');
});

// Partners Routes
Route::prefix('partners')->name('partners.')->group(function () {
    Route::get('/', [PartnersController::class, 'index'])->name('index');
});

// Show Products Route
Route::get('/showproducts', [ProductController::class, 'showProducts'])->name('showproducts');

// Search Route
Route::get('/search', [SearchController::class, 'search'])->name('search');

require __DIR__ . '/auth.php';
