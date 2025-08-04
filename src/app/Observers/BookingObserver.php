<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "creating" event.
     */
    public function creating(Booking $booking): void
    {
        $this->calculateVariances($booking);
    }

    /**
     * Handle the Booking "updating" event.
     */
    public function updating(Booking $booking): void
    {
        $this->calculateVariances($booking);
    }

    /**
     * Calculate case and pallet variances
     */
    private function calculateVariances(Booking $booking): void
    {
        // Calculate case variance
        if ($booking->actual_cases !== null && $booking->expected_cases !== null) {
            $booking->case_variance = $booking->actual_cases - $booking->expected_cases;
        } else {
            $booking->case_variance = 0;
        }

        // Calculate pallet variance
        if ($booking->actual_pallets !== null && $booking->expected_pallets !== null) {
            $booking->pallet_variance = $booking->actual_pallets - $booking->expected_pallets;
        } else {
            $booking->pallet_variance = 0;
        }
    }
}