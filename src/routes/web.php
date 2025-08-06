<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\{
    DashboardController,
    DepotController,
    BookingTypeController,
    SlotController,
    BookingController,
    SettingsController,
    SlotTemplateController,
    BookingRulesController,
    SlotCapacityController,
    SlotUsageController,
    SlotGenerationSettingController,
    SlotGeneratorController,
    AdminSettingsController,
    DepotProductController,
    UserSwitchController,
    ProductController,
    CustomerDepotProductController,
    DepotCaseRangeController,
    CustomerController,
    AdminController,
    SlotReleaseRuleController,
};
use App\Http\Controllers\Customer\{
    CustomerDashboardController,
    CustomerBookingController
};
use App\Http\Controllers\DepotAdmin\{
    DepotAdminDashboardController
};
use App\Http\Controllers\SiteAdmin\{
    SiteAdminDashboardController
};

require __DIR__.'/auth.php';

Route::get('/redirect-after-login', function () {
    $user = Auth::user();

    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('depot-admin')) return redirect()->route('depot.dashboard');
    if ($user->hasRole('site-admin')) return redirect()->route('site.dashboard');
    if ($user->hasRole('customer')) return redirect()->route('customer.bookings.index');

    return redirect()->route('dashboard');
});

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

//Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::get('/dashboard', fn() => redirect()->route('admin.dashboard'))->name('dashboard');

    // Universal switch-back route (accessible to all authenticated users during testing)
    Route::post('switch-back', [UserSwitchController::class, 'switchBack'])->name('switch-back');
    
    // Emergency recovery route (GET request for direct URL access)
    Route::get('emergency-switch-back', [UserSwitchController::class, 'switchBack'])->name('emergency-switch-back');
    
    // Recovery page for locked-out users
    Route::get('recovery', function() { return view('recovery'); })->name('recovery');

    /**
     * ───── Admin Routes ─────
     */
    Route::prefix('admin')->as('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resources([
            'depots' => DepotController::class,
            'booking-types' => BookingTypeController::class,           
            'slot-templates' => SlotTemplateController::class,
            'products' => ProductController::class,
            'customers' => CustomerController::class,
            'customer-depot-products' => CustomerDepotProductController::class,
        ]);
        
        Route::post('slot-templates/{slotTemplate}/duplicate', [SlotTemplateController::class, 'duplicate'])->name('slot-templates.duplicate');
        Route::post('slot-templates/bulk-duplicate', [SlotTemplateController::class, 'bulkDuplicate'])->name('slot-templates.bulk-duplicate');

           // ─── Slot Generation ──────────────────────────────────────────
        Route::get('slots/generate', [SlotGeneratorController::class, 'index'])->name('slots.generate.form');
        Route::post('slots/generate', [SlotGeneratorController::class, 'store'])->name('slots.generate');
        Route::resource('slots', SlotController::class)->except(['show']);
         Route::resource('slot-release-rules', SlotReleaseRuleController::class)
              ->names('slotReleaseRules')
              ->parameters(['slot-release-rules' => 'rule']);

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'store'])->name('settings.store');

        // User switching routes (testing only)
        Route::post('switch-user/{user}', [UserSwitchController::class, 'switchTo'])->name('switch-user');

        Route::get('booking-rules', [BookingRulesController::class, 'index'])->name('booking-rules.index');
        Route::post('booking-rules', [BookingRulesController::class, 'store'])->name('booking-rules.store');

        Route::get('slot-capacity', [SlotCapacityController::class, 'index'])->name('slot-capacity.index');
        Route::post('slot-capacity', [SlotCapacityController::class, 'update'])->name('slot-capacity.update');

        Route::get('slot-usage', [SlotUsageController::class, 'index'])->name('slot-usage.index');

        Route::get('settings/dashboard', [AdminSettingsController::class, 'dashboard'])->name('settings.dashboard');
        Route::resource('depot-case-ranges', DepotCaseRangeController::class);

        Route::resource('users', AdminController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'edit' => 'users.edit',
            'update' => 'users.update',
        ]);
    });

    /**
     * ───── Booking Routes (Available to Admin, Depot-Admin, Site-Admin) ─────
     */
    Route::prefix('admin')->as('admin.')->middleware(['role:admin|depot-admin|site-admin'])->group(function () {
        Route::resource('bookings', BookingController::class);
        
        // Arrival/Departure routes
        Route::get('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival.form');
        Route::post('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival');
        Route::patch('bookings/{booking}/departure', [BookingController::class, 'markDeparted'])->name('bookings.departure');
        
        // PDF Email and Download routes
        Route::post('bookings/{booking}/email-pdf', [BookingController::class, 'emailPDF'])->name('bookings.email-pdf');
        Route::get('bookings/{booking}/download-pdf', [BookingController::class, 'downloadPDF'])->name('bookings.download-pdf');
    });

    /**
     * ───── Depot Admin Routes ─────
     */
    Route::prefix('depot-admin')->as('depot.')->middleware(['role:depot-admin'])->group(function () {
        Route::get('/dashboard', [DepotAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/arrivals', [BookingController::class, 'arrivals'])->name('arrivals.index');
        
        Route::resource('slots', SlotController::class)->except(['show']);
    });

    /**
     * ───── Site Admin Routes ─────
     */
    Route::prefix('site-admin')->as('site.')->middleware(['role:site-admin'])->group(function () {
        Route::get('/dashboard', [SiteAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/search', [SiteAdminDashboardController::class, 'search'])->name('search');
        Route::get('/arrivals', [SiteAdminDashboardController::class, 'arrivals'])->name('arrivals.index');
        Route::get('/departures', [SiteAdminDashboardController::class, 'departures'])->name('departures.index');
    });

    /**
     * ───── Customer Routes ─────
     */
    Route::prefix('customer')->middleware(['role:customer'])->as('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        
        // Booking management
        Route::resource('bookings', \App\Http\Controllers\Customer\CustomerBookingController::class)->except(['destroy']);
        
        // API endpoints for booking creation
        Route::get('/availability', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'availability'])->name('availability');
        Route::get('/slots', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'slots'])->name('slots');
        
        // PDF Email and Download routes
        Route::post('/bookings/{booking}/email-pdf', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'emailPDF'])->name('bookings.email-pdf');
        Route::get('/bookings/{booking}/download-pdf', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'downloadPDF'])->name('bookings.download-pdf');
    });


    Route::fallback(function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});


});
