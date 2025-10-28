<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
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
use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\OrderQuotationsController;
use App\Http\Controllers\Admin\SupplierCategoryController;
use App\Http\Controllers\Supplier\InquiryController;

// Cookie Consent Routes
// Route::post('/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie.consent');

// Google OAuth Routes with crawler protection
Route::middleware(['prevent-crawler-indexing'])->group(function () {
    Route::get('/login/google', [GoogleController::class, 'redirect'])->name('login.google');
    Route::get('/login/google/callback', [GoogleController::class, 'callback']);
    Route::post('/google/one-tap', [GoogleController::class, 'handleOneTap'])->name('google.one-tap');
    
    // Handle GET requests to /google/one-tap (for crawlers)
    Route::get('/google/one-tap', [GoogleController::class, 'handleOneTapGet'])->name('google.one-tap.get');
});

// Test custom logging route
Route::get('/test-custom-log', function() {
    $customLog = app('App\Services\CustomLogService');
    $customLog->info('Test custom log entry from route');
    $customLog->error('Test error from custom log');
    return response()->json(['message' => 'Custom log entries written successfully']);
})->name('test.custom.log');

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/privacy-policy', function() {
    return view('privacy-policy');
})->name('privacy.policy');
Route::get('/about', fn() => view('about'))->name('about');
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

// Quotation routes - Public access
Route::get('/quotation/{product:slug}', [QuotationController::class, 'redirect'])->name('quotation.redirect');
Route::get('/quotation/form/{product:slug}', [QuotationController::class, 'form'])->name('quotation.form');
Route::post('/quotation/store', [QuotationController::class, 'store'])->name('quotation.store');
Route::get('/quotation/confirmation/{product:slug}', [QuotationController::class, 'confirmation'])->name('quotation.confirmation');

// Marketing Unsubscribe Route (public)
Route::get('/marketing/unsubscribe/{token}', [\App\Http\Controllers\MarketingController::class, 'unsubscribe'])->name('marketing.unsubscribe');

