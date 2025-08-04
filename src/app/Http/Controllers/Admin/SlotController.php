<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Depot;
use App\Models\BookingType;
use App\Models\Slot;

class SlotController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

public function index(Request $request)
{
    $query = Slot::with('depot')     // only load depot
                 ->withCount('bookings');  // optional: adds bookings_count

    if (! $request->has('show_past')) {
        $query->where('end_at', '>=', now());
    }

    if ($request->filled('depot_id')) {
        $query->where('depot_id', $request->depot_id);
    }

    if ($request->filled('date')) {
        $query->whereDate('start_at', $request->date);
    }

    $slots = $query->orderBy('start_at')->paginate(30);

    return view('admin.slots.index', compact('slots'));
}


    public function create()
    {
        $depots = Depot::all();
        $types  = BookingType::all();

        return view('admin.slots.create', compact('depots', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'depot_id'        => 'required|exists:depots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'start_at'        => 'required|date',
            'end_at'          => 'required|date|after:start_at',
            'is_blocked'      => 'sometimes|boolean',
        ]);

        Slot::create($data);

        return back()->with('success', 'Slot created successfully.');
    }

    public function edit(Slot $slot)
    {
        $depots = Depot::all();
        $types  = BookingType::all();

        return view('admin.slots.edit', compact('slot', 'depots', 'types'));
    }

    public function update(Request $request, Slot $slot)
    {
        $data = $request->validate([
            'depot_id'        => 'required|exists:depots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'start_at'        => 'required|date',
            'end_at'          => 'required|date|after:start_at',
            'is_blocked'      => 'sometimes|boolean',
        ]);

        $slot->update($data);

        return redirect()->route('admin.slots.index')->with('success', 'Slot updated successfully.');
    }

    public function destroy(Slot $slot)
    {
        $slot->delete();

        return back()->with('success', 'Slot deleted.');
    }
}
