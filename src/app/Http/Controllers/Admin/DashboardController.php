<?php

namespace App\Http\Controllers\Admin;

use App\Models\Depot;
use App\Models\Booking;
use App\Models\BookingType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        // Parse incoming date or default to today
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        $bookingTypes = BookingType::all();
        $depots = Depot::with(['slots.bookings'])->get();

        foreach ($depots as $depot) {
            // Filter slots that occur on the selected date
            $slots = $depot->slots->filter(function($slot) use ($date) {
                return Carbon::parse($slot->start_at)->toDateString() === $date->toDateString();
            });

            $summary = [
                'date' => $date,
                'total' => $slots->count(),
                'used' => 0,
                'available' => 0,
                'arrived' => 0,
                'in_progress' => 0,
                'finished' => 0,
                'late' => 0,
                'types' => [],
            ];

            foreach ($slots as $slot) {
                $used = $slot->bookings->count();
                $capacity = $slot->capacity;
                $available = max(0, $capacity - $used);

                $summary['used'] += $used;
                $summary['available'] += $available;

                foreach ($slot->bookings as $booking) {
                    if ($booking->arrived_at && !$booking->departed_at) {
                        $summary['arrived']++;
                        $summary['in_progress']++;
                    } elseif ($booking->arrived_at && $booking->departed_at) {
                        $summary['finished']++;
                    }

                    if (!$booking->arrived_at && Carbon::now()->greaterThan(Carbon::parse($slot->start_at)->addMinutes(30))) {
                        $summary['late']++;
                    }

                    $typeId = $booking->booking_type_id;
                    $summary['types'][$typeId]['used'] = ($summary['types'][$typeId]['used'] ?? 0) + 1;
                    $summary['types'][$typeId]['capacity'] = ($summary['types'][$typeId]['capacity'] ?? 0) + 1;
                }

                // Account capacity for types as well
                $typeId = $slot->booking_type_id;
                $summary['types'][$typeId]['capacity'] = ($summary['types'][$typeId]['capacity'] ?? 0) + $capacity;
            }

            $depot->summary = $summary;
        }

        return view('admin.dashboard', compact('depots', 'bookingTypes', 'date'));
    }
}
