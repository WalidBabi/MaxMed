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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Define the 'about' route
Route::get('/about', function () {
    return view('about');
})->name('about');



Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/dashboard', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{product}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    // Partners Routes
    Route::get('/partners', function () {
        return view('partners.index');
    })->name('partners.index');

    // News Routes
    Route::get('/news', function () {
        return view('news.index');
    })->name('news.index');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');


Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductController::class, 'showProduct'])->name('product.show');



// Update the News Routes
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

Route::get('/test-mail', function () {
    try {
        // Create a test order if none exists
        $order = Order::first();
        
        if (!$order) {
            $order = Order::create([
                'user_id' => 1, // Make sure this user exists
                'order_number' => 'TEST-' . uniqid(),
                'total_amount' => 99.99,
                'status' => 'pending',
                'shipping_address' => 'Test Address',
                'shipping_city' => 'Test City',
                'shipping_state' => 'Test State',
                'shipping_zipcode' => '12345',
                'shipping_phone' => '1234567890',
            ]);
        }

        Mail::to('cs@maxmedme.com')->send(new App\Mail\OrderPlaced($order));
        
        return 'Test email sent! Check logs for details.';
    } catch (\Exception $e) {
        // Log the error and return a more helpful message
        \Log::error('Mail test failed: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
})->name('test.mail');
// Product Routes


Route::get('/check-availability/{product}/{quantity}', [ProductController::class, 'checkAvailability'])->name('check.availability');

Route::get('/quotation/request/{product}', [QuotationController::class, 'request'])->name('quotation.request');
Route::post('/quotation/store', [QuotationController::class, 'store'])->name('quotation.store');
Route::get('/quotation/confirmation/{quotation}', [QuotationController::class, 'confirmation'])->name('quotation.confirmation');
Route::get('/quotation/{product}', [QuotationController::class, 'form'])->name('quotation.form');
// Admin Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth'])  // Explicitly include 'web' middleware
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        
        // News Management
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);
        
        // Product Management
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

        // Order Management
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status.update');

        // Category Management
        Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    });

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{category}/{subcategory}', [CategoryController::class, 'showSubcategory'])->name('categories.subcategory.show');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('product.show');

// Add this route for the categories index
Route::get('/admin/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');

require __DIR__ . '/auth.php';
