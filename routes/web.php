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
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\CrmLeadController;

// Cookie Consent Routes
// Route::post('/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie.consent');

// Google OAuth Routes
Route::get('/login/google', [GoogleController::class, 'redirect'])->name('login.google');
Route::get('/login/google/callback', [GoogleController::class, 'callback']);
Route::post('/google/one-tap', [GoogleController::class, 'handleOneTap'])->name('google.one-tap');

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/privacy-policy', function() {
    return view('privacy-policy');
})->name('privacy.policy');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

// Partners Route Group - Moved to the partners prefix group below
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('product.show');

// Backward compatibility redirect: old ID-based URLs to new slug-based URLs
Route::get('/product/{id}', function($id) {
    $product = \App\Models\Product::findOrFail($id);
    return redirect()->route('product.show', $product->slug, 301);
})->where('id', '[0-9]+')->name('product.show.old');

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
    Route::get('/{product:slug}', [QuotationController::class, 'redirect'])->name('redirect');
    Route::get('/{product:slug}/form', [QuotationController::class, 'form'])->name('form');
    Route::post('/store', [QuotationController::class, 'store'])->name('store');
    Route::get('/confirmation/{product:slug}', [QuotationController::class, 'confirmation'])->name('confirmation');
});

// Backward compatibility redirects: old ID-based quotation URLs to new slug-based URLs
Route::get('/quotation/{id}', function($id) {
    $product = \App\Models\Product::findOrFail($id);
    return redirect()->route('quotation.redirect', $product->slug, 301);
})->where('id', '[0-9]+')->name('quotation.redirect.old');

Route::get('/quotation/{id}/form', function($id) {
    $product = \App\Models\Product::findOrFail($id);
    return redirect()->route('quotation.form', $product->slug, 301);
})->where('id', '[0-9]+')->name('quotation.form.old');

