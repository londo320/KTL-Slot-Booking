<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CustomerDepotProductController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;

Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'role:admin'])
     ->group(function () {
         // Admin CRUD for customer–depot–product rules
         Route::resource('customer-depot-products', CustomerDepotProductController::class);

         // (Optional) Admin UI for bookings
         Route::resource('bookings', AdminBookingController::class);
     });
