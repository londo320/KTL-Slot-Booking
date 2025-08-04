<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Slot;
use Illuminate\Support\Facades\Route;

class TestCustomerRoutes extends Command
{
    protected $signature = 'app:test-customer-routes';
    protected $description = 'Test customer routes and functionality';

    public function handle()
    {
        $this->info('Testing Customer Routes and Controllers...');
        
        // Key customer routes to test
        $routes = [
            'customer.dashboard' => 'Customer Dashboard',
            'customer.bookings.index' => 'Customer Bookings List',
            'customer.bookings.create' => 'Customer Booking Create',
        ];

        $this->info('Checking route existence...');
        
        foreach ($routes as $routeName => $description) {
            try {
                $url = route($routeName);
                $this->line("✅ {$description}: {$url}");
            } catch (\Exception $e) {
                $this->error("❌ {$description}: Route not found - {$e->getMessage()}");
            }
        }

        // Check if customer views exist
        $this->info("\nChecking key customer views...");
        $views = [
            'customer.dashboard' => 'resources/views/customer/dashboard.blade.php',
            'customer.bookings.index' => 'resources/views/customer/bookings/index.blade.php',
            'customer.bookings.create' => 'resources/views/customer/bookings/create.blade.php',
        ];

        foreach ($views as $viewName => $path) {
            $fullPath = base_path($path);
            if (file_exists($fullPath)) {
                $this->line("✅ View {$viewName}: exists");
            } else {
                $this->error("❌ View {$viewName}: missing at {$path}");
            }
        }

        // Check customer data
        $this->info("\nChecking customer data...");
        try {
            $customerCount = Customer::count();
            $this->line("✅ Customers in database: {$customerCount}");
            
            $customerUsers = User::whereNotNull('customer_id')->count();
            $this->line("✅ Customer users: {$customerUsers}");
            
            $customerBookings = Booking::whereNotNull('customer_id')->count();
            $this->line("✅ Customer bookings: {$customerBookings}");
            
        } catch (\Exception $e) {
            $this->error("❌ Customer data issue: {$e->getMessage()}");
        }

        $this->info("\n🏁 Customer routes test completed!");
        
        return 0;
    }
}