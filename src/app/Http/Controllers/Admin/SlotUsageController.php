<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SlotUsageController extends Controller
{
    public function index(Request $request)
    {
        $depots = Depot::all();
        $selectedDepot = $request->input('depot_id', $depots->first()?->id);
        $date = $request->input('date', now()->toDateString());

        $slots = Slot::with(['bookings.bookingType'])
            ->whereDate('start_at', $date)
            ->where('depot_id', $selectedDepot)
            ->orderBy('start_at')
            ->get();

        return view('admin.slot-usage.index', compact('depots', 'slots', 'selectedDepot', 'date'));
    }
}
