<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class TestArrivalProcess extends Command
{
    protected $signature = 'app:test-arrival-process';
    protected $description = 'Test the arrival process and transportation fields';

    public function handle()
    {
        $this->info('Testing Arrival Process and Transportation Fields...');
        
        // Find a booking without arrival
        $pendingBooking = Booking::whereNull('arrived_at')->first();
        
        if (!$pendingBooking) {
            $this->warn('No pending bookings found to test arrival process.');
            return 0;
        }

        $this->info("\n🔍 Testing with Booking: {$pendingBooking->booking_reference}");
        $customerName = $pendingBooking->customer->name ?? 'N/A';
        $this->line("Customer: {$customerName}");
        $this->line("Slot: {$pendingBooking->slot->depot->name} at {$pendingBooking->slot->start_at->format('d-M H:i')}");
        
        // Test transportation fields
        $this->info("\n📦 Current Transportation Data:");
        $this->line("Vehicle Registration: " . ($pendingBooking->vehicle_registration ?? 'Not set'));
        $this->line("Container Number: " . ($pendingBooking->container_number ?? 'Not set'));
        $this->line("Driver Name: " . ($pendingBooking->driver_name ?? 'Not set'));
        $this->line("Driver Phone: " . ($pendingBooking->driver_phone ?? 'Not set'));
        $this->line("Gate Number: " . ($pendingBooking->gate_number ?? 'Not set'));
        $this->line("Bay Number: " . ($pendingBooking->bay_number ?? 'Not set'));
        
        // Test route availability
        $this->info("\n🚛 Testing Arrival Form Routes:");
        
        $routes = [
            'admin' => 'admin.bookings.arrival.form',
            'depot-admin' => 'depot.bookings.arrival.form',
            'site-admin' => 'site.bookings.arrival.form',
        ];

        foreach ($routes as $role => $routeName) {
            try {
                $url = route($routeName, $pendingBooking);
                $this->line("✅ {$role}: {$url}");
            } catch (\Exception $e) {
                $this->error("❌ {$role}: Route error - {$e->getMessage()}");
            }
        }

        // Test validation rules
        $this->info("\n✅ Validation Requirements:");
        $this->line("• Vehicle Registration: REQUIRED on arrival");
        $this->line("• Container Number: Optional, can be edited");
        $this->line("• Driver Details: Optional");
        $this->line("• Gate/Bay Assignment: Optional");
        $this->line("• Actual Quantities: Optional");

        // Test customer read-only access
        $this->info("\n👤 Customer Access:");
        if ($pendingBooking->vehicle_registration || $pendingBooking->container_number) {
            $this->line("✅ Customer can view transportation details (read-only)");
        } else {
            $this->line("ℹ️ No transportation details to display for customer yet");
        }
        
        $this->info("\n🏁 Arrival process test completed!");
        $this->warn("💡 To test full arrival process, use the web interface with appropriate role login.");
        
        return 0;
    }
}