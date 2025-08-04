<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class GenerateBookingReferences extends Command
{
    protected $signature = 'bookings:generate-references';
    protected $description = 'Generate unique booking references for existing bookings';

    public function handle()
    {
        $this->info('Generating booking references for existing bookings...');
        
        $bookingsWithoutRef = Booking::whereNull('booking_reference')->get();
        
        if ($bookingsWithoutRef->isEmpty()) {
            $this->info('All bookings already have booking references.');
            return 0;
        }
        
        $this->info("Found {$bookingsWithoutRef->count()} bookings without references.");
        
        $bar = $this->output->createProgressBar($bookingsWithoutRef->count());
        $bar->start();
        
        foreach ($bookingsWithoutRef as $booking) {
            $booking->booking_reference = Booking::generateBookingReference();
            $booking->save();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('✅ Booking references generated successfully!');
        
        return 0;
    }
}