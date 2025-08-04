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

    /**
     * ───── Admin Routes ─────
     */
    Route::prefix('admin')->as('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resources([
            'depots' => DepotController::class,
            'booking-types' => BookingTypeController::class,           
            'bookings' => BookingController::class,
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

        Route::get('booking-rules', [BookingRulesController::class, 'index'])->name('booking-rules.index');
        Route::post('booking-rules', [BookingRulesController::class, 'store'])->name('booking-rules.store');

        Route::get('slot-capacity', [SlotCapacityController::class, 'index'])->name('slot-capacity.index');
        Route::post('slot-capacity', [SlotCapacityController::class, 'update'])->name('slot-capacity.update');

        Route::get('slot-usage', [SlotUsageController::class, 'index'])->name('slot-usage.index');

        Route::get('slot-settings', [SlotGenerationSettingController::class, 'index'])->name('slot-settings.index');
        Route::post('slot-settings', [SlotGenerationSettingController::class, 'store'])->name('slot-settings.store');

        Route::get('settings/dashboard', [AdminSettingsController::class, 'dashboard'])->name('settings.dashboard');

        // Depot Product Management
        Route::get('depots/{depot}/products', [DepotProductController::class, 'index'])->name('depots.products.index');
        Route::post('depots/{depot}/products', [DepotProductController::class, 'update'])->name('depots.products.update');
        Route::delete('depots/{depot}/products/{product}', [DepotProductController::class, 'destroy'])->name('depots.products.destroy');

        // Case Ranges
        Route::get('case-ranges', [DepotCaseRangeController::class, 'index'])->name('case-ranges.index');
        Route::get('case-ranges/create', [DepotCaseRangeController::class, 'create'])->name('case-ranges.create');
        Route::post('case-ranges', [DepotCaseRangeController::class, 'store'])->name('case-ranges.store');
        Route::get('case-ranges/{caseRange}/edit', [DepotCaseRangeController::class, 'edit'])->name('case-ranges.edit');
        Route::put('case-ranges/{caseRange}', [DepotCaseRangeController::class, 'update'])->name('case-ranges.update');
        Route::delete('case-ranges/{caseRange}', [DepotCaseRangeController::class, 'destroy'])->name('case-ranges.destroy');

        // Arrival/Departure
        Route::get('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival.form');
        Route::post('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival');
        Route::patch('bookings/{booking}/departure', [BookingController::class, 'markDeparted'])->name('bookings.departure');

       // Route::get('users', [AdminController::class, 'index'])->name('users.index');
       // Route::post('users/bulk-action', [AdminController::class, 'bulkAction'])->name('users.bulkAction');
       // Route::get('users/assign-role/{userId}', [AdminController::class, 'showAssignForm']);
       // Route::put('users/assign-role/{userId}', [AdminController::class, 'assignRoleAndDepots'])->name('assignRoleAndDepots');
       // Route::get('users/{userId}/details', [AdminController::class, 'getUserDetails'])->name('users.details');
       // Route::put('users/{userId}', [AdminController::class, 'update'])->name('users.update');
        Route::resource('users', AdminController::class)->except(['show']);
      
        

    });

    /**
     * ───── Depot Admin Routes ─────
     */
    Route::prefix('depot-admin')->as('depot.')->middleware(['role:depot-admin'])->group(function () {
        Route::get('/dashboard', [DepotAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/arrivals', [BookingController::class, 'arrivals'])->name('arrivals.index');
        
        Route::resource('slots', SlotController::class)->except(['show']);
        Route::resource('bookings', BookingController::class)->only(['index', 'edit', 'update']);
        Route::get('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival.form');
        Route::post('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival');
        Route::patch('bookings/{booking}/departure', [BookingController::class, 'markDeparted'])->name('bookings.departure');
    });

    /**
     * ───── Site Admin Routes ─────
     */
    Route::prefix('site-admin')->as('site.')->middleware(['role:site-admin'])->group(function () {
        Route::get('/dashboard', [SiteAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/search', [SiteAdminDashboardController::class, 'search'])->name('search');
        Route::get('/arrivals', [SiteAdminDashboardController::class, 'arrivals'])->name('arrivals.index');
        Route::get('/departures', [SiteAdminDashboardController::class, 'departures'])->name('departures.index');
        
        Route::get('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival.form');
        Route::post('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival');
        Route::patch('bookings/{booking}/departure', [BookingController::class, 'markDeparted'])->name('bookings.departure');
    });

    /**
     * ───── Customer Routes ─────
     */
    Route::prefix('customer')->middleware(['role:customer'])->as('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        //Route::resource('bookings', CustomerBookingController::class)->only(['index', 'create', 'store']);
         Route::resource('bookings', \App\Http\Controllers\Customer\CustomerBookingController::class)->except(['show', 'destroy']);
    });


    Route::fallback(function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});


});