// Email Tracking Routes (public, no authentication required)
Route::get('/email/track/open/{trackingId}', [\App\Http\Controllers\EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('/email/track/click/{trackingId}', [\App\Http\Controllers\EmailTrackingController::class, 'trackClick'])->name('email.track.click');
Route::get('/email/unsubscribe/{token}', [\App\Http\Controllers\EmailTrackingController::class, 'trackUnsubscribe'])->name('email.track.unsubscribe');

// User Behavior Tracking Routes (public, no authentication required)
Route::post('/api/user-behavior/track', [\App\Http\Controllers\UserBehaviorController::class, 'track'])->name('user-behavior.track');
Route::post('/api/user-behavior/track-batch', [\App\Http\Controllers\UserBehaviorController::class, 'trackBatch'])->name('user-behavior.track-batch');
Route::get('/api/user-behavior/analytics', [\App\Http\Controllers\UserBehaviorController::class, 'analytics'])->name('user-behavior.analytics');
Route::get('/api/user-behavior/journey', [\App\Http\Controllers\UserBehaviorController::class, 'userJourney'])->name('user-behavior.journey');

// Test route for user behavior tracking
Route::get('/test-tracking', function() {
    return view('test-tracking');
})->name('test.tracking');

// Test route for cookie consent
Route::get('/test-cookie-clear', function() {
    return view('test-cookie-clear');
})->name('test.cookie.clear');

// Debug route for testing tracking
Route::get('/debug/campaign/{campaignId}', function($campaignId) {
    $campaign = \App\Models\Campaign::with(['contacts', 'emailLogs'])->find($campaignId);
    
    if (!$campaign) {
        return response()->json(['error' => 'Campaign not found'], 404);
    }
    
    $contact = $campaign->contacts()->first();
    $emailLog = $campaign->emailLogs()->first();
    
    if (!$contact || !$emailLog) {
        return response()->json(['error' => 'No contact or email log found'], 404);
    }
    
    $trackingService = new \App\Services\EmailTrackingService();
    
    return response()->json([
        'campaign' => [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'statistics' => [
                'recipients' => $campaign->total_recipients,
                'sent' => $campaign->sent_count,
                'delivered' => $campaign->delivered_count,
                'opens' => $campaign->opened_count,
                'clicks' => $campaign->clicked_count
            ]
        ],
        'tracking_urls' => [
            'open' => $trackingService->generateTrackingPixelUrl($campaign, $contact, $emailLog),
            'click' => $trackingService->generateClickTrackingUrl($campaign, $contact, $emailLog, 'https://example.com'),
            'unsubscribe' => $trackingService->generateUnsubscribeUrl($contact, $campaign)
        ],
        'email_log' => [
            'id' => $emailLog->id,
            'status' => $emailLog->status,
            'opened_at' => $emailLog->opened_at,
            'clicked_at' => $emailLog->clicked_at
        ]
    ]);
})->name('debug.campaign');

// Product Routes
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [\App\Http\Controllers\ProductController::class, 'show'])->name('product.show');

// Legacy product URL redirect (ID-based to slug-based)
Route::get('/product/{id}', function($id) {
    $product = \App\Models\Product::findOrFail($id);
    return redirect()->route('product.show', $product->slug, 301);
})->where('id', '[0-9]+')->name('product.show.old');

// Fix specific problematic product URLs from Search Console
Route::get('/product/117', function() {
    $product = \App\Models\Product::find(117);
    if ($product && $product->slug) {
        return redirect()->route('product.show', $product->slug, 301);
    }
    return redirect('/products', 301);
});

Route::get('/product/186', function() {
    $product = \App\Models\Product::find(186);
    if ($product && $product->slug) {
        return redirect()->route('product.show', $product->slug, 301);
    }
    return redirect('/products', 301);
});

// Handle ALL problematic product IDs found in Google Search Console CSV
$problematicProductIds = [238, 368, 369, 370, 424, 374, 342, 359, 71, 125, 360, 47, 313, 58, 367, 93, 69, 56, 70, 146, 165, 404, 192, 128, 381, 100, 383, 423, 422, 414, 396, 391, 425, 361, 39, 222, 386, 91, 278, 257, 154, 382, 259, 237, 213, 317, 111, 384, 243, 232, 53, 57, 220, 94, 392, 64, 332, 375, 344, 250, 88, 420, 338, 84, 207, 115, 102, 337, 43, 211, 42, 242, 235, 240, 319, 188, 78, 74, 224, 314, 276, 226, 219, 304, 230, 307, 309, 303, 280, 229, 85, 103, 37, 379, 331, 279, 40, 274, 312, 305, 311, 399, 347, 389, 363, 283, 60, 249, 62, 95, 200, 117, 186, 82, 255, 234, 263, 187, 265, 90, 133, 46, 153, 52, 106, 38, 164, 228, 175, 249, 124, 233, 256, 248, 259, 86, 61, 271, 264, 166, 190, 274, 277, 276, 281, 283, 189, 270, 187, 185, 140, 137, 87, 114, 89, 136, 141, 59, 51, 104, 98, 107, 110, 49, 50, 284, 206, 285, 109, 207, 209, 76, 280, 135, 48, 112, 108, 105, 99, 129, 171, 116, 275, 97, 236, 282, 177, 54, 156, 143, 167, 101, 139, 173, 142, 163, 134, 168, 131, 159, 130, 157, 88, 128, 138, 165, 150, 111, 35, 36];

foreach ($problematicProductIds as $productId) {
    Route::get("/product/{$productId}", function() use ($productId) {
        $product = \App\Models\Product::find($productId);
        if ($product && $product->slug) {
            return redirect()->route('product.show', $product->slug, 301);
        }
        return redirect('/products', 301);
    });
}

// Legacy www.maxmedme.com redirects - Handled by quotation.redirect.old route

// Fix category URLs with specific IDs from CSV
$problematicCategoryIds = [64, 56, 52, 85, 113, 46, 153, 69, 163, 188, 109, 135, 177, 99, 190, 91, 145, 106];
foreach ($problematicCategoryIds as $categoryId) {
    Route::get("/categories/{$categoryId}", function() use ($categoryId) {
        $category = \App\Models\Category::find($categoryId);
        if ($category) {
            return redirect("/categories/{$category->slug}", 301);
        }
        return redirect('/categories', 301);
    });
    
    Route::get("/categories/{$categoryId}/{sub}", function($categoryId, $sub) {
        return redirect('/categories', 301);
    })->where(['categoryId' => '[0-9]+', 'sub' => '.*']);
}

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

// Backward compatibility redirects: old ID-based quotation URLs to new slug-based URLs
Route::get('/quotation/{id}', function($id) {
    $product = \App\Models\Product::findOrFail($id);
    return redirect()->route('quotation.redirect', $product->slug, 301);
})->where('id', '[0-9]+')->name('quotation.redirect.old');

// Comprehensive redirects for all problematic URLs from Google Search Console CSV
// These URLs are being blocked by robots.txt and need proper 301 redirects

// Quotation form redirects for specific IDs found in CSV - ACTIVE
$quotationFormIds = [80, 45, 307, 309, 303, 315, 310, 314, 47, 79, 74, 75, 72, 82, 311, 221, 230, 44, 225, 224, 223, 227, 229, 222, 92, 81, 233, 226, 219, 312, 306, 71, 43, 316, 70, 175, 228, 115, 117, 118, 123, 122, 127, 249, 124, 119, 120, 255, 125, 126, 245, 212, 121, 250, 145, 73, 64, 57, 261, 266, 78, 263, 267, 63, 96, 246, 273, 279, 272, 254, 93, 258, 252, 260, 253, 247, 244, 251, 256, 234, 257, 248, 259, 86, 61, 271, 264, 166, 278, 190, 274, 277, 276, 281, 283, 189, 270, 91, 187, 185, 140, 137, 90, 87, 114, 94, 89, 136, 141, 59, 51, 133, 113, 104, 98, 107, 103, 102, 100, 55, 110, 49, 52, 50, 60, 284, 206, 265, 285, 109, 207, 209, 69, 76, 85, 56, 280, 135, 48, 112, 108, 105, 99, 129, 186, 171, 188, 116, 275, 97, 58, 236, 282, 177, 54, 156, 143, 167, 101, 139, 173, 142, 163, 134, 164, 168, 131, 159, 130, 157, 88, 53, 128, 138, 165, 153, 150, 111, 38, 35, 40, 37, 42, 46, 36];

foreach ($quotationFormIds as $formId) {
    Route::get("/quotation/{$formId}/form", function($formId) {
        $product = \App\Models\Product::find($formId);
        if ($product && $product->slug) {
            return redirect()->route('quotation.form', $product->slug, 301);
        }
        return redirect('/quotation/form', 301);
    })->where('formId', '[0-9]+');
}

// Quotation page redirects (without /form)
// TEMPORARILY COMMENTED OUT TO TEST MAIN ROUTES
$quotationIds = [80, 92, 43, 35, 40, 37, 42, 46, 36];

// foreach ($quotationIds as $quotationId) {
//     Route::get("/quotation/{$quotationId}", function($quotationId) {
//         $product = \App\Models\Product::find($quotationId);
//         if ($product && $product->slug) {
//             return redirect()->route('quotation.redirect', $product->slug, 301);
//         }
//         return redirect('/quotation/form', 301);
//     })->where('quotationId', '[0-9]+');
// }

// Category redirects for specific problematic category combinations from CSV
$categoryCombinations = [
    '66/71/73' => 'analytical-chemistry/laboratory-equipment/centrifuges',
    '50/58' => 'research-life-sciences/laboratory-equipment',
    '66/71/72' => 'analytical-chemistry/laboratory-equipment/shakers',
    '60/77' => 'molecular-biology/dna-rna-extraction',
    '55/61' => 'genomics-life-sciences/pcr-thermocyclers',
    '51/39/86' => 'research-life-sciences/analytical-instruments/spectrophotometers',
    '57/74' => 'genomics-life-sciences/laboratory-equipment',
    '46' => 'laboratory-equipment',
    '55/59' => 'genomics-life-sciences/microscopes',
    '43/46' => 'analytical-chemistry/laboratory-equipment',
    '76' => 'forensic-supplies',
    '55/58' => 'genomics-life-sciences/laboratory-equipment',
    '55' => 'genomics-life-sciences',
    '56' => 'molecular-biology',
    '51/60' => 'research-life-sciences/molecular-biology',
    '51/55/59' => 'research-life-sciences/genomics-life-sciences/microscopes',
    '79' => 'veterinary-agri-tools',
    '34/36' => 'education-training-tools/laboratory-equipment',
    '44' => 'analytical-chemistry',
    '40' => 'forensic-supplies',
    '34' => 'education-training-tools',
    '50' => 'research-life-sciences',
    '43' => 'analytical-chemistry',
    '49/51/52' => 'education-training-tools/research-life-sciences/laboratory-equipment',
    '49/51' => 'education-training-tools/research-life-sciences',
    '72' => 'laboratory-equipment'
];

foreach ($categoryCombinations as $oldPath => $newPath) {
    Route::get("/categories/{$oldPath}", function() use ($newPath) {
        return redirect("/categories/{$newPath}", 301);
    });
}

// Guest Order Tracking Routes (no authentication required)
Route::get('/track-order/{order}', [\App\Http\Controllers\OrderTrackingController::class, 'track'])->name('orders.track');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Customer Order Routes - for regular users
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');

    // Stripe Routes
    Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
    
    // Invoice Payment Routes (public - accessible without auth)
    Route::get('/invoice/{invoice}/payment/success', [\App\Http\Controllers\InvoicePaymentController::class, 'paymentSuccess'])->name('invoice.payment.success');
    Route::get('/invoice/{invoice}/payment/view', [\App\Http\Controllers\InvoicePaymentController::class, 'viewInvoice'])->name('invoice.payment.view');

    // Quotation Routes that need authentication
    Route::prefix('quotation')->name('quotation.')->group(function () {
        Route::get('/request/{product:slug}', [QuotationController::class, 'request'])->name('request');
    });

    // CRM Routes
    Route::prefix('crm')->name('crm.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [CrmController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [CrmController::class, 'reports'])->name('reports');
    
    // CRM Notifications
    Route::get('notifications', [\App\Http\Controllers\Crm\NotificationController::class, 'index']);
    Route::get('notifications/check-new', [\App\Http\Controllers\Crm\NotificationController::class, 'checkNew']);
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Crm\NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\Crm\NotificationController::class, 'markAllAsRead']);
    Route::get('notifications/count', [\App\Http\Controllers\Crm\NotificationController::class, 'count']);
    
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
    Route::patch('leads/{lead}/status', [CrmLeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('leads/{lead}/send-email', [CrmLeadController::class, 'sendEmail'])->name('leads.send-email');
    Route::get('leads/{lead}/requirements', [CrmLeadController::class, 'viewRequirements'])->name('leads.view-requirements');
    
    // Enhanced Lead Management Features
    Route::post('leads/bulk-status-update', [CrmLeadController::class, 'bulkStatusUpdate'])->name('leads.bulk-status-update');
    Route::post('leads/bulk-assign', [CrmLeadController::class, 'bulkAssign'])->name('leads.bulk-assign');
    Route::get('leads-stats', [CrmLeadController::class, 'getPipelineStats'])->name('leads.pipeline-stats');
    Route::post('leads-quick-add', [CrmLeadController::class, 'quickAdd'])->name('leads.quick-add');
    Route::get('leads-export', [CrmLeadController::class, 'export'])->name('leads.export');
    
    // Lead Price Submissions
    Route::post('leads/{lead}/price-submissions', [\App\Http\Controllers\CrmLeadPriceController::class, 'store'])->name('leads.price-submissions.store');
    Route::get('leads/{lead}/price-submissions', [\App\Http\Controllers\CrmLeadPriceController::class, 'index'])->name('leads.price-submissions.index');
    Route::get('price-submissions/{priceSubmission}/attachments/{attachmentIndex}', [\App\Http\Controllers\CrmLeadPriceController::class, 'downloadAttachment'])->name('price-submissions.download-attachment');
    
    // Customer Management
    Route::resource('customers', \App\Http\Controllers\Crm\CustomerController::class);
    Route::post('customers/{customer}/convert-to-lead', [\App\Http\Controllers\Crm\CustomerController::class, 'convertToLead'])->name('customers.convert-to-lead');
    Route::get('customers/{id}/details', [\App\Http\Controllers\Crm\CustomerController::class, 'getCustomerDetails'])->name('customers.details');
    Route::get('customers/by-name/{name}', [\App\Http\Controllers\Crm\CustomerController::class, 'getByName'])->name('customers.by-name');
    
    // Marketing System Routes
    Route::prefix('marketing')->name('marketing.')->group(function () {
        // Marketing Dashboard
        Route::get('/', [\App\Http\Controllers\Crm\MarketingController::class, 'dashboard'])->name('dashboard');
        
        // Marketing Contacts
        Route::resource('contacts', \App\Http\Controllers\Crm\MarketingContactController::class);
        Route::post('contacts/import', [\App\Http\Controllers\Crm\MarketingContactController::class, 'import'])->name('contacts.import');
        Route::get('contacts/export', [\App\Http\Controllers\Crm\MarketingContactController::class, 'export'])->name('contacts.export');
        Route::patch('contacts/{contact}/unsubscribe', [\App\Http\Controllers\Crm\MarketingContactController::class, 'unsubscribe'])->name('contacts.unsubscribe');
        Route::patch('contacts/{contact}/resubscribe', [\App\Http\Controllers\Crm\MarketingContactController::class, 'resubscribe'])->name('contacts.resubscribe');
        
        // Contact Lists
        Route::resource('contact-lists', \App\Http\Controllers\Crm\ContactListController::class);
        Route::post('contact-lists/{contactList}/refresh', [\App\Http\Controllers\Crm\ContactListController::class, 'refresh'])->name('contact-lists.refresh');
        
        // Email Templates
        Route::resource('email-templates', \App\Http\Controllers\Crm\EmailTemplateController::class);
        Route::post('email-templates/{emailTemplate}/clone', [\App\Http\Controllers\Crm\EmailTemplateController::class, 'clone'])->name('email-templates.clone');
        Route::get('email-templates/{emailTemplate}/preview', [\App\Http\Controllers\Crm\EmailTemplateController::class, 'preview'])->name('email-templates.preview');
        
        // Campaigns
        Route::resource('campaigns', \App\Http\Controllers\Crm\CampaignController::class);
        Route::get('campaigns/export', [\App\Http\Controllers\Crm\CampaignController::class, 'export'])->name('campaigns.export');
        Route::post('campaigns/{campaign}/duplicate', [\App\Http\Controllers\Crm\CampaignController::class, 'duplicate'])->name('campaigns.duplicate');
        Route::get('campaigns/{campaign}/preview', [\App\Http\Controllers\Crm\CampaignController::class, 'preview'])->name('campaigns.preview');
        Route::get('campaigns/{campaign}/statistics', [\App\Http\Controllers\Crm\CampaignController::class, 'getStatistics'])->name('campaigns.statistics');
        Route::post('campaigns/{campaign}/schedule', [\App\Http\Controllers\Crm\CampaignController::class, 'schedule'])->name('campaigns.schedule');
        Route::post('campaigns/{campaign}/send', [\App\Http\Controllers\Crm\CampaignController::class, 'send'])->name('campaigns.send');
        Route::post('campaigns/{campaign}/select-winner', [\App\Http\Controllers\Crm\CampaignController::class, 'selectWinner'])->name('campaigns.select-winner');
        Route::patch('campaigns/{campaign}/pause', [\App\Http\Controllers\Crm\CampaignController::class, 'pause'])->name('campaigns.pause');
        Route::patch('campaigns/{campaign}/resume', [\App\Http\Controllers\Crm\CampaignController::class, 'resume'])->name('campaigns.resume');
        Route::patch('campaigns/{campaign}/cancel', [\App\Http\Controllers\Crm\CampaignController::class, 'cancel'])->name('campaigns.cancel');
        
        // Analytics
        Route::get('analytics', [\App\Http\Controllers\Crm\MarketingAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('analytics/campaigns', [\App\Http\Controllers\Crm\MarketingAnalyticsController::class, 'campaigns'])->name('analytics.campaigns');
        Route::get('analytics/contacts', [\App\Http\Controllers\Crm\MarketingAnalyticsController::class, 'contacts'])->name('analytics.contacts');
        
        // User Behavior Analytics - Removed duplicate route
    });
});

    // Admin Routes
    Route::middleware(['web', 'auth', 'verified', \App\Http\Middleware\AdminMiddleware::class])->name('admin.')->prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/sales-data', [\App\Http\Controllers\Admin\DashboardController::class, 'getSalesData'])->name('dashboard.sales-data');

        // Advanced Analytics Routes
        Route::get('/analytics', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'index'])->name('analytics.dashboard');
        Route::get('/analytics/data', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'getAnalyticsData'])->name('analytics.data');
        Route::get('/analytics/revenue', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'getRevenueAnalytics'])->name('analytics.revenue');
        Route::get('/analytics/cash-flow', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'getCashFlowAnalytics'])->name('analytics.cash-flow');
        Route::get('/analytics/sales-performance', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'getSalesPerformanceAnalytics'])->name('analytics.sales-performance');
        Route::get('/analytics/customers', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'getCustomerAnalytics'])->name('analytics.customers');
        Route::get('/analytics/products', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'getProductAnalytics'])->name('analytics.products');
        Route::get('/analytics/export', [\App\Http\Controllers\Admin\SalesAnalyticsController::class, 'exportAnalytics'])->name('analytics.export');

        // Inquiry Management
        Route::resource('inquiries', \App\Http\Controllers\Admin\InquiryController::class)->names([
            'index' => 'admin.inquiries.index',
            'create' => 'admin.inquiries.create',
            'store' => 'admin.inquiries.store',
            'show' => 'admin.inquiries.show',
            'edit' => 'admin.inquiries.edit',
            'update' => 'admin.inquiries.update',
            'destroy' => 'admin.inquiries.destroy',
        ]);
        Route::post('inquiries/{inquiry}/broadcast', [\App\Http\Controllers\Admin\InquiryController::class, 'broadcast'])->name('admin.inquiries.broadcast');
        Route::put('inquiries/{inquiry}/status', [\App\Http\Controllers\Admin\InquiryController::class, 'updateStatus'])->name('admin.inquiries.status.update');
        Route::post('inquiries/bulk-forward', [\App\Http\Controllers\Admin\InquiryController::class, 'bulkForward'])->name('admin.inquiries.bulk-forward');
        Route::post('inquiries/{inquiry}/forward', [\App\Http\Controllers\Admin\InquiryController::class, 'forwardToSupplier'])->name('admin.inquiries.forward');
        Route::post('inquiries/{inquiry}/generate-quote', [\App\Http\Controllers\Admin\InquiryController::class, 'generateQuote'])->name('admin.inquiries.generate-quote');
        Route::post('inquiries/{inquiry}/create-purchase-order', [\App\Http\Controllers\Admin\InquiryController::class, 'createPurchaseOrder'])->name('admin.inquiries.create-purchase-order');
        Route::get('inquiries/supplier-recommendations', [\App\Http\Controllers\Admin\InquiryController::class, 'getSupplierRecommendations'])->name('admin.inquiries.supplier-recommendations');
        Route::get('inquiries/status-updates', [\App\Http\Controllers\Admin\InquiryController::class, 'getStatusUpdates'])->name('inquiries.status-updates');

        // News Management
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);

        // Product Management
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        
        // Product Specifications Management
        Route::get('product-specifications', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'index'])->name('product-specifications.index');
        Route::get('products/{product}/specifications', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'show'])->name('product-specifications.show');
        Route::get('products/{product}/specifications/create', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'create'])->name('product-specifications.create');
        Route::post('products/{product}/specifications', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'store'])->name('product-specifications.store');
        Route::get('products/{product}/specifications/{specification}/edit', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'edit'])->name('product-specifications.edit');
        Route::put('products/{product}/specifications/{specification}', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'update'])->name('product-specifications.update');
        Route::delete('products/{product}/specifications/{specification}', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'destroy'])->name('product-specifications.destroy');
        Route::post('products/{product}/specifications/bulk', [App\Http\Controllers\Admin\ProductSpecificationController::class, 'bulkCreate'])->name('product-specifications.bulk-create');

        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('status.update');
            Route::delete('/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('destroy');
        });
        
        // AJAX routes for order creation
        Route::get('products/{product}/ajax/specifications', [\App\Http\Controllers\Admin\ProductController::class, 'getSpecifications'])->name('products.ajax.specifications');
        Route::get('products/{product}/ajax/size-options', [\App\Http\Controllers\Admin\ProductController::class, 'getSizeOptions'])->name('products.ajax.size-options');
        Route::get('invoices/{invoice}/ajax/details', [\App\Http\Controllers\Admin\InvoiceController::class, 'getDetails'])->name('invoices.ajax.details');

        // Category Management - Using slug-based routing
        Route::get('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category:slug}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category:slug}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category:slug}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Brand Management
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
        // AJAX endpoint to create brand without page refresh
        Route::post('brands/ajax', [\App\Http\Controllers\Admin\BrandController::class, 'storeAjax'])
            ->name('brands.ajax.store');

                // Delivery Management
        Route::resource('deliveries', \App\Http\Controllers\Admin\DeliveryController::class)->except(['show']);
        Route::get('deliveries/{delivery}', [\App\Http\Controllers\Admin\DeliveryController::class, 'show'])->name('deliveries.show');
        Route::post('deliveries/{delivery}/mark-as-shipped', [\App\Http\Controllers\Admin\DeliveryController::class, 'markAsShipped'])->name('deliveries.mark-as-shipped');
        Route::post('deliveries/{delivery}/mark-as-delivered', [\App\Http\Controllers\Admin\DeliveryController::class, 'markAsDelivered'])->name('deliveries.mark-as-delivered');
        Route::post('deliveries/{delivery}/update-status', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateStatus'])->name('deliveries.update-status');
        Route::post('deliveries/{delivery}/convert-to-final-invoice', [\App\Http\Controllers\Admin\DeliveryController::class, 'convertToFinalInvoice'])->name('deliveries.convert-to-final-invoice');
        Route::get('deliveries/{delivery}/pdf', [\App\Http\Controllers\Admin\DeliveryController::class, 'generatePdf'])->name('deliveries.pdf');
        
        // Add link to deliveries in the admin sidebar
        Route::get('deliveries', [\App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('deliveries.index');
        
        // Customer Management moved to CRM Portal
        Route::get('customers/by-name/{name}', [\App\Http\Controllers\Crm\CustomerController::class, 'getByName'])->name('customers.by-name');
        
        // Role Management
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::post('roles/{role}/toggle-status', [\App\Http\Controllers\Admin\RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
        
        // Permission Documentation Guide
        Route::get('/permission-guide', function () {
            $permissions = \App\Models\Permission::where('is_active', true)->get()->groupBy('category');
            
            // Get categories from actual permissions in database, with fallback to static method
            $permissionCategories = \App\Models\Permission::getCategories();
            $actualCategories = $permissions->keys()->mapWithKeys(function($category) use ($permissionCategories) {
                return [$category => $permissionCategories[$category] ?? ucwords(str_replace('_', ' ', $category))];
            });
            
            $permissionDocumentation = \App\Services\PermissionDocumentationService::getAllPermissionDocumentation();
            
            return view('admin.permissions.index', compact('permissions', 'actualCategories', 'permissionDocumentation'));
        })->name('permissions.guide')->middleware('permission:roles.view');
        
        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        
        // Quote Management
        Route::resource('quotes', \App\Http\Controllers\Admin\QuoteController::class);
        // Fetch possible recipient names for a given email (used by Send Quote modal)
        Route::get('quotes/names', [\App\Http\Controllers\Admin\QuoteController::class, 'getNamesByEmail'])
            ->name('admin.quotes.names');
        Route::get('quotes/{quote}/pdf', [\App\Http\Controllers\Admin\QuoteController::class, 'generatePdf'])->name('quotes.pdf');
        Route::put('quotes/{quote}/status', [\App\Http\Controllers\Admin\QuoteController::class, 'updateStatus'])->name('quotes.status.update');
        Route::delete('quotes/{quote}/attachments', [\App\Http\Controllers\Admin\QuoteController::class, 'removeAttachment'])->name('quotes.attachments.remove');
        Route::post('quotes/{quote}/send-email', [\App\Http\Controllers\Admin\QuoteController::class, 'sendEmail'])->name('quotes.send-email');
        Route::post('quotes/{quote}/convert-to-proforma', [\App\Http\Controllers\Admin\QuoteController::class, 'convertToProforma'])->name('quotes.convert-to-proforma');
Route::get('quotes/search/suggestions', [\App\Http\Controllers\Admin\QuoteController::class, 'searchSuggestions'])->name('quotes.search-suggestions');
        
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
        Route::post('invoices/{invoice}/generate-payment-link', [\App\Http\Controllers\Admin\InvoiceController::class, 'generatePaymentLink'])->name('invoices.generate-payment-link');
        
        // Purchase Order Management
        Route::resource('purchase-orders', \App\Http\Controllers\Admin\PurchaseOrderController::class);
        Route::post('purchase-orders/{purchaseOrder}/send-email', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'sendEmail'])->name('purchase-orders.send-email');
        Route::post('purchase-orders/{purchaseOrder}/acknowledge', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'markAsAcknowledged'])->name('purchase-orders.acknowledge');
        Route::post('purchase-orders/{purchaseOrder}/update-status', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
        Route::get('purchase-orders/{purchaseOrder}/pdf', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'generatePdf'])->name('purchase-orders.pdf');
        Route::post('purchase-orders/{purchaseOrder}/create-payment', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'createPayment'])->name('purchase-orders.create-payment');
        
        Route::get('api/orders/{order}/items', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'getOrderItems'])->name('api.orders.items');

        // Unified Inquiry & Quotation Management Routes
        Route::get('inquiry-quotations', [\App\Http\Controllers\Admin\InquiryQuotationController::class, 'index'])->name('inquiry-quotations.index');
        Route::get('inquiry-quotations/{inquiry}', [\App\Http\Controllers\Admin\InquiryQuotationController::class, 'show'])->name('inquiry-quotations.show');
        Route::get('inquiry-quotations/quotations/{quotation}', [\App\Http\Controllers\Admin\InquiryQuotationController::class, 'showQuotation'])->name('inquiry-quotations.quotation-show');
        Route::post('inquiry-quotations/quotations/{quotation}/approve', [\App\Http\Controllers\Admin\InquiryQuotationController::class, 'approveQuotation'])->name('inquiry-quotations.quotations.approve');
        Route::post('inquiry-quotations/quotations/bulk-approve', [\App\Http\Controllers\Admin\InquiryQuotationController::class, 'bulkApprove'])->name('inquiry-quotations.quotations.bulk-approve');

        // Legacy routes (keep for backward compatibility)
        Route::get('quotations', [\App\Http\Controllers\Admin\QuotationController::class, 'index'])->name('quotations.index');
        Route::get('quotations/{quotation}', [\App\Http\Controllers\Admin\QuotationController::class, 'show'])->name('quotations.show');
        Route::post('quotations/{quotation}/approve', [\App\Http\Controllers\Admin\QuotationController::class, 'approve'])->name('quotations.approve');

        Route::post('quotations/bulk-action', [\App\Http\Controllers\Admin\QuotationController::class, 'bulkAction'])->name('quotations.bulk-action');
        Route::get('quotations/{quotation}/workflow-status', [\App\Http\Controllers\Admin\QuotationController::class, 'workflowStatus'])->name('quotations.workflow-status');
        
        // Cash Receipts Management
        Route::resource('cash-receipts', \App\Http\Controllers\Admin\CashReceiptController::class)->except(['edit', 'update']);
        Route::get('cash-receipts/{cashReceipt}/pdf', [\App\Http\Controllers\Admin\CashReceiptController::class, 'downloadPdf'])->name('cash-receipts.pdf');
        Route::post('cash-receipts/{cashReceipt}/send-email', [\App\Http\Controllers\Admin\CashReceiptController::class, 'sendEmail'])->name('cash-receipts.send-email');
        Route::patch('cash-receipts/{cashReceipt}/cancel', [\App\Http\Controllers\Admin\CashReceiptController::class, 'cancel'])->name('cash-receipts.cancel');
        Route::post('orders/{order}/quick-cash-receipt', [\App\Http\Controllers\Admin\CashReceiptController::class, 'quickCreate'])->name('orders.quick-cash-receipt');

        // Supplier Payments Management
        Route::resource('supplier-payments', \App\Http\Controllers\Admin\SupplierPaymentController::class)->only(['index', 'show', 'edit', 'update']);
        Route::post('supplier-payments/{supplierPayment}/mark-completed', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'markAsCompleted'])->name('supplier-payments.mark-completed');
        Route::post('supplier-payments/{supplierPayment}/mark-failed', [\App\Http\Controllers\Admin\SupplierPaymentController::class, 'markAsFailed'])->name('supplier-payments.mark-failed');
        
        // Supplier Category Management
        Route::get('supplier-categories', [SupplierCategoryController::class, 'index'])->name('supplier-categories.index');
        Route::get('supplier-categories/response-times', [SupplierCategoryController::class, 'responseTimes'])->name('supplier-categories.response-times');
        Route::get('supplier-categories/{supplier}/edit', [SupplierCategoryController::class, 'edit'])->name('supplier-categories.edit');
        Route::post('supplier-categories/{supplier}/categories', [SupplierCategoryController::class, 'update'])->name('supplier-categories.update');
        Route::post('supplier-categories/{supplier}/{category}/approve', [SupplierCategoryController::class, 'approve'])->name('supplier-categories.approve');
        Route::post('supplier-categories/{supplier}/{category}/reject', [SupplierCategoryController::class, 'reject'])->name('supplier-categories.reject');
        Route::get('supplier-categories/export', [SupplierCategoryController::class, 'export'])->name('supplier-categories.export');
        
        // Supplier Profiles Management
        Route::get('supplier-profiles', [\App\Http\Controllers\Admin\SupplierProfileController::class, 'index'])->name('supplier-profiles.index');
        Route::get('supplier-profiles/{supplier}', [\App\Http\Controllers\Admin\SupplierProfileController::class, 'show'])->name('supplier-profiles.show');
        Route::get('supplier-profiles/{supplier}/documents/{documentType}', [\App\Http\Controllers\Admin\SupplierProfileController::class, 'downloadDocument'])->name('supplier-profiles.download-document');
        Route::get('supplier-profiles/{supplier}/certifications/{index}', [\App\Http\Controllers\Admin\SupplierProfileController::class, 'downloadCertification'])->name('supplier-profiles.download-certification');
        Route::post('supplier-profiles/{supplier}/update-status', [\App\Http\Controllers\Admin\SupplierProfileController::class, 'updateStatus'])->name('supplier-profiles.update-status');
        
        // Feedback Management
        Route::resource('feedback', \App\Http\Controllers\Admin\FeedbackController::class)->only(['index', 'show', 'update']);
        Route::get('feedback/stats', [\App\Http\Controllers\Admin\FeedbackController::class, 'stats'])->name('feedback.stats');
        Route::get('feedback/system/{systemFeedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'showSystem'])->name('feedback.show-system');
        Route::put('feedback/system/{systemFeedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'updateSystem'])->name('feedback.update-system');
        
        // Notifications Management
        Route::get('notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('notifications/count', [\App\Http\Controllers\Admin\NotificationController::class, 'count'])->name('notifications.count');
        Route::get('notifications/stream', [\App\Http\Controllers\Admin\NotificationController::class, 'stream'])->name('notifications.stream');
        Route::get('notifications/check-new', [\App\Http\Controllers\Admin\NotificationController::class, 'checkNew'])->name('notifications.check-new');
        

        
        // Test route for debugging
        Route::post('quotes/test-store', function(\Illuminate\Http\Request $request) {
            \Log::info('Test store route hit', [
                'authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);
            return response()->json(['success' => true, 'message' => 'Test successful']);
        })->name('quotes.test.store');

        // Test route for cash receipt debugging
        Route::post('cash-receipts/test-store', function(\Illuminate\Http\Request $request) {
            \Log::info('Cash receipt test store route hit', [
                'authenticated' => auth()->check(),
                'user_id' => auth()->id(),
                'data' => $request->all(),
                'method' => $request->method(),
                'url' => $request->url()
            ]);
            return response()->json(['success' => true, 'message' => 'Cash receipt test successful']);
        })->name('cash-receipts.test.store');

        // Order Quotations
        Route::get('/orders/{order}/quotations', [OrderQuotationsController::class, 'index'])->name('orders.quotations.index');
        Route::post('/orders/{order}/quotations/{quotation}/approve', [OrderQuotationsController::class, 'approve'])->name('orders.quotations.approve');

        // User Behavior Analytics
        Route::get('user-behavior', function() {
            return view('admin.user-behavior.index');
        })->name('user-behavior.index');
    });

    // Supplier Onboarding Routes (accessible without authentication for invitation flow)
    Route::prefix('supplier/onboarding')->name('supplier.onboarding.')->group(function () {
        Route::get('/company', [\App\Http\Controllers\Supplier\OnboardingController::class, 'company'])->name('company');
        Route::post('/company', [\App\Http\Controllers\Supplier\OnboardingController::class, 'storeCompany'])->name('company.store');
        Route::get('/documents', [\App\Http\Controllers\Supplier\OnboardingController::class, 'documents'])->name('documents');
        Route::post('/documents', [\App\Http\Controllers\Supplier\OnboardingController::class, 'storeDocuments'])->name('documents.store');
        Route::get('/categories', [\App\Http\Controllers\Supplier\OnboardingController::class, 'categories'])->name('categories');
        Route::post('/categories', [\App\Http\Controllers\Supplier\OnboardingController::class, 'storeCategories'])->name('categories.store');
    });

    // Main Supplier Routes (protected by onboarding middleware)
    Route::middleware(['auth', 'verified', \App\Http\Middleware\SupplierBadgeCountsMiddleware::class, \App\Http\Middleware\CheckSupplierOnboarding::class])->prefix('supplier')->name('supplier.')->group(function () {
        // Supplier Notifications
        Route::get('notifications', [\App\Http\Controllers\Supplier\NotificationController::class, 'index']);
        Route::get('notifications/check-new', [\App\Http\Controllers\Supplier\NotificationController::class, 'checkNew']);
        Route::post('notifications/{id}/read', [\App\Http\Controllers\Supplier\NotificationController::class, 'markAsRead']);
        Route::post('notifications/mark-all-read', [\App\Http\Controllers\Supplier\NotificationController::class, 'markAllAsRead']);
        Route::get('notifications/count', [\App\Http\Controllers\Supplier\NotificationController::class, 'count']);
        
        Route::get('/', [\App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
        
        // Supplier Product Management Routes
        Route::get('/products', [\App\Http\Controllers\Supplier\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [\App\Http\Controllers\Supplier\ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [\App\Http\Controllers\Supplier\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [\App\Http\Controllers\Supplier\ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/edit', [\App\Http\Controllers\Supplier\ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [\App\Http\Controllers\Supplier\ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [\App\Http\Controllers\Supplier\ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/toggle-status', [\App\Http\Controllers\Supplier\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        
        // Supplier Product Specifications Routes
        Route::get('/product-specifications', [\App\Http\Controllers\Supplier\ProductSpecificationController::class, 'index'])->name('product-specifications.index');
        Route::get('/products/{product}/specifications', [\App\Http\Controllers\Supplier\ProductSpecificationController::class, 'show'])->name('product-specifications.show');
        Route::get('/products/{product}/specifications/edit', [\App\Http\Controllers\Supplier\ProductSpecificationController::class, 'edit'])->name('product-specifications.edit');
        Route::put('/products/{product}/specifications', [\App\Http\Controllers\Supplier\ProductSpecificationController::class, 'update'])->name('product-specifications.update');
        Route::post('/products/{product}/specifications/bulk-import', [\App\Http\Controllers\Supplier\ProductSpecificationController::class, 'bulkImport'])->name('product-specifications.bulk-import');
        
        // Supplier API routes
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/category-specifications/{category}', [\App\Http\Controllers\Supplier\ProductController::class, 'getCategorySpecifications'])->name('category-specifications');
        });
        
        // Supplier Order Management Routes
        Route::get('/orders', [\App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Supplier\OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/quotation', [\App\Http\Controllers\Supplier\OrderController::class, 'showQuotationForm'])->name('orders.quotation');
        Route::post('/orders/{order}/quotation', [\App\Http\Controllers\Supplier\OrderController::class, 'submitQuotation'])->name('orders.submit-quotation');
        Route::post('/orders/{order}/mark-processing', [\App\Http\Controllers\Supplier\OrderController::class, 'markAsProcessing'])->name('orders.mark-processing');
        Route::post('/orders/{order}/mark-pending', [\App\Http\Controllers\Supplier\OrderController::class, 'markAsPending'])->name('orders.mark-pending');
        Route::post('/orders/{order}/submit-documents', [\App\Http\Controllers\Supplier\OrderController::class, 'submitDocuments'])->name('orders.submit-documents');
        Route::get('/orders/{order}/download-packing-list', [\App\Http\Controllers\Supplier\OrderController::class, 'downloadPackingList'])->name('orders.download-packing-list');
        Route::get('/orders/{order}/download-commercial-invoice', [\App\Http\Controllers\Supplier\OrderController::class, 'downloadCommercialInvoice'])->name('orders.download-commercial-invoice');
        
        // Supplier Inquiry Management Routes
        Route::get('inquiries', [App\Http\Controllers\Supplier\InquiryController::class, 'index'])->name('supplier.inquiries.index');
        Route::get('inquiries/{inquiry}', [App\Http\Controllers\Supplier\InquiryController::class, 'show'])->name('supplier.inquiries.show');
        Route::patch('inquiries/{inquiry}/status', [App\Http\Controllers\Supplier\InquiryController::class, 'updateStatus'])->name('supplier.inquiries.status.update');
        Route::post('inquiries/{inquiry}/not-available', [App\Http\Controllers\Supplier\InquiryController::class, 'respondNotAvailable'])->name('supplier.inquiries.not-available');
        Route::get('inquiries/{inquiry}/quotation', [App\Http\Controllers\Supplier\InquiryController::class, 'quotationForm'])->name('supplier.inquiries.quotation');
        Route::post('inquiries/{inquiry}/quotation', [App\Http\Controllers\Supplier\InquiryController::class, 'store'])->name('supplier.inquiries.quotation.store');
        
        // Supplier Category Management Routes
        Route::get('/categories', [\App\Http\Controllers\Supplier\CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}', [\App\Http\Controllers\Supplier\CategoryController::class, 'show'])->name('categories.show')->where('category', '[0-9]+');
        Route::post('/categories/request-assignment', [\App\Http\Controllers\Supplier\CategoryController::class, 'requestAssignment'])->name('categories.request-assignment');
        
        // Supplier Feedback Routes
        Route::get('/feedback', [\App\Http\Controllers\Supplier\SystemFeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/feedback/create', [\App\Http\Controllers\Supplier\SystemFeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/feedback', [\App\Http\Controllers\Supplier\SystemFeedbackController::class, 'store'])->name('feedback.store');
        Route::get('/feedback/{feedback}', [\App\Http\Controllers\Supplier\SystemFeedbackController::class, 'show'])->name('feedback.show');

        // Supplier Purchase Order Routes
        Route::get('/purchase-orders', [\App\Http\Controllers\Supplier\OrderController::class, 'purchaseOrders'])->name('purchase-orders.index');
        Route::get('/purchase-orders/{purchaseOrder}', [\App\Http\Controllers\Supplier\OrderController::class, 'showPurchaseOrder'])->name('purchase-orders.show');
        Route::post('/purchase-orders/{purchaseOrder}/acknowledge', [\App\Http\Controllers\Supplier\OrderController::class, 'acknowledgePurchaseOrder'])->name('purchase-orders.acknowledge');
    });

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



// Debug Notifications Route
Route::get('/debug-notifications', function () {
    $currentUser = Auth::user();
    if (!$currentUser) {
        return "❌ Not logged in";
    }
    
    $isAdmin = $currentUser->is_admin;
    $notifications = $currentUser->notifications()->orderBy('created_at', 'desc')->limit(5)->get();
    $unreadCount = $currentUser->unreadNotifications()->count();
    
    $output = "🔍 Debug Notification System\n\n";
    $output .= "Current User: {$currentUser->name} (ID: {$currentUser->id})\n";
    $output .= "Is Admin: " . ($isAdmin ? "✅ YES" : "❌ NO") . "\n";
    $output .= "Total Notifications: {$notifications->count()}\n";
    $output .= "Unread Notifications: {$unreadCount}\n\n";
    
    if ($notifications->count() > 0) {
        $output .= "📋 Recent Notifications:\n";
        foreach ($notifications as $notification) {
            $message = $notification->data['message'] ?? 'No message';
            $read = $notification->read_at ? 'Read' : 'Unread';
            $output .= "- {$message} ({$read}, {$notification->created_at})\n";
        }
    } else {
        $output .= "❌ No notifications found for this user\n";
    }
    
    // Check all notifications in the database
    $allNotifications = DB::table('notifications')->count();
    $output .= "\n🗄️ Total notifications in database: {$allNotifications}\n";
    
    return "<pre>{$output}</pre>";
})->middleware('auth')->name('debug.notifications');

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
            'size' => $item->size,
            'specifications' => $item->specifications,
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
                'description' => $item->description,
                'item_description' => $item->item_description ?? 'N/A',
                'size' => $item->size,
                'specifications' => $item->specifications,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount_percentage' => $item->discount_percentage,
                'line_total' => $item->formatted_line_total,
            ];
        }
    }
    
    return response()->json([
        'quote' => $quoteData,
        'invoice' => $invoiceData,
        'invoice_exists' => $invoice ? true : false,
        'size_transferred' => $invoice ? 
            collect($invoice->items)->every(fn($item) => !is_null($item->size)) : false,
        'size_values' => $invoice ? 
            collect($invoice->items)->pluck('size')->toArray() : []
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

// Test notification system
Route::get('/test-notification', function () {
    if (!app()->environment('production')) {
        // Create a test contact submission to trigger notifications
        $submission = App\Models\ContactSubmission::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test Notification',
            'message' => 'This is a test notification to verify the system works.',
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test notification triggered',
            'submission_id' => $submission->id
        ]);
    }
    
    return response()->json(['error' => 'Only available in development'], 403);
})->name('test.notification');

// Health check endpoint for production debugging
Route::get('/health', function () {
    try {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'database' => 'disconnected',
            'cache' => 'not working',
            'sessions' => 'not working',
            'admin_users' => 0,
            'errors' => []
        ];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $health['database'] = 'connected';
        } catch (\Exception $e) {
            $health['database'] = 'disconnected';
            $health['errors'][] = 'Database: ' . $e->getMessage();
        }

        // Check cache
        try {
            Cache::put('health_check', 'working', 1);
            if (Cache::get('health_check') === 'working') {
                $health['cache'] = 'working';
                Cache::forget('health_check');
            }
        } catch (\Exception $e) {
            $health['errors'][] = 'Cache: ' . $e->getMessage();
        }

        // Check sessions table
        try {
            if (Schema::hasTable('sessions')) {
                $sessionCount = DB::table('sessions')->count();
                $health['sessions'] = 'working (' . $sessionCount . ' sessions)';
            } else {
                $health['sessions'] = 'table missing';
                $health['errors'][] = 'Sessions table does not exist';
            }
        } catch (\Exception $e) {
            $health['errors'][] = 'Sessions: ' . $e->getMessage();
        }

        // Check admin users
        try {
            $adminCount = User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->count();
            $health['admin_users'] = $adminCount;
            
            if ($adminCount === 0) {
                $health['errors'][] = 'No admin users found';
            }
        } catch (\Exception $e) {
            $health['errors'][] = 'Admin users: ' . $e->getMessage();
        }

        // Determine overall status
        if (!empty($health['errors'])) {
            $health['status'] = 'unhealthy';
        }

        $statusCode = $health['status'] === 'healthy' ? 200 : 503;
        
        return response()->json($health, $statusCode);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Health check failed: ' . $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('health.check');

// Emergency diagnostic route - bypasses all middleware
Route::get('/emergency-diagnostic', function () {
    try {
        $diagnostic = [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_debug' => config('app.debug'),
            'app_env' => config('app.env'),
            'database_connection' => config('database.default'),
            'session_driver' => config('session.driver'),
            'cache_store' => config('cache.default'),
            'tests' => []
        ];

        // Test 1: Basic PHP functionality
        try {
            $diagnostic['tests']['php_basic'] = 'working';
        } catch (\Exception $e) {
            $diagnostic['tests']['php_basic'] = 'failed: ' . $e->getMessage();
        }

        // Test 2: Laravel basic functionality
        try {
            $diagnostic['tests']['laravel_basic'] = 'working';
        } catch (\Exception $e) {
            $diagnostic['tests']['laravel_basic'] = 'failed: ' . $e->getMessage();
        }

        // Test 3: Database connection
        try {
            DB::connection()->getPdo();
            $diagnostic['tests']['database'] = 'connected';
        } catch (\Exception $e) {
            $diagnostic['tests']['database'] = 'failed: ' . $e->getMessage();
        }

        // Test 4: Sessions table
        try {
            if (Schema::hasTable('sessions')) {
                $diagnostic['tests']['sessions_table'] = 'exists';
            } else {
                $diagnostic['tests']['sessions_table'] = 'missing';
            }
        } catch (\Exception $e) {
            $diagnostic['tests']['sessions_table'] = 'error: ' . $e->getMessage();
        }

        // Test 5: Users table
        try {
            if (Schema::hasTable('users')) {
                $userCount = DB::table('users')->count();
                $diagnostic['tests']['users_table'] = 'exists (' . $userCount . ' users)';
            } else {
                $diagnostic['tests']['users_table'] = 'missing';
            }
        } catch (\Exception $e) {
            $diagnostic['tests']['users_table'] = 'error: ' . $e->getMessage();
        }

        // Test 6: Roles table
        try {
            if (Schema::hasTable('roles')) {
                $roleCount = DB::table('roles')->count();
                $diagnostic['tests']['roles_table'] = 'exists (' . $roleCount . ' roles)';
            } else {
                $diagnostic['tests']['roles_table'] = 'missing';
            }
        } catch (\Exception $e) {
            $diagnostic['tests']['roles_table'] = 'error: ' . $e->getMessage();
        }

        // Test 7: Cache functionality
        try {
            Cache::put('test_key', 'test_value', 1);
            if (Cache::get('test_key') === 'test_value') {
                $diagnostic['tests']['cache'] = 'working';
                Cache::forget('test_key');
            } else {
                $diagnostic['tests']['cache'] = 'not working';
            }
        } catch (\Exception $e) {
            $diagnostic['tests']['cache'] = 'failed: ' . $e->getMessage();
        }

        // Test 8: File permissions
        try {
            $storageWritable = is_writable(storage_path());
            $logsWritable = is_writable(storage_path('logs'));
            $diagnostic['tests']['file_permissions'] = [
                'storage_writable' => $storageWritable,
                'logs_writable' => $logsWritable
            ];
        } catch (\Exception $e) {
            $diagnostic['tests']['file_permissions'] = 'error: ' . $e->getMessage();
        }

        // Test 9: Environment variables
        $diagnostic['tests']['env_variables'] = [
            'app_key_set' => !empty(config('app.key')),
            'app_key_length' => strlen(config('app.key')),
            'db_host' => config('database.connections.mysql.host'),
            'db_database' => config('database.connections.mysql.database'),
            'db_username' => config('database.connections.mysql.username') ? 'set' : 'not set',
            'db_password' => config('database.connections.mysql.password') ? 'set' : 'not set'
        ];

        return response()->json($diagnostic, 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Diagnostic failed: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'timestamp' => now()->toISOString()
        ], 500);
    }
})->name('emergency.diagnostic');

// Public notification status endpoint (no auth required)
Route::get('/api/notification-status', function () {
    try {
        // Count recent contact submissions and quotation requests
        $recentSubmissions = App\Models\ContactSubmission::where('created_at', '>', now()->subHours(24))->count();
        $recentQuotations = App\Models\QuotationRequest::where('created_at', '>', now()->subHours(24))->count();
        
        // Count total unread notifications in system
        $totalNotifications = DB::table('notifications')
            ->whereNull('read_at')
            ->where('created_at', '>', now()->subHours(24))
            ->count();
        
        return response()->json([
            'success' => true,
            'recent_submissions' => $recentSubmissions,
            'recent_quotations' => $recentQuotations,
            'total_notifications' => $totalNotifications,
            'timestamp' => now()->toISOString(),
            'has_activity' => ($recentSubmissions + $recentQuotations + $totalNotifications) > 0
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Unable to fetch notification status',
            'timestamp' => now()->toISOString()
        ]);
    }
})->name('api.notification.status');

// Test route for category specifications
Route::get('/test-specifications/{categoryId}', function($categoryId) {
    $service = new \App\Services\ProductSpecificationService();
    $templates = $service->getCategorySpecificationTemplates($categoryId);
    
    return response()->json([
        'category_id' => $categoryId,
        'templates' => $templates,
        'template_count' => count($templates)
    ]);
})->name('test.specifications');

// Simple test route for category specifications
Route::get('/test-specs/{categoryId}', function($categoryId) {
    try {
        $service = new \App\Services\ProductSpecificationService();
        $templates = $service->getCategorySpecificationTemplates($categoryId);
        
        return response()->json([
            'category_id' => $categoryId,
            'templates' => $templates,
            'template_count' => count($templates),
            'success' => true
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'success' => false
        ], 500);
    }
})->name('test.specs');

// Debug route for product specifications
Route::get('/debug-product-specs/{productId}', function($productId) {
    try {
        $product = \App\Models\Product::with(['specifications', 'category'])->findOrFail($productId);
        $controller = new \App\Http\Controllers\Supplier\ProductSpecificationController();
        
        // Use reflection to call private method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getCategorySpecificationTemplates');
        $method->setAccessible(true);
        $templates = $method->invoke($controller, $product->category_id);
        
        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name ?? 'Unknown'
            ],
            'existing_specs' => $product->specifications->map(function($spec) {
                return [
                    'key' => $spec->specification_key,
                    'value' => $spec->specification_value,
                    'category' => $spec->category,
                    'display_name' => $spec->display_name
                ];
            }),
            'templates' => $templates,
            'success' => true
        ], 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'success' => false
        ], 500);
    }
})->name('debug.product.specs');

// Admin API routes
Route::prefix('admin/api')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/category-specifications/{category}', [App\Http\Controllers\Admin\ProductController::class, 'getCategorySpecifications'])->name('category-specifications');
});

// Supplier Invitation Routes (Admin only)
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('supplier-invitations', \App\Http\Controllers\Admin\SupplierInvitationController::class)
        ->except(['edit', 'update']);
    Route::get('supplier-invitations/onboarding-preview', [\App\Http\Controllers\Admin\SupplierInvitationController::class, 'onboardingPreview'])->name('supplier-invitations.onboarding-preview');
    Route::post('supplier-invitations/{supplierInvitation}/resend', [\App\Http\Controllers\Admin\SupplierInvitationController::class, 'resend'])->name('supplier-invitations.resend');
    Route::match(['post', 'delete'], 'supplier-invitations/{supplierInvitation}/cancel', [\App\Http\Controllers\Admin\SupplierInvitationController::class, 'cancel'])->name('supplier-invitations.cancel');
    Route::post('supplier-invitations/bulk-action', [\App\Http\Controllers\Admin\SupplierInvitationController::class, 'bulkAction'])->name('supplier-invitations.bulk-action');
    
    // Sales Targets Routes
    Route::resource('sales-targets', \App\Http\Controllers\Admin\SalesTargetController::class);
    Route::get('sales-targets/analytics', [\App\Http\Controllers\Admin\SalesTargetController::class, 'analytics'])->name('sales-targets.analytics');
    Route::post('sales-targets/update-all-achievements', [\App\Http\Controllers\Admin\SalesTargetController::class, 'updateAllAchievedAmounts'])->name('sales-targets.update-all-achievements');
});

// Apply prevent-back middleware to auth routes
Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        // Permission Management
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->names([
            'index' => 'admin.permissions.index',
            'create' => 'admin.permissions.create',
            'store' => 'admin.permissions.store',
            'show' => 'admin.permissions.show',
            'edit' => 'admin.permissions.edit',
            'update' => 'admin.permissions.update',
            'destroy' => 'admin.permissions.destroy',
        ]);
        Route::post('permissions/generate', [\App\Http\Controllers\Admin\PermissionController::class, 'generateController'])->name('admin.permissions.generate');
        Route::post('permissions/sync', [\App\Http\Controllers\Admin\PermissionController::class, 'syncRoutes'])->name('admin.permissions.sync');

        // Inquiries
        Route::resource('inquiries', \App\Http\Controllers\Admin\InquiryController::class)->names([
            'index' => 'admin.inquiries.index',
            'create' => 'admin.inquiries.create',
            'store' => 'admin.inquiries.store',
            'show' => 'admin.inquiries.show',
            'edit' => 'admin.inquiries.edit',
            'update' => 'admin.inquiries.update',
            'destroy' => 'admin.inquiries.destroy',
        ]);
        Route::post('inquiries/{inquiry}/broadcast', [\App\Http\Controllers\Admin\InquiryController::class, 'broadcast'])->name('admin.inquiries.broadcast');
        Route::put('inquiries/{inquiry}/status', [\App\Http\Controllers\Admin\InquiryController::class, 'updateStatus'])->name('admin.inquiries.status.update');
        Route::post('inquiries/bulk-forward', [\App\Http\Controllers\Admin\InquiryController::class, 'bulkForward'])->name('admin.inquiries.bulk-forward');
        Route::post('inquiries/{inquiry}/forward', [\App\Http\Controllers\Admin\InquiryController::class, 'forwardToSupplier'])->name('admin.inquiries.forward');
        Route::post('inquiries/{inquiry}/generate-quote', [\App\Http\Controllers\Admin\InquiryController::class, 'generateQuote'])->name('admin.inquiries.generate-quote');
        Route::post('inquiries/{inquiry}/create-purchase-order', [\App\Http\Controllers\Admin\InquiryController::class, 'createPurchaseOrder'])->name('admin.inquiries.create-purchase-order');
        Route::get('inquiries/supplier-recommendations', [\App\Http\Controllers\Admin\InquiryController::class, 'getSupplierRecommendations'])->name('admin.inquiries.supplier-recommendations');
        
        // ... existing admin routes ...
    });

    // Supplier routes
    Route::prefix('supplier')->middleware('supplier')->group(function () {
        // Inquiries
        Route::get('inquiries', [App\Http\Controllers\Supplier\InquiryController::class, 'index'])->name('supplier.inquiries.index');
        Route::get('inquiries/{inquiry}', [App\Http\Controllers\Supplier\InquiryController::class, 'show'])->name('supplier.inquiries.show');
        Route::patch('inquiries/{inquiry}/status', [App\Http\Controllers\Supplier\InquiryController::class, 'updateStatus'])->name('supplier.inquiries.status.update');
        Route::post('inquiries/{inquiry}/not-available', [App\Http\Controllers\Supplier\InquiryController::class, 'respondNotAvailable'])->name('supplier.inquiries.not-available');
        Route::get('inquiries/{inquiry}/quotation', [App\Http\Controllers\Supplier\InquiryController::class, 'quotationForm'])->name('supplier.inquiries.quotation');
        Route::post('inquiries/{inquiry}/quotation', [App\Http\Controllers\Supplier\InquiryController::class, 'store'])->name('supplier.inquiries.quotation.store');
        
        // ... existing supplier routes ...
    });

    // Order and payment routes
    Route::prefix('orders')->group(function () {
        // ... existing order routes ...
    });
});