Route::get('/quotation/confirmation/{id}', function($id) {
    $product = \App\Models\Product::findOrFail($id);
    return redirect()->route('quotation.confirmation', $product->slug, 301);
})->where('id', '[0-9]+')->name('quotation.confirmation.old');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Stripe Routes
    Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');

    // Quotation Routes that need authentication
    Route::prefix('quotation')->name('quotation.')->group(function () {
        Route::get('/request/{product:slug}', [QuotationController::class, 'request'])->name('request');
    });

    // CRM Routes
    Route::prefix('crm')->name('crm.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [CrmController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [CrmController::class, 'reports'])->name('reports');
    
    // Contact Submissions Management (CRM)
    Route::resource('contact-submissions', \App\Http\Controllers\Crm\ContactSubmissionController::class)->only(['index', 'show'])
        ->parameters(['contact-submissions' => 'submission']);
    Route::post('contact-submissions/{submission}/convert-to-lead', [\App\Http\Controllers\Crm\ContactSubmissionController::class, 'convertToLead'])->name('contact-submissions.convert-to-lead');
    Route::post('contact-submissions/{submission}/convert-to-inquiry', [\App\Http\Controllers\Crm\ContactSubmissionController::class, 'convertToInquiry'])->name('contact-submissions.convert-to-inquiry');
    Route::put('contact-submissions/{submission}/status', [\App\Http\Controllers\Crm\ContactSubmissionController::class, 'updateStatus'])->name('contact-submissions.status.update');
    Route::post('contact-submissions/{submission}/notes', [\App\Http\Controllers\Crm\ContactSubmissionController::class, 'addNotes'])->name('contact-submissions.add-notes');
    Route::post('contact-submissions/bulk-action', [\App\Http\Controllers\Crm\ContactSubmissionController::class, 'bulkAction'])->name('contact-submissions.bulk-action');
    
    // Quotation Requests Management (CRM)
    Route::resource('quotation-requests', \App\Http\Controllers\Crm\QuotationRequestController::class)->only(['index', 'show']);
    Route::post('quotation-requests/{quotationRequest}/convert-to-lead', [\App\Http\Controllers\Crm\QuotationRequestController::class, 'convertToLead'])->name('quotation-requests.convert-to-lead');
    Route::put('quotation-requests/{quotationRequest}/status', [\App\Http\Controllers\Crm\QuotationRequestController::class, 'updateStatus'])->name('quotation-requests.status.update');
    Route::post('quotation-requests/{quotationRequest}/notes', [\App\Http\Controllers\Crm\QuotationRequestController::class, 'addNotes'])->name('quotation-requests.add-notes');
    Route::post('quotation-requests/bulk-action', [\App\Http\Controllers\Crm\QuotationRequestController::class, 'bulkAction'])->name('quotation-requests.bulk-action');
    
    // Lead Management
    Route::resource('leads', CrmLeadController::class);
    Route::post('leads/{lead}/activity', [CrmLeadController::class, 'addActivity'])->name('leads.activity.add');
    Route::post('leads/{lead}/convert', [CrmLeadController::class, 'convert'])->name('leads.convert');
    Route::post('leads/{lead}/convert-to-deal', [CrmLeadController::class, 'convertToDeal'])->name('leads.convert-to-deal');
    Route::post('leads/{lead}/create-quotation-request', [CrmLeadController::class, 'createQuotationRequest'])->name('leads.create-quotation-request');
    Route::patch('leads/{lead}/status', [CrmLeadController::class, 'updateStatus'])->name('leads.update-status');
});

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        // News Management
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);

        // Product Management
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);

        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('status.update');
            Route::delete('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('destroy');
        });

        // Category Management
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        
        // Brand Management
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);

                // Delivery Management
        Route::resource('deliveries', \App\Http\Controllers\Admin\DeliveryController::class)->except(['show']);
        Route::get('deliveries/{delivery}', [\App\Http\Controllers\Admin\DeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('deliveries/{delivery}/mark-as-shipped', [\App\Http\Controllers\Admin\DeliveryController::class, 'markAsShipped'])->name('deliveries.mark-as-shipped');
        Route::post('deliveries/{delivery}/mark-as-delivered', [\App\Http\Controllers\Admin\DeliveryController::class, 'markAsDelivered'])->name('deliveries.mark-as-delivered');
        Route::post('deliveries/{delivery}/update-status', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
        Route::post('deliveries/{delivery}/convert-to-final-invoice', [\App\Http\Controllers\Admin\DeliveryController::class, 'convertToFinalInvoice'])->name('deliveries.convert-to-final-invoice');
        
        // Add link to deliveries in the admin sidebar
        Route::get('deliveries', [\App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('deliveries.index');
        
        // Customer Management
                    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
            Route::get('customers/{id}/details', [\App\Http\Controllers\Admin\CustomerController::class, 'getCustomerDetails'])->name('customers.details');
        Route::get('customers/by-name/{name}', [\App\Http\Controllers\Admin\CustomerController::class, 'getByName'])->name('customers.by-name');
        
        // Role Management
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::post('roles/{role}/toggle-status', [\App\Http\Controllers\Admin\RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        
        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        
        // Quote Management
        Route::resource('quotes', \App\Http\Controllers\Admin\QuoteController::class);
        Route::get('quotes/{quote}/pdf', [\App\Http\Controllers\Admin\QuoteController::class, 'generatePdf'])->name('quotes.pdf');
        Route::put('quotes/{quote}/status', [\App\Http\Controllers\Admin\QuoteController::class, 'updateStatus'])->name('quotes.status.update');
        Route::delete('quotes/{quote}/attachments', [\App\Http\Controllers\Admin\QuoteController::class, 'removeAttachment'])->name('quotes.attachments.remove');
        Route::post('quotes/{quote}/send-email', [\App\Http\Controllers\Admin\QuoteController::class, 'sendEmail'])->name('quotes.send-email');
        Route::get('quotes/{quote}/convert-to-proforma', [\App\Http\Controllers\Admin\QuoteController::class, 'convertToProforma'])->name('quotes.convert-to-proforma');
        
        // Invoice Management
        Route::resource('invoices', \App\Http\Controllers\Admin\InvoiceController::class);
        Route::post('quotes/{quote}/convert-to-invoice', [\App\Http\Controllers\Admin\InvoiceController::class, 'convertFromQuote'])->name('quotes.convert-to-invoice');
        Route::post('invoices/{invoice}/convert-to-final', [\App\Http\Controllers\Admin\InvoiceController::class, 'convertToFinal'])->name('invoices.convert-to-final');
        Route::put('invoices/{invoice}/status', [\App\Http\Controllers\Admin\InvoiceController::class, 'updateStatus'])->name('invoices.status.update');
        Route::post('invoices/{invoice}/record-payment', [\App\Http\Controllers\Admin\InvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
        Route::post('invoices/{invoice}/send-email', [\App\Http\Controllers\Admin\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
        Route::get('invoices/{invoice}/pdf', [\App\Http\Controllers\Admin\InvoiceController::class, 'generatePdf'])->name('invoices.pdf');
        Route::post('invoices/{invoice}/create-order', [\App\Http\Controllers\Admin\InvoiceController::class, 'createOrder'])->name('invoices.create-order');
        Route::post('invoices/{invoice}/link-delivery', [\App\Http\Controllers\Admin\InvoiceController::class, 'linkDelivery'])->name('invoices.link-delivery');
        
        // Purchase Order Management
        Route::resource('purchase-orders', \App\Http\Controllers\Admin\PurchaseOrderController::class);
        Route::post('purchase-orders/{purchaseOrder}/send-to-supplier', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'sendToSupplier'])->name('purchase-orders.send-to-supplier');
        Route::post('purchase-orders/{purchaseOrder}/acknowledge', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'markAsAcknowledged'])->name('purchase-orders.acknowledge');
        Route::post('purchase-orders/{purchaseOrder}/update-status', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
        Route::post('purchase-orders/{purchaseOrder}/create-payment', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'createPayment'])->name('purchase-orders.create-payment');
        
        // Supplier Payments Management
        Route::resource('supplier-payments', \App\Http\Controllers\Admin\SupplierPaymentController::class)->only(['index', 'show', 'edit', 'update']);
        Route::post('supplier-payments/{supplierPayment}/mark-completed', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'markAsCompleted'])->name('supplier-payments.mark-completed');
        Route::post('supplier-payments/{supplierPayment}/mark-failed', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'markAsFailed'])->name('supplier-payments.mark-failed');
        
        // Supplier Category Management
        Route::resource('supplier-categories', \App\Http\Controllers\Admin\SupplierCategoryController::class);
        Route::post('supplier-categories/bulk-status', [\App\Http\Controllers\Admin\SupplierCategoryController::class, 'bulkUpdateStatus'])->name('supplier-categories.bulk-status');
        Route::get('supplier-categories/export', [\App\Http\Controllers\Admin\SupplierCategoryController::class, 'export'])->name('supplier-categories.export');
        Route::get('api/suppliers-by-category', [\App\Http\Controllers\Admin\SupplierCategoryController::class, 'getSuppliersByCategory'])->name('api.suppliers-by-category');
        
        // Inquiry Management
        Route::resource('inquiries', \App\Http\Controllers\Admin\InquiryController::class);
        Route::post('inquiries/{inquiry}/forward-to-supplier', [\App\Http\Controllers\Admin\InquiryController::class, 'forwardToSupplier'])->name('inquiries.forward-to-supplier');
        Route::put('inquiries/{inquiry}/status', [\App\Http\Controllers\Admin\InquiryController::class, 'updateStatus'])->name('inquiries.status.update');
        Route::post('inquiries/{inquiry}/generate-quote', [\App\Http\Controllers\Admin\InquiryController::class, 'generateQuote'])->name('inquiries.generate-quote');
        Route::delete('inquiries/{inquiry}/cancel', [\App\Http\Controllers\Admin\InquiryController::class, 'cancel'])->name('inquiries.cancel');
        

        
        // Test route for debugging
        Route::post('quotes/test-store', function(\Illuminate\Http\Request $request) {
            \Log::info('Test store route hit', [
                'authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);
            return response()->json(['success' => true, 'message' => 'Test successful']);
        })->name('quotes.test.store');
    });

    // Supplier Routes
    Route::prefix('supplier')->name('supplier.')->middleware(['auth'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', \App\Http\Controllers\Supplier\ProductController::class);
        Route::resource('feedback', \App\Http\Controllers\Supplier\SystemFeedbackController::class);
        
        // Supplier Order Management Routes
        Route::get('/orders', [\App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Supplier\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/mark-processing', [\App\Http\Controllers\Supplier\OrderController::class, 'markAsProcessing'])->name('orders.mark-processing');
        Route::post('/orders/{order}/mark-pending', [\App\Http\Controllers\Supplier\OrderController::class, 'markAsPending'])->name('orders.mark-pending');
        Route::post('/orders/{order}/submit-documents', [\App\Http\Controllers\Supplier\OrderController::class, 'submitDocuments'])->name('orders.submit-documents');
        Route::get('/orders/{order}/download-packing-list', [\App\Http\Controllers\Supplier\OrderController::class, 'downloadPackingList'])->name('orders.download-packing-list');
        Route::get('/orders/{order}/download-commercial-invoice', [\App\Http\Controllers\Supplier\OrderController::class, 'downloadCommercialInvoice'])->name('orders.download-commercial-invoice');
        Route::post('/orders/{order}/update-status', [\App\Http\Controllers\Supplier\OrderController::class, 'updateStatus'])->name('orders.update-status');
        
        // Supplier Inquiry Management Routes
        Route::get('/inquiries', [\App\Http\Controllers\Supplier\InquiryController::class, 'index'])->name('inquiries.index');
        
        // Supplier Category Management Routes
        Route::get('/categories', [\App\Http\Controllers\Supplier\CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}', [\App\Http\Controllers\Supplier\CategoryController::class, 'show'])->name('categories.show');
        Route::post('/categories/request-assignment', [\App\Http\Controllers\Supplier\CategoryController::class, 'requestAssignment'])->name('categories.request-assignment');
        Route::get('/inquiries/{inquiry}', [\App\Http\Controllers\Supplier\InquiryController::class, 'show'])->name('inquiries.show');
        Route::post('/inquiries/{inquiry}/not-available', [\App\Http\Controllers\Supplier\InquiryController::class, 'respondNotAvailable'])->name('inquiries.not-available');
        Route::get('/inquiries/{inquiry}/quotation', [\App\Http\Controllers\Supplier\InquiryController::class, 'quotationForm'])->name('inquiries.quotation-form');
        Route::post('/inquiries/{inquiry}/quotation', [\App\Http\Controllers\Supplier\InquiryController::class, 'storeQuotation'])->name('inquiries.store-quotation');
        Route::post('/quotations/{quotation}/submit', [\App\Http\Controllers\Supplier\InquiryController::class, 'submitQuotation'])->name('quotations.submit');
    });

    // Orders Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Feedback Route
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// Public Delivery Tracking Routes (no authentication required)
Route::prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/track', [\App\Http\Controllers\DeliveryController::class, 'track'])->name('track');
    Route::get('/signature/{tracking}', [\App\Http\Controllers\DeliveryController::class, 'signature'])->name('signature');
    Route::post('/signature/{tracking}', [\App\Http\Controllers\DeliveryController::class, 'processSignature'])->name('process-signature');
    Route::get('/receipt/{tracking}', [\App\Http\Controllers\DeliveryController::class, 'downloadReceipt'])->name('receipt');
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

        $salesEmail = app()->environment('production') ? 'sales@maxmedme.com' : 'wbabi@localhost.com';
        Mail::to($salesEmail)->send(new App\Mail\OrderPlaced($order));

        return 'Test email sent! Check logs for details.';
    } catch (\Exception $e) {
        Log::error('Mail test failed: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
})->name('test.mail');

// Test Invoice Email Route
Route::get('/test-invoice-email', function () {
    try {
        $invoice = \App\Models\Invoice::first();
        if (!$invoice) {
            return 'No invoice found to test with';
        }

        $salesEmail = app()->environment('production') ? 'sales@maxmedme.com' : 'wbabi@localhost.com';
        
        $emailData = [
            'to_email' => 'test@example.com',
            'cc_emails' => [$salesEmail],
            'subject' => 'Test Invoice ' . $invoice->invoice_number,
            'message' => 'This is a test email'
        ];

        Mail::to('test@example.com')
            ->cc([$salesEmail])
            ->send(new \App\Mail\InvoiceEmail($invoice, $emailData));

        return 'Test invoice email sent! Check logs and email for details.';
    } catch (\Exception $e) {
        Log::error('Invoice mail test failed: ' . $e->getMessage());
        return 'Error: ' . $e->getMessage();
    }
})->name('test.invoice.mail');

// Delivery Signature Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/deliveries/{delivery}/sign', [\App\Http\Controllers\DeliverySignatureController::class, 'show'])
        ->name('deliveries.sign');
    Route::post('/deliveries/{delivery}/sign', [\App\Http\Controllers\DeliverySignatureController::class, 'store'])
        ->name('deliveries.sign.store');
    
    // Save signature to file
    Route::post('/deliveries/{delivery}/save-signature', [\App\Http\Controllers\SignatureController::class, 'saveSignature'])
        ->name('deliveries.signature.save');
});

// Orders Routes - moved to authenticated section

// Categories Routes
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    
    // Updated to use slug binding instead of numeric IDs
    Route::get('/{category:slug}', [CategoryController::class, 'show'])->name('show');
    
    // Use explicit binding without automatic nested resolution
    Route::get('/{category_slug}/{subcategory_slug}', [CategoryController::class, 'showSubcategory'])
        ->name('subcategory.show')
        ->where(['category_slug' => '[a-z0-9\-]+', 'subcategory_slug' => '[a-z0-9\-]+']);
    
    Route::get('/{category_slug}/{subcategory_slug}/{subsubcategory_slug}', [CategoryController::class, 'showSubSubcategory'])
        ->name('subsubcategory.show')
        ->where(['category_slug' => '[a-z0-9\-]+', 'subcategory_slug' => '[a-z0-9\-]+', 'subsubcategory_slug' => '[a-z0-9\-]+']);
        
    Route::get('/{category_slug}/{subcategory_slug}/{subsubcategory_slug}/subcategories', [CategoryController::class, 'showSubSubcategory'])
        ->name('subsubcategory.subsubcategories')
        ->where(['category_slug' => '[a-z0-9\-]+', 'subcategory_slug' => '[a-z0-9\-]+', 'subsubcategory_slug' => '[a-z0-9\-]+']);
        
    Route::get('/{category_slug}/{subcategory_slug}/{subsubcategory_slug}/{subsubsubcategory_slug}', [CategoryController::class, 'showSubSubSubcategory'])
        ->name('subsubsubcategory.show')
        ->where(['category_slug' => '[a-z0-9\-]+', 'subcategory_slug' => '[a-z0-9\-]+', 'subsubcategory_slug' => '[a-z0-9\-]+', 'subsubsubcategory_slug' => '[a-z0-9\-]+']);
});

// Backward compatibility redirects: old ID-based category URLs to new slug-based URLs
Route::get('/categories/{id}', function($id) {
    $category = \App\Models\Category::findOrFail($id);
    return redirect()->route('categories.show', $category->slug, 301);
})->where('id', '[0-9]+')->name('categories.show.old');

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

// Temporary route to check GD extension - remove after debugging
Route::get('/check-gd', function () {
    return response()->json([
        'php_version' => phpversion(),
        'gd_loaded' => extension_loaded('gd'),
        'gd_info' => extension_loaded('gd') ? gd_info() : 'GD not loaded',
        'functions' => [
            'imagecreatefromjpeg' => function_exists('imagecreatefromjpeg'),
            'imagecreatefrompng' => function_exists('imagecreatefrompng'),
            'getimagesize' => function_exists('getimagesize'),
        ]
    ]);
});

// Debug route for testing invoice workflow automation
Route::get('/debug/invoice/{invoice}/workflow', function(\App\Models\Invoice $invoice) {
    \Illuminate\Support\Facades\Log::info("DEBUG: Manual workflow test for invoice {$invoice->id}");
    
    $beforeOrderId = $invoice->order_id;
    $invoice->handleWorkflowAutomation();
    $afterOrderId = $invoice->fresh()->order_id;
    
    return response()->json([
        'invoice_id' => $invoice->id,
        'type' => $invoice->type,
        'status' => $invoice->status,
        'payment_status' => $invoice->payment_status,
        'paid_amount' => $invoice->paid_amount,
        'total_amount' => $invoice->total_amount,
        'payment_terms' => $invoice->payment_terms,
        'should_create_order' => $invoice->shouldCreateOrder(),
        'before_order_id' => $beforeOrderId,
        'after_order_id' => $afterOrderId,
        'order_created' => $beforeOrderId != $afterOrderId
    ]);
})->name('debug.invoice.workflow');

// Debug route for testing quote to invoice conversion
Route::get('/debug/quote/{quote}/to-invoice', function(\App\Models\Quote $quote) {
    $quote->load('items.product');
    
    $quoteData = [
        'quote_id' => $quote->id,
        'quote_number' => $quote->quote_number,
        'customer_name' => $quote->customer_name,
        'items' => []
    ];
    
    foreach ($quote->items as $item) {
        $quoteData['items'][] = [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_name' => $item->product ? $item->product->name : 'No Product',
            'item_details' => $item->item_details,
            'quantity' => $item->quantity,
            'rate' => $item->rate,
            'discount' => $item->discount,
            'amount' => $item->amount,
        ];
    }
    
    // Check if invoice already exists
    $invoice = \App\Models\Invoice::where('quote_id', $quote->id)->first();
    $invoiceData = null;
    
    if ($invoice) {
        $invoice->load('items.product');
        $invoiceData = [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'type' => $invoice->type,
            'items' => []
        ];
        
        foreach ($invoice->items as $item) {
            $invoiceData['items'][] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product ? $item->product->name : 'No Product',
                'item_description' => $item->item_description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_percentage' => $item->discount_percentage,
                'line_total' => $item->line_total,
            ];
        }
    }
    
    return response()->json([
        'quote' => $quoteData,
        'invoice' => $invoiceData,
        'invoice_exists' => $invoice ? true : false,
        'product_id_transferred' => $invoice ? 
            collect($invoice->items)->every(fn($item) => !is_null($item->product_id)) : false
    ]);
})->name('debug.quote.to-invoice');

// Debug route for testing payment workflow step by step
Route::get('/debug/invoice/{invoice}/payment-workflow', function(\App\Models\Invoice $invoice) {
    $invoice->load(['payments', 'items', 'order']);
    
    // Step 1: Check current invoice state
    $currentState = [
        'invoice_id' => $invoice->id,
        'invoice_number' => $invoice->invoice_number,
        'type' => $invoice->type,
        'status' => $invoice->status,
        'payment_status' => $invoice->payment_status,
        'payment_terms' => $invoice->payment_terms,
        'total_amount' => $invoice->total_amount,
        'paid_amount' => $invoice->paid_amount,
        'advance_percentage' => $invoice->advance_percentage,
        'order_id' => $invoice->order_id,
        'existing_order' => $invoice->order ? $invoice->order->order_number : null,
    ];
    
    // Step 2: Check payments
    $payments = [];
    foreach ($invoice->payments as $payment) {
        $payments[] = [
            'id' => $payment->id,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'payment_date' => $payment->payment_date->format('Y-m-d'),
        ];
    }
    
    // Step 3: Check workflow conditions
    $workflowChecks = [
        'is_proforma' => $invoice->type === 'proforma',
        'status_confirmed' => $invoice->status === 'confirmed',
        'should_create_order' => $invoice->shouldCreateOrder(),
        'no_existing_order' => !$invoice->order_id,
        'has_items' => $invoice->items->count() > 0,
    ];
    
    // Step 4: Check advance payment requirements
    $advanceChecks = [];
    if ($invoice->payment_terms === 'advance_50') {
        $requiredAmount = $invoice->total_amount * 0.5;
        $advanceChecks = [
            'payment_terms' => 'advance_50',
            'required_amount' => $requiredAmount,
            'paid_amount' => $invoice->paid_amount,
            'threshold_met' => $invoice->paid_amount >= $requiredAmount,
        ];
    } elseif ($invoice->payment_terms === 'advance_100') {
        $advanceChecks = [
            'payment_terms' => 'advance_100',
            'required_amount' => $invoice->total_amount,
            'paid_amount' => $invoice->paid_amount,
            'threshold_met' => $invoice->payment_status === 'paid',
        ];
    } elseif ($invoice->payment_terms === 'custom' && $invoice->advance_percentage) {
        $requiredAmount = ($invoice->total_amount * $invoice->advance_percentage) / 100;
        $advanceChecks = [
            'payment_terms' => 'custom',
            'advance_percentage' => $invoice->advance_percentage,
            'required_amount' => $requiredAmount,
            'paid_amount' => $invoice->paid_amount,
            'threshold_met' => $invoice->paid_amount >= $requiredAmount,
        ];
    } else {
        $advanceChecks = [
            'payment_terms' => $invoice->payment_terms,
            'note' => 'No advance payment required for this payment term',
            'threshold_met' => false,
        ];
    }
    
    // Step 5: Simulate workflow automation
    $workflowResult = [];
    try {
        // Test if workflow would run
        $allConditionsMet = $workflowChecks['is_proforma'] && 
                           $workflowChecks['status_confirmed'] && 
                           $workflowChecks['should_create_order'] && 
                           $workflowChecks['no_existing_order'];
        
        $workflowResult = [
            'all_conditions_met' => $allConditionsMet,
            'would_create_order' => $allConditionsMet,
            'simulation_note' => $allConditionsMet ? 'Order would be created' : 'Conditions not met for order creation'
        ];
        
        // If conditions are met, let's see what would happen in order creation
        if ($allConditionsMet) {
            $orderData = [
                'would_create_user_id' => $invoice->getCustomerUserId(),
                'would_create_order_number' => 'ORD-' . str_pad(\App\Models\Order::max('id') + 1, 6, '0', STR_PAD_LEFT),
                'would_create_total_amount' => $invoice->total_amount,
                'would_create_status' => 'processing',
                'item_count' => $invoice->items->count(),
            ];
            $workflowResult['order_simulation'] = $orderData;
        }
        
    } catch (\Exception $e) {
        $workflowResult['error'] = $e->getMessage();
    }
    
    return response()->json([
        'current_state' => $currentState,
        'payments' => $payments,
        'workflow_checks' => $workflowChecks,
        'advance_checks' => $advanceChecks,
        'workflow_result' => $workflowResult,
        'debug_summary' => [
            'total_payments' => count($payments),
            'completed_payments' => collect($payments)->where('status', 'completed')->count(),
            'total_paid' => collect($payments)->where('status', 'completed')->sum('amount'),
            'should_auto_create_order' => $workflowChecks['is_proforma'] && 
                                        $workflowChecks['status_confirmed'] && 
                                        $workflowChecks['should_create_order'] && 
                                        $workflowChecks['no_existing_order']
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.invoice.payment-workflow');

// Manual trigger for workflow automation (testing)
Route::post('/debug/invoice/{invoice}/trigger-workflow', function(\App\Models\Invoice $invoice) {
    \Illuminate\Support\Facades\Log::info("=== MANUAL WORKFLOW TRIGGER START ===");
    \Illuminate\Support\Facades\Log::info("Invoice {$invoice->id} before workflow: type={$invoice->type}, status={$invoice->status}, payment_status={$invoice->payment_status}, paid_amount={$invoice->paid_amount}, order_id={$invoice->order_id}");
    
    try {
        $invoice->handleWorkflowAutomation();
        
        $freshInvoice = $invoice->fresh();
        \Illuminate\Support\Facades\Log::info("Invoice {$invoice->id} after workflow: status={$freshInvoice->status}, order_id={$freshInvoice->order_id}");
        
        return response()->json([
            'success' => true,
            'message' => 'Workflow automation triggered successfully',
            'before' => [
                'status' => $invoice->status,
                'payment_status' => $invoice->payment_status,
                'order_id' => $invoice->order_id,
            ],
            'after' => [
                'status' => $freshInvoice->status,
                'payment_status' => $freshInvoice->payment_status,
                'order_id' => $freshInvoice->order_id,
            ]
        ]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Manual workflow trigger failed: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
})->name('debug.invoice.trigger-workflow');

// Debug route for testing delivery-proforma invoice relationship
Route::get('/debug/delivery/{delivery}/proforma', function(\App\Models\Delivery $delivery) {
    $delivery->load(['order.proformaInvoice', 'order.invoice']);
    
    $debug = [
        'delivery_id' => $delivery->id,
        'order_id' => $delivery->order_id,
        'order_exists' => $delivery->order ? true : false,
    ];
    
    if ($delivery->order) {
        $debug['order_details'] = [
            'id' => $delivery->order->id,
            'order_number' => $delivery->order->order_number,
            'status' => $delivery->order->status,
        ];
        
        // Test different ways to get proforma invoice
        $debug['proforma_tests'] = [
            'via_order_proforma_invoice' => $delivery->order->proformaInvoice ? [
                'id' => $delivery->order->proformaInvoice->id,
                'invoice_number' => $delivery->order->proformaInvoice->invoice_number,
                'type' => $delivery->order->proformaInvoice->type,
            ] : null,
            'via_order_invoice' => $delivery->order->invoice ? [
                'id' => $delivery->order->invoice->id,
                'invoice_number' => $delivery->order->invoice->invoice_number,
                'type' => $delivery->order->invoice->type,
            ] : null,
            'via_delivery_proforma_invoice_query' => $delivery->proformaInvoice()->first() ? [
                'id' => $delivery->proformaInvoice()->first()->id,
                'invoice_number' => $delivery->proformaInvoice()->first()->invoice_number,
                'type' => $delivery->proformaInvoice()->first()->type,
            ] : null,
            'via_delivery_get_proforma_invoice' => $delivery->getProformaInvoice() ? [
                'id' => $delivery->getProformaInvoice()->id,
                'invoice_number' => $delivery->getProformaInvoice()->invoice_number,
                'type' => $delivery->getProformaInvoice()->type,
            ] : null,
        ];
        
        // Test the methods used in the view
        $debug['view_methods'] = [
            'hasConvertibleProformaInvoice' => $delivery->hasConvertibleProformaInvoice(),
            'getConvertibleProformaInvoice' => $delivery->getConvertibleProformaInvoice() ? [
                'id' => $delivery->getConvertibleProformaInvoice()->id,
                'invoice_number' => $delivery->getConvertibleProformaInvoice()->invoice_number,
            ] : null,
            'proformaInvoice_exists' => $delivery->proformaInvoice()->exists(),
        ];
    }
    
    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
})->name('debug.delivery.proforma');

require __DIR__ . '/auth.php';
