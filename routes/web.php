<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\SummerNoteController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // profile
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
    // password update
    Route::put('profile-password-update', [ProfileController::class, 'passwordUpdate'])->name('user.password.update');
    Route::post('/update-profile', [ProfileController::class, 'profileUpdate'])->name('profile.update');

    // 2FA
    Route::prefix('profile/two-factor')->group(function () {
        Route::get('/', [TwoFactorController::class, 'index'])->name('two-factor.index');
        Route::get('/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
        Route::get('/backup-codes', [TwoFactorController::class, 'backupCodes'])->name('two-factor.backup-codes');
        Route::post('/backup-codes/regenerate', [TwoFactorController::class, 'regenerateBackupCodes'])->name('two-factor.regenerate-backup-codes');
    });

    // settings
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::put('setting', [SettingController::class, 'update'])->name('setting.update');
    Route::post('setting/delete-media', [SettingController::class, 'deleteMedia'])->name('setting.delete-media');
    Route::post('setting/reorder-banners', [SettingController::class, 'reorderBanners'])->name('setting.reorder-banners');

    // tickets
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/export', [TicketController::class, 'export'])->name('tickets.export');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::put('tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::post('tickets/{ticket}/check-in', [TicketController::class, 'checkIn'])->name('tickets.check-in');
    Route::get('tickets/{ticket}/download-pdf', [TicketController::class, 'downloadPdf'])->name('tickets.download-pdf');
    Route::post('tickets/{ticket}/resend-email', [TicketController::class, 'resendEmail'])->name('tickets.resend-email');

    // coupons
    Route::resource('coupon', CouponController::class);

    // category
    Route::resource('category', CategoryController::class);

    // Tax rate
    Route::resource('tax', TaxRateController::class);

    // events
    Route::resource('event', EventController::class);
    Route::get('event-trash', [EventController::class, 'trashView'])->name('event.trash');
    Route::get('event-restore/{id}', [EventController::class, 'restore'])->name('event.restore');
    // deleted permanently
    Route::delete('event-delete/{id}', [EventController::class, 'force_delete'])->name('event.force.delete');
    // bulk delete
    Route::post('/event-bulk-delete', [EventController::class, 'bulkDelete'])->name('event.bulk-delete');
    // bulk trashed delete
    Route::post('/event-trash-bulk-delete', [EventController::class, 'trashBulkDelete'])->name('event.trash.bulk-delete');

    Route::post('summernote', [SummerNoteController::class, 'summerUpload'])->name('summer.upload.image');
    Route::post('summernote/delete', [SummerNoteController::class, 'summerDelete'])->name('summer.delete.image');

    // roles
    Route::resource('roles', RoleController::class)->only(['index', 'edit', 'update']);

    // users
    Route::resource('users', UserController::class);

    // orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

});

// 2FA verify login (no auth middleware - user hasn't fully logged in yet)
Route::match(['get', 'post'], 'admin/profile/two-factor/verify', [TwoFactorController::class, 'verifyLogin'])->name('two-factor.verify-login');
Route::match(['get', 'post'], 'admin/profile/two-factor/verify/recovery', [TwoFactorController::class, 'verifyLoginRecovery'])->name('two-factor.verify-recovery');

// frontend
Route::get('/', [FrontController::class, 'index'])->name('home');
// event details
Route::get('event/{slug}', [FrontController::class, 'eventDetail'])->name('event.detail');

// events list
Route::get('events', [FrontController::class, 'events'])->name('events');

// category filter
Route::get('category/{slug}', [FrontController::class, 'categoryEvents'])->name('category.events');

// contact
Route::get('contact', [FrontController::class, 'contact'])->name('contact');


// booking page
Route::get('booking/{slug}', [FrontController::class, 'booking'])->name('booking');

// Session-based ticket management
Route::post('/events/{event:slug}/tickets/add', [BookingController::class, 'addTicket'])->name('booking.tickets.add');
Route::post('/events/{event:slug}/tickets/remove', [BookingController::class, 'removeTicket'])->name('booking.tickets.remove');
Route::post('/events/{event:slug}/tickets/apply', [BookingController::class, 'applyTickets'])->name('booking.tickets.apply');
Route::post('/events/{event:slug}/tickets/clear', [BookingController::class, 'clearTickets'])->name('booking.tickets.clear');

Route::post('/events/{event:slug}/book', [BookingController::class, 'process'])->name('booking.process');
Route::get('checkout', [BookingController::class, 'show'])->name('checkout.show');
Route::post('checkout/save-billing', [BookingController::class, 'saveBilling'])->name('checkout.save-billing');

// Coupon routes
Route::post('coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
Route::post('coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

Route::get('get-states/{country_id}', [App\Http\Controllers\LocationController::class, 'getStates'])->name('get.states');
Route::get('get-cities/{state_id}', [App\Http\Controllers\LocationController::class, 'getCities'])->name('get.cities');

// Payment routes
Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
Route::post('payment/process', [PaymentController::class, 'process'])->name('payment.process');

Route::post('payment/razorpay/init', [PaymentController::class, 'initRazorpay'])->name('payment.razorpay.init');

Route::post('payment/stripe/init', [PaymentController::class, 'initStripePayment'])->name('payment.stripe.init');
Route::get('payment/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('payment.stripe.success');
Route::get('payment/stripe/cancel', [PaymentController::class, 'stripeCancel'])->name('payment.stripe.cancel');

Route::get('payment/success/{order}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('payment/failure', [PaymentController::class, 'failure'])->name('payment.failure');
