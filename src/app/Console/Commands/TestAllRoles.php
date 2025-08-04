<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class TestAllRoles extends Command
{
    protected $signature = 'app:test-all-roles';
    protected $description = 'Test all role routes and functionality';

    public function handle()
    {
        $this->info('Testing All Role Routes and Interfaces...');
        
        // Test Admin Routes
        $this->info("\n🔴 ADMIN ROUTES:");
        $adminRoutes = [
            'admin.dashboard' => 'Admin Dashboard',
            'admin.bookings.index' => 'Admin Bookings',
            'admin.depots.index' => 'Admin Depots',
            'admin.users.index' => 'Admin Users',
        ];
        $this->testRoutes($adminRoutes);

        // Test Depot-Admin Routes
        $this->info("\n🔵 DEPOT-ADMIN ROUTES:");
        $depotRoutes = [
            'depot.dashboard' => 'Depot-Admin Dashboard',
            'depot.bookings.index' => 'Depot-Admin Bookings',
            'depot.slots.index' => 'Depot-Admin Slots',
            'depot.arrivals.index' => 'Depot-Admin Live Arrivals',
        ];
        $this->testRoutes($depotRoutes);

        // Test Site-Admin Routes
        $this->info("\n🟢 SITE-ADMIN ROUTES:");
        $siteRoutes = [
            'site.dashboard' => 'Site-Admin Dashboard',
            'site.search' => 'Site-Admin Search',
            'site.arrivals.index' => 'Site-Admin Arrivals',
            'site.departures.index' => 'Site-Admin Departures',
        ];
        $this->testRoutes($siteRoutes);

        // Test Customer Routes
        $this->info("\n🟡 CUSTOMER ROUTES:");
        $customerRoutes = [
            'customer.dashboard' => 'Customer Dashboard',
            'customer.bookings.index' => 'Customer Bookings',
            'customer.bookings.create' => 'Customer Booking Create',
        ];
        $this->testRoutes($customerRoutes);

        // Check role counts
        $this->info("\n📊 ROLE STATISTICS:");
        $roleStats = [
            'admin' => User::role('admin')->count(),
            'depot-admin' => User::role('depot-admin')->count(),
            'site-admin' => User::role('site-admin')->count(),
            'customer' => User::role('customer')->count(),
        ];

        foreach ($roleStats as $role => $count) {
            $this->line("👥 {$role}: {$count} users");
        }

        $this->info("\n🏁 All role routes test completed!");
        
        return 0;
    }

    private function testRoutes(array $routes)
    {
        foreach ($routes as $routeName => $description) {
            try {
                $url = route($routeName);
                $this->line("✅ {$description}: {$url}");
            } catch (\Exception $e) {
                $this->error("❌ {$description}: Route not found - {$e->getMessage()}");
            }
        }
    }
}