// Apply to specific form submission routes
Route::middleware([\App\Http\Middleware\PreventBackHistory::class])->group(function () {
    // Route::post('/contact', [ContactController::class, 'store'])->name('contact.store'); // Removed duplicate route
    Route::post('/quote', [\App\Http\Controllers\Admin\QuoteController::class, 'store'])->name('quote.store');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// Test route for supplier middleware
Route::get('/test-supplier-middleware', function () {
    $user = \Auth::user();
    if (!$user) {
        return 'Not authenticated';
    }
    
    return [
        'user_id' => $user->id,
        'email' => $user->email,
        'is_supplier' => $user->isSupplier(),
        'role_name' => $user->role ? $user->role->name : 'no role',
        'auth_check' => \Auth::check(),
        'can_access_onboarding' => $user->isSupplier()
    ];
})->middleware(['auth', \App\Http\Middleware\SupplierMiddleware::class])->name('test.supplier.middleware');

require __DIR__ . '/auth.php';

// Temporary debug route for customer address issue
Route::get('/debug/customer-address/{customerId}', function($customerId) {
    $customer = \App\Models\Customer::find($customerId);
    
    if (!$customer) {
        return response()->json(['error' => 'Customer not found']);
    }
    
    $debug = [
        'customer_id' => $customer->id,
        'customer_name' => $customer->name,
        'raw_fields' => [
            'billing_street' => $customer->billing_street,
            'billing_city' => $customer->billing_city,
            'billing_state' => $customer->billing_state,
            'billing_zip' => $customer->billing_zip,
            'billing_country' => $customer->billing_country,
            'shipping_street' => $customer->shipping_street,
            'shipping_city' => $customer->shipping_city,
            'shipping_state' => $customer->shipping_state,
            'shipping_zip' => $customer->shipping_zip,
            'shipping_country' => $customer->shipping_country,
        ],
        'computed_addresses' => [
            'billing_address' => $customer->billing_address,
            'shipping_address' => $customer->shipping_address,
        ]
    ];
    
    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
})->name('debug.customer.address');

// Debug route to test quote conversion for specific quote
Route::get('/debug/quote-conversion/{quoteId}', function($quoteId) {
    $quote = \App\Models\Quote::find($quoteId);
    
    if (!$quote) {
        return response()->json(['error' => 'Quote not found']);
    }
    
    $customer = \App\Models\Customer::where('name', $quote->customer_name)->first();
    
    $debug = [
        'quote_id' => $quote->id,
        'quote_number' => $quote->quote_number,
        'customer_name' => $quote->customer_name,
        'customer_found' => $customer ? true : false,
    ];
    
    if ($customer) {
        $debug['customer_debug'] = [
            'customer_id' => $customer->id,
            'raw_billing_fields' => [
                'billing_street' => $customer->billing_street,
                'billing_city' => $customer->billing_city,
                'billing_state' => $customer->billing_state,
                'billing_zip' => $customer->billing_zip,
                'billing_country' => $customer->billing_country,
            ],
            'raw_shipping_fields' => [
                'shipping_street' => $customer->shipping_street,
                'shipping_city' => $customer->shipping_city,
                'shipping_state' => $customer->shipping_state,
                'shipping_zip' => $customer->shipping_zip,
                'shipping_country' => $customer->shipping_country,
            ],
            'computed_addresses' => [
                'billing_address' => $customer->billing_address,
                'shipping_address' => $customer->shipping_address,
            ]
        ];
    }
    
    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
})->name('debug.quote.conversion');

// Comprehensive redirects for canonical URL issues from Google Search Console CSV
// These redirects ensure proper canonical URLs and prevent "Alternate page with proper canonical tag" errors

// Redirect www to non-www (handled by middleware, but adding explicit redirects for problematic URLs)
// REMOVED: This route was causing a circular redirect and overriding the main product.show route
// Route::get('/products/{product:slug}', function($product) {
//     return redirect()->route('product.show', $product->slug, 301);
// })->where('product', '.*')->middleware('canonical.domain');

// Redirect old product URLs with numeric IDs to slug-based URLs
$productRedirectIds = [92, 117, 186, 224, 314, 43, 64, 276, 226, 219, 304, 230, 307, 309, 303, 280, 229, 85, 103, 37, 379, 359, 331, 279, 40, 274, 312, 305, 311, 257, 399, 396, 347, 389, 69, 363, 283, 420, 60, 249, 62, 95, 200, 422];

foreach ($productRedirectIds as $productId) {
    Route::get("/product/{$productId}", function($productId) {
        $product = \App\Models\Product::find($productId);
        if ($product && $product->slug) {
            return redirect()->route('product.show', $product->slug, 301);
        }
        return redirect('/products', 301);
    })->where('productId', '[0-9]+');
}

// Redirect problematic category URLs with query parameters
Route::get('/categories/{category}/{subcategory}?page={page}', function($category, $subcategory, $page = null) {
    $categoryModel = \App\Models\Category::where('slug', $category)->first();
    if ($categoryModel) {
        $url = route('categories.show', $categoryModel);
        if ($page && $page > 1) {
            $url .= "?page={$page}";
        }
        return redirect($url, 301);
    }
    return redirect('/categories', 301);
})->where(['category' => '[a-z0-9\-]+', 'subcategory' => '[a-z0-9\-]+', 'page' => '[0-9]+']);

// Redirect specific problematic category combinations from CSV
$categoryRedirects = [
    '51/39/82' => 'research-life-sciences/analytical-instruments/laboratory-equipment',
    '82' => 'laboratory-equipment',
    '75' => 'molecular-biology',
    '66/68/88' => 'analytical-chemistry/laboratory-equipment/centrifuges',
    '68/61' => 'laboratory-equipment/pcr-thermocyclers',
    '39/82' => 'analytical-instruments/laboratory-equipment',
    '81' => 'rapid-test-kits',
    '84' => 'thermal-process-equipment'
];

foreach ($categoryRedirects as $oldPath => $newPath) {
    Route::get("/categories/{$oldPath}", function() use ($newPath) {
        return redirect("/categories/{$newPath}", 301);
    });
    
    // Handle pagination
    Route::get("/categories/{$oldPath}?page={page}", function($page) use ($newPath) {
        return redirect("/categories/{$newPath}?page={$page}", 301);
    })->where('page', '[0-9]+');
}

// Redirect product query URLs to proper category pages
Route::get('/products?category={category}', function($category) {
    $categoryMap = [
        'plasticware' => 'laboratory-consumables',
        'rapid-tests' => 'rapid-test-kits',
        'consumables' => 'laboratory-consumables',
        'MaxTest© Rapid Tests IVD' => 'rapid-test-kits',
        'microbiology' => 'microbiology-equipment'
    ];
    
    $newCategory = $categoryMap[$category] ?? 'laboratory-equipment';
    return redirect("/categories/{$newCategory}", 301);
})->where('category', '.*');

// Redirect quotation URLs to ensure proper canonical structure
// REMOVED: These routes were causing circular redirects and overriding the main quotation routes
// Route::get('/quotation/{product:slug}', function($product) {
//     return redirect()->route('quotation.redirect', $product->slug, 301);
// })->where('product', '.*');

// Route::get('/quotation/{product:slug}/form', function($product) {
//     return redirect()->route('quotation.form', $product->slug, 301);
// })->where('product', '.*');

Route::get('/quotation/{product:slug}/form', function($product) {
    if (is_string($product)) {
        // If $product is a string (slug), find the actual product
        $productModel = \App\Models\Product::where('slug', $product)->first();
        if ($productModel) {
            return redirect()->route('quotation.form', $productModel->slug, 301);
        }
        return redirect('/quotation/form', 301);
    }
    return redirect()->route('quotation.form', $product->slug, 301);
})->where('product', '.*');

// Redirect legacy URLs with special characters
Route::get('/education%26training-tools', function() {
    return redirect('/categories/education-training-tools', 301);
});

Route::get('/analytical-chemistry', function() {
    return redirect('/categories/analytical-chemistry', 301);
});

Route::get('/genomics-%26-life-sciences', function() {
    return redirect('/categories/genomics-life-sciences', 301);
});

Route::get('/veterinary-%26-agri-tools', function() {
    return redirect('/categories/veterinary-agri-tools', 301);
});

Route::get('/forensic-supplies', function() {
    return redirect('/categories/forensic-supplies', 301);
});

Route::get('/molecular-biology', function() {
    return redirect('/categories/molecular-biology', 301);
});

Route::get('/research-%26-life-sciences', function() {
    return redirect('/categories/research-life-sciences', 301);
});

// Test Supplier Invitation Route
Route::get('/test-supplier-invitation', function () {
    try {
        // Get a sample invitation
        $invitation = \App\Models\SupplierInvitation::first();
        
        if (!$invitation) {
            return 'No supplier invitations found in database. Please create one from the admin panel first.';
        }
        
        $invitationUrl = route('supplier.invitation.onboarding', ['token' => $invitation->token]);
        
        return "
            <h3>Supplier Invitation Test</h3>
            <p><strong>Found invitation for:</strong> {$invitation->email}</p>
            <p><strong>Company:</strong> {$invitation->company_name}</p>
            <p><strong>Status:</strong> {$invitation->status}</p>
            <p><strong>Invitation URL:</strong></p>
            <p><a href='{$invitationUrl}' target='_blank'>{$invitationUrl}</a></p>
            <p><strong>Test:</strong> Click the link above to test the invitation flow</p>
        ";
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->name('test.supplier.invitation');

// Debug route for authentication issues
Route::get('/debug-auth', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'debug'])
    ->middleware('auth')
    ->name('debug.auth');

// Test logging route for production
Route::get('/test-logging', function () {
    $output = "🔍 Logging Test Results\n\n";
    
    try {
        // Get current logging configuration
        $logChannel = config('logging.default');
        $logPath = storage_path('logs/laravel.log');
        
        $output .= "Current Log Channel: {$logChannel}\n";
        $output .= "Log Path: {$logPath}\n";
        $output .= "Environment: " . app()->environment() . "\n";
        $output .= "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
        $output .= "LOG_LEVEL: " . (env('LOG_LEVEL') ?? 'not set') . "\n";
        $output .= "LOG_CHANNEL: " . (env('LOG_CHANNEL') ?? 'not set') . "\n\n";
        
        // Check file permissions
        $logDir = dirname($logPath);
        $output .= "Log Directory: {$logDir}\n";
        $output .= "Directory Exists: " . (is_dir($logDir) ? 'Yes' : 'No') . "\n";
        $output .= "Directory Writable: " . (is_writable($logDir) ? 'Yes' : 'No') . "\n";
        
        if (file_exists($logPath)) {
            $output .= "Log File Exists: Yes\n";
            $output .= "Log File Writable: " . (is_writable($logPath) ? 'Yes' : 'No') . "\n";
            $output .= "Log File Size: " . number_format(filesize($logPath)) . " bytes\n";
        } else {
            $output .= "Log File Exists: No\n";
        }
        
        $output .= "\n";
        
        // Test logging at different levels
        $testId = uniqid();
        $testMessage = "Test logging entry {$testId}";
        
        $output .= "Testing logging with ID: {$testId}\n\n";
        
        Log::emergency($testMessage . ' [EMERGENCY]');
        Log::alert($testMessage . ' [ALERT]');
        Log::critical($testMessage . ' [CRITICAL]');
        Log::error($testMessage . ' [ERROR]');
        Log::warning($testMessage . ' [WARNING]');
        Log::notice($testMessage . ' [NOTICE]');
        Log::info($testMessage . ' [INFO]');
        Log::debug($testMessage . ' [DEBUG]');
        
        $output .= "Log entries written. Checking if they appear in log file...\n\n";
        
        // Check if logs were written
        if (file_exists($logPath)) {
            $logContent = file_get_contents($logPath);
            $matchCount = substr_count($logContent, $testId);
            
            if ($matchCount > 0) {
                $output .= "✅ SUCCESS: Found {$matchCount} log entries in the file\n";
                
                // Show last few lines of the log
                $lines = explode("\n", $logContent);
                $lastLines = array_slice($lines, -10);
                $output .= "\nLast 10 lines of log file:\n";
                $output .= "================================\n";
                foreach ($lastLines as $line) {
                    if (!empty(trim($line))) {
                        $output .= $line . "\n";
                    }
                }
            } else {
                $output .= "❌ FAILED: No test entries found in log file\n";
                $output .= "This means logging is not working properly.\n";
            }
        } else {
            $output .= "❌ FAILED: Log file was not created\n";
        }
        
        // Try alternative log locations
        $output .= "\nChecking alternative log locations:\n";
        $altPaths = [
            '/var/log/laravel.log',
            '/var/log/nginx/error.log',
            '/var/log/apache2/error.log',
            storage_path('logs/production-debug.log'),
            storage_path('logs/laravel-' . date('Y-m-d') . '.log')
        ];
        
        foreach ($altPaths as $altPath) {
            if (file_exists($altPath)) {
                $output .= "✅ Found: {$altPath} (" . number_format(filesize($altPath)) . " bytes)\n";
            } else {
                $output .= "❌ Not found: {$altPath}\n";
            }
        }
        
    } catch (\Exception $e) {
        $output .= "❌ ERROR: " . $e->getMessage() . "\n";
        $output .= "File: " . $e->getFile() . "\n";
        $output .= "Line: " . $e->getLine() . "\n";
    }
    
    return response("<pre>{$output}</pre>", 200, ['Content-Type' => 'text/html']);
})->name('test.logging');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Additional authenticated routes can be added here
});

// Performance monitoring routes (admin only)
Route::middleware(['auth', 'admin'])->prefix('admin/performance')->group(function () {
    Route::get('/metrics', [\App\Http\Controllers\PerformanceController::class, 'metrics'])->name('admin.performance.metrics');
    Route::post('/clear-cache', [\App\Http\Controllers\PerformanceController::class, 'clearCache'])->name('admin.performance.clear-cache');
    Route::get('/recommendations', [\App\Http\Controllers\PerformanceController::class, 'recommendations'])->name('admin.performance.recommendations');
});
