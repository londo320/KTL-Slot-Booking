<?php

namespace App\Http\Controllers\DepotAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Slot;
use App\Models\Depot;
use Carbon\Carbon;

class DepotAdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:depot-admin']);
    }

    public function index()
    {
        $user = auth()->user();
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        if (empty($allowedDepotIds)) {
            abort(403, 'You are not assigned to any depots.');
        }

        // Today's stats
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $todaysBookings = Booking::whereHas('slot', function($q) use ($allowedDepotIds, $today, $tomorrow) {
            $q->whereIn('depot_id', $allowedDepotIds)
              ->whereBetween('start_at', [$today, $tomorrow]);
        })->with(['slot.depot', 'customer', 'bookingType'])->get();

        $stats = [
            'total_bookings' => $todaysBookings->count(),
            'arrived' => $todaysBookings->whereNotNull('arrived_at')->count(),
            'departed' => $todaysBookings->whereNotNull('departed_at')->count(),
            'outstanding' => $todaysBookings->whereNull('arrived_at')->count(),
            'on_site' => $todaysBookings->whereNotNull('arrived_at')->whereNull('departed_at')->count(),
        ];

        // Upcoming bookings (next 2 hours)
        $upcomingBookings = Booking::whereHas('slot', function($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
              ->whereBetween('start_at', [now(), now()->addHours(2)]);
        })
        ->whereNull('arrived_at')
        ->with(['slot.depot', 'customer', 'bookingType'])
        ->orderBy('slot_id')
        ->take(10)
        ->get();

        // Current arrivals (arrived but not departed)
        $currentArrivals = Booking::whereHas('slot', function($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
        ->whereNotNull('arrived_at')
        ->whereNull('departed_at')
        ->with(['slot.depot', 'customer', 'bookingType'])
        ->orderBy('arrived_at')
        ->get();

        // Get user's depots for display
        $userDepots = $user->depots()->get();

        return view('depot-admin.dashboard', compact(
            'stats', 
            'todaysBookings', 
            'upcomingBookings', 
            'currentArrivals',
            'userDepots'
        ));
    }
}