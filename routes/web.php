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

    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('b2b-details-form', [App\Http\Controllers\HomeController::class, 'b2b_details_form']);


    /* Sales Officer */
    Route::resource('product-management', App\Http\Controllers\Superadmin\ProductManagementController::class);
    Route::resource('user-management', App\Http\Controllers\Superadmin\UserManagementController::class);
    Route::get('inventory-management', [App\Http\Controllers\Superadmin\InventoryManagementController::class, 'index'])->name('inventory');
    Route::post('inventory-management', [App\Http\Controllers\Superadmin\InventoryManagementController::class, 'store'])->name('inventory.store');

    Route::resource('b2b-creation', App\Http\Controllers\Superadmin\B2BController::class);
    Route::resource('deliveryrider-creation', App\Http\Controllers\Superadmin\DeliveryRiderController::class);
    Route::resource('salesofficer-creation', App\Http\Controllers\Superadmin\SalesOfficerController::class);

    Route::get('user-report', [App\Http\Controllers\Superadmin\ReportController::class, 'user_report'])->name('user.report');
    Route::get('delivery-report', [App\Http\Controllers\Superadmin\ReportController::class, 'delivery_report'])->name('delivery.report');
    Route::get('inventory-report', [App\Http\Controllers\Superadmin\ReportController::class, 'inventory_report'])->name('inventory.report');

    Route::get('submitted_po', [App\Http\Controllers\Superadmin\TrackingController::class, 'submitted_po'])->name('tracking.submitted-po');
    Route::get('/purchase-requests/{id}', [App\Http\Controllers\Superadmin\TrackingController::class, 'show']);
    Route::put('/process-so/{id}', [App\Http\Controllers\Superadmin\TrackingController::class, 'process_so']);
    Route::get('/delivery-personnel', [App\Http\Controllers\Superadmin\TrackingController::class, 'delivery_personnel'])->name('tracking.delivery-personnel');
    Route::post('/assign-delivery-personnel', [App\Http\Controllers\Superadmin\TrackingController::class, 'assign_delivery_personnel'])->name('tracking.assign-delivery-personnel');

    /* Sales Officer */
    Route::prefix('salesofficer')->name('salesofficer.')->group(function () {
        Route::get('/purchase-requests/all', [App\Http\Controllers\SalesOfficer\PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
        Route::get('/purchase-requests/{id}', [App\Http\Controllers\SalesOfficer\PurchaseRequestController::class, 'show']);
        Route::put('/purchase-requests/send-quotation/{id}', [App\Http\Controllers\SalesOfficer\PurchaseRequestController::class, 'purchase-requests.update']);
        
        Route::get('/send-quotations/all', [App\Http\Controllers\SalesOfficer\QuotationsController::class, 'index'])->name('send-quotations.index');

        Route::get('/submitted-order/all', [App\Http\Controllers\SalesOfficer\OrderController::class, 'index'])->name('submitted-order.index');
    });

    /* B2B */
    Route::prefix('b2b')->name('b2b.')->group(function () {
        Route::get('/purchase-requests', [App\Http\Controllers\B2B\PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
        Route::post('/purchase-requests/store', [App\Http\Controllers\B2B\PurchaseRequestController::class, 'store'])->name('purchase-requests.store');
        Route::delete('/purchase-requests/items/{id}', [App\Http\Controllers\B2B\PurchaseRequestController::class, 'destroyItem'])->name('purchase-requests.destroyItem');
        
        Route::get('/quotations/review', [App\Http\Controllers\B2B\QuotationController::class, 'review'])->name('quotations.review');
        Route::get('/quotations/review/{id}', [App\Http\Controllers\B2B\QuotationController::class, 'show'])->name('quotations.show');
        Route::post('/quotations/submit/{id}', [App\Http\Controllers\B2B\QuotationController::class, 'submit_quotation'])->name('quotations.submit');
        Route::get('/quotations/status/{id}', [App\Http\Controllers\B2B\QuotationController::class, 'checkStatus']);

        Route::resource('address', 'App\Http\Controllers\B2B\B2BAddressController');
        Route::get('/geocode', [App\Http\Controllers\B2B\B2BAddressController::class, 'geocode']);
        Route::post('/address/set-default', [App\Http\Controllers\B2B\B2BAddressController::class, 'setDefault']);

        Route::get('/delivery', [App\Http\Controllers\B2B\DeliveryController::class, 'index'])->name('delivery.index');

        Route::get('/profile', [App\Http\Controllers\B2B\B2BController::class, 'index'])->name('profile.index');
    });


    //General Setting
    Route::get('generalsettings', [App\Http\Controllers\GeneralSettingsController::class, 'index'])->name('generalsettings');
    Route::post('generalsettings-company', [App\Http\Controllers\GeneralSettingsController::class, 'company']);
    Route::post('generalsettings-profile', [App\Http\Controllers\GeneralSettingsController::class, 'profile']);
    Route::post('generalsettings-account', [App\Http\Controllers\GeneralSettingsController::class, 'account']);
    Route::post('generalsettings-password', [App\Http\Controllers\GeneralSettingsController::class, 'password']);
});
