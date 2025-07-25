<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');
Route::get('/products/{id}', [App\Http\Controllers\WelcomeController::class, 'show']);

// routes/web.php
Route::post('/login/ajax', [App\Http\Controllers\Auth\LoginController::class, 'ajaxLogin']);

Auth::routes(['verify' => true]);

Route::get('/secure-js-file/{filename}', [App\Http\Controllers\SecureController::class, 'serveJsFile'])->name('secure.js');
Route::post('/verify/code',  [App\Http\Controllers\Auth\VerificationController::class, 'otp_verify']);

Route::get('/google/redirect', [App\Http\Controllers\Auth\GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [App\Http\Controllers\Auth\GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware(['prevent-back-history', 'auth', 'verified'])->group(function () {

    /* Super Admin */

    // Management
    Route::resource('product-management', App\Http\Controllers\Superadmin\ProductManagementController::class);
    Route::resource('category-management', App\Http\Controllers\Superadmin\CategoryController::class);
    Route::resource('user-management', App\Http\Controllers\Superadmin\UserManagementController::class);
    Route::get('inventory-management', [App\Http\Controllers\Superadmin\InventoryManagementController::class, 'index'])->name('inventory');
    Route::post('inventory-management', [App\Http\Controllers\Superadmin\InventoryManagementController::class, 'store'])->name('inventory.store');
    Route::resource('bank-management', App\Http\Controllers\Superadmin\BankManagementController::class);

    // Account Creation
    Route::resource('b2b-creation', App\Http\Controllers\Superadmin\B2BController::class);
    Route::resource('deliveryrider-creation', App\Http\Controllers\Superadmin\DeliveryRiderController::class);
    Route::resource('salesofficer-creation', App\Http\Controllers\Superadmin\SalesOfficerController::class);

    // Report
    Route::get('user-report', [App\Http\Controllers\Superadmin\ReportController::class, 'userReport'])->name('user.report');
    Route::get('delivery-report', [App\Http\Controllers\Superadmin\ReportController::class, 'deliveryReport'])->name('delivery.report');
    Route::get('inventory-report', [App\Http\Controllers\Superadmin\ReportController::class, 'inventoryReport'])->name('inventory.report');

    // Tracking
    Route::get('submitted_po', [App\Http\Controllers\Superadmin\TrackingController::class, 'submittedPO'])->name('tracking.submitted-po');
    Route::get('/purchase-requests/{id}', [App\Http\Controllers\Superadmin\TrackingController::class, 'show']);
    Route::put('/process-so/{id}', [App\Http\Controllers\Superadmin\TrackingController::class, 'processSO']);
    Route::get('/delivery/location', [App\Http\Controllers\Superadmin\TrackingController::class, 'deliveryLocation'])->name('tracking.delivery.location');
    Route::get('/delivery/tracking/{id}', [App\Http\Controllers\Superadmin\TrackingController::class, 'deliveryTracking'])->name('tracking.delivery.tracking');
    Route::post('/delivery/upload-proof', [App\Http\Controllers\Superadmin\TrackingController::class, 'uploadProof'])->name('tracking.delivery.upload-proof');
    Route::get('/delivery-personnel', [App\Http\Controllers\Superadmin\TrackingController::class, 'deliveryPersonnel'])->name('tracking.delivery-personnel');
    Route::post('/assign-delivery-personnel', [App\Http\Controllers\Superadmin\TrackingController::class, 'assignDeliveryPersonnel'])->name('tracking.assign-delivery-personnel');
    Route::get('/b2b/requirements', [App\Http\Controllers\Superadmin\TrackingController::class, 'b2bRequirements'])->name('tracking.b2b.requirement');
    Route::post('/b2b/requirement/update-status', [App\Http\Controllers\Superadmin\TrackingController::class, 'updateStatus']);

    /* Sales Officer */
    Route::prefix('salesofficer')->name('salesofficer.')->group(function () {
        Route::get('/purchase-requests/all', [App\Http\Controllers\SalesOfficer\PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
        Route::get('/purchase-requests/{id}', [App\Http\Controllers\SalesOfficer\PurchaseRequestController::class, 'show']);
        Route::put('/purchase-requests/s-q/{id}', [App\Http\Controllers\SalesOfficer\PurchaseRequestController::class, 'updateSendQuotation']);
        Route::get('/send-quotations/all', [App\Http\Controllers\SalesOfficer\QuotationsController::class, 'index'])->name('send-quotations.index');
        Route::get('/submitted-order/all', [App\Http\Controllers\SalesOfficer\OrderController::class, 'index'])->name('submitted-order.index');
    });

    /* Delivery */
    Route::prefix('deliveryrider')->name('deliveryrider.')->group(function () {
        Route::put('/delivery/pickup/{id}', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'deliveryPickup']);
        Route::get('/delivery/location', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'deliveryLocation'])->name('delivery.location');
        Route::get('/delivery/tracking/{id}', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'deliveryTracking'])->name('delivery.tracking');
        Route::get('/delivery/orders', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'deliveryOrders'])->name('delivery.orders');
        Route::get('/delivery/orders/{id}/items', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'getOrderItems'])->name('delivery.orderItems');
        Route::get('/delivery/histories', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'deliveryHistories'])->name('delivery.histories');
        Route::get('/delivery/history/{order}', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'getDeliveryDetails']);
        Route::post('/delivery/upload-proof', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'uploadProof'])->name('delivery.upload-proof');
        Route::post('/delivery/cancel/{id}', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'cancelDelivery'])->name('delivery.cancel');
        Route::get('/delivery/ratings', [App\Http\Controllers\DeliveryRider\DeliveryController::class, 'deliveryRatings'])->name('delivery.ratings');
    });

    /* B2B */
    Route::prefix('b2b')->name('b2b.')->group(function () {
       
        Route::post('/business/requirement', [App\Http\Controllers\B2B\B2BController::class, 'business_requirement'])->name('business.requirement');

        Route::get('/profile', [App\Http\Controllers\B2B\B2BController::class, 'index'])->name('profile.index');
        Route::put('/profile/update', [App\Http\Controllers\B2B\B2BController::class, 'update'])->name('profile.update');
        Route::post('/profile/upload', [App\Http\Controllers\B2B\B2BController::class, 'upload'])->name('profile.upload');

        Route::get('/purchase-requests', [App\Http\Controllers\B2B\PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
        Route::post('/purchase-requests/store', [App\Http\Controllers\B2B\PurchaseRequestController::class, 'store'])->name('purchase-requests.store');
        Route::delete('/purchase-requests/items/{id}', [App\Http\Controllers\B2B\PurchaseRequestController::class, 'destroyItem'])->name('purchase-requests.destroyItem');

        Route::get('/quotations/review', [App\Http\Controllers\B2B\QuotationController::class, 'review'])->name('quotations.review');
        Route::get('/quotations/review/{id}', [App\Http\Controllers\B2B\QuotationController::class, 'show'])->name('quotations.show');
        Route::post('/quotations/cancel/{id}', [App\Http\Controllers\B2B\QuotationController::class, 'cancelQuotation'])->name('quotations.cancel');
       
        Route::post('/quotations/payment/paylater', [App\Http\Controllers\B2B\QuotationController::class, 'payLater'])->name('quotations.payment.paylater');
        Route::post('/quotations/payment/upload', [App\Http\Controllers\B2B\QuotationController::class, 'uploadPaymentProof'])->name('quotations.payment.upload');
        Route::get('/quotations/status/{id}', [App\Http\Controllers\B2B\QuotationController::class, 'checkStatus']);

        Route::resource('address', App\Http\Controllers\B2B\B2BAddressController::class);
        Route::get('/geocode', [App\Http\Controllers\B2B\B2BAddressController::class, 'geoCode']);
        Route::post('/address/set-default', [App\Http\Controllers\B2B\B2BAddressController::class, 'setDefault']);

        Route::get('/delivery', [App\Http\Controllers\B2B\DeliveryController::class, 'index'])->name('delivery.index');
        Route::get('/delivery/track/{id}', [App\Http\Controllers\B2B\DeliveryController::class, 'track_delivery'])->name('delivery.track.index');
        Route::get('/delivery/invoice/{id}', [App\Http\Controllers\B2B\DeliveryController::class, 'view_invoice'])->name('delivery.invoice');
        Route::get('/delivery/invoice/download/{id}', [App\Http\Controllers\B2B\DeliveryController::class, 'downloadInvoice'])->name('delivery.invoice.download');
        Route::get('/delivery/rider/rate/{id}', [App\Http\Controllers\B2B\DeliveryController::class, 'rate_page'])->name('delivery.rider.rate');
        Route::post('/delivery/rider/rate/{id}', [App\Http\Controllers\B2B\DeliveryController::class, 'save_rating'])->name('delivery.rider.rate.submit');

        Route::get('/purchase', [App\Http\Controllers\B2B\PurchaseController::class, 'index'])->name('purchase.index');
        Route::post('/purchase/return', [App\Http\Controllers\B2B\PurchaseController::class, 'requestReturn']);
        Route::post('/purchase/refund', [App\Http\Controllers\B2B\PurchaseController::class, 'requestRefund']);
        Route::get('/purchase/return-refund/data', [App\Http\Controllers\B2B\PurchaseController::class, 'purchaseReturnRefund'])->name('purchase.rr');

        Route::get('/purchase/credit', [App\Http\Controllers\B2B\CreditController::class, 'index'])->name('purchase.credit');
        Route::post('/purchase/credit/payment', [App\Http\Controllers\B2B\CreditController::class, 'credit_payment'])->name('purchase.credit.payment');
    });


    //Chat
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/users', [App\Http\Controllers\ChatController::class, 'getUsers']);
    Route::get('/chat/messages/{recipientId}', [App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::get('/recent-messages', [App\Http\Controllers\ChatController::class, 'recentMessage']);

    //Notification
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notification.index');
    Route::get('/notifications/api', [App\Http\Controllers\NotificationController::class, 'notificationsApi']);
    Route::post('/notifications/mark-all-selected', [App\Http\Controllers\NotificationController::class, 'markSelectedAsRead']);

    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/api/sales-revenue-data', [App\Http\Controllers\HomeController::class, 'salesRevenueData']);
    Route::get('/api/monthly-top-products', [App\Http\Controllers\HomeController::class, 'monthlyTopPurchasedProducts']);
    Route::get('/api/inventory-pie-data', [App\Http\Controllers\HomeController::class, 'inventoryPieData']);
    Route::post('b2b-details-form', [App\Http\Controllers\HomeController::class, 'b2b_details_form']);
    Route::resource('terms', App\Http\Controllers\TermsController::class);

    Route::get('company/settings', [App\Http\Controllers\SettingsController::class, 'company'])->name('company.settings');
    Route::post('/company/update', [App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('company.update');
    Route::get('profile/settings', [App\Http\Controllers\SettingsController::class, 'profile'])->name('profile.settings');
    Route::get('user-profile/settings', [App\Http\Controllers\SettingsController::class, 'getUserProfile']);
    Route::post('/profile/update', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('profile.update');

    // Route::post('generalsettings-company', [App\Http\Controllers\GeneralSettingsController::class, 'company']);
    // Route::post('generalsettings-profile', [App\Http\Controllers\GeneralSettingsController::class, 'profile']);
    // Route::post('generalsettings-account', [App\Http\Controllers\GeneralSettingsController::class, 'account']);
    // Route::post('generalsettings-password', [App\Http\Controllers\GeneralSettingsController::class, 'password']);
});
