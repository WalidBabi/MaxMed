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
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndustryController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::get('/check-availability/{product}/{quantity}', [ProductController::class, 'checkAvailability'])->name('check.availability');

// Industry routes
Route::get('/industries', function() {
    return view('industries.index');
})->name('industry.index');

// Cart Routes - Keep publicly accessible for SEO
Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{product}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');

// Quotation routes for viewing - Move outside auth for SEO
Route::prefix('quotation')->name('quotation.')->group(function () {
    Route::get('/{product}', [QuotationController::class, 'redirect'])->name('redirect');
    Route::get('/{product}/form', [QuotationController::class, 'form'])->name('form');
    Route::post('/store', [QuotationController::class, 'store'])->name('store');
    Route::get('/confirmation/{product}', [QuotationController::class, 'confirmation'])->name('confirmation');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Stripe Routes
    Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');

    // Quotation Routes that need authentication
    Route::prefix('quotation')->name('quotation.')->group(function () {
        Route::get('/request/{product}', [QuotationController::class, 'request'])->name('request');
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
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        
        // Brand Management
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
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

        Mail::to('sales@maxmedme.com')->send(new App\Mail\OrderPlaced($order));

        return 'Test email sent! Check logs for details.';
    } catch (\Exception $e) {
        Log::error('Mail test failed: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
})->name('test.mail');

// Orders Routes
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// Categories Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    
    // Add middleware to check if category exists
    Route::get('/{category}', [CategoryController::class, 'show'])->name('show')
        ->where('category', '[0-9]+'); // Ensure only numeric IDs are accepted
    
    Route::get('/{category}/{subcategory}', [CategoryController::class, 'showSubcategory'])->name('subcategory.show')
        ->where(['category' => '[0-9]+', 'subcategory' => '[0-9]+']); // Validate both IDs
    
    Route::get('/{category}/{subcategory}/{subsubcategory}', [CategoryController::class, 'showSubSubcategory'])->name('subsubcategory.show')
        ->where(['category' => '[0-9]+', 'subcategory' => '[0-9]+', 'subsubcategory' => '[0-9]+']); // Validate all IDs
        
    Route::get('/{category}/{subcategory}/{subsubcategory}/subcategories', [CategoryController::class, 'showSubSubcategory'])->name('subsubcategory.subsubcategories')
        ->where(['category' => '[0-9]+', 'subcategory' => '[0-9]+', 'subsubcategory' => '[0-9]+']); // For showing the subsubcategories view
        
    Route::get('/{category}/{subcategory}/{subsubcategory}/{subsubsubcategory}', [CategoryController::class, 'showSubSubSubcategory'])->name('subsubsubcategory.show')
        ->where(['category' => '[0-9]+', 'subcategory' => '[0-9]+', 'subsubcategory' => '[0-9]+', 'subsubsubcategory' => '[0-9]+']); // Validate all IDs
});

// Partners Routes
Route::prefix('partners')->name('partners.')->group(function () {
    Route::get('/', [PartnersController::class, 'index'])->name('index');
});

// Show Products Route
Route::get('/showproducts', [ProductController::class, 'showProducts'])->name('showproducts');

// Search Route
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// QR Code Route
Route::get('/qr-code', [QRCodeController::class, 'generate'])->name('qr.code');

// Contact Form Route
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');

// Legacy URL redirects for 404 pages in Google Search Console
Route::get('/education%26-training-tools', function() {
    return redirect('/products', 301);
});
Route::get('/analytical-chemistry', function() {
    return redirect('/products', 301);
});
Route::get('/genomics-%26-life-sciences', function() {
    return redirect('/products', 301);
});
Route::get('/veterinary-%26-agri-tools', function() {
    return redirect('/products', 301);
});
Route::get('/forensic-supplies', function() {
    return redirect('/products', 301);
});
Route::get('/molecular-biology', function() {
    return redirect('/products', 301);
});
Route::get('/research-%26-life-sciences', function() {
    return redirect('/products', 301);
});

require __DIR__ . '/auth.php';
