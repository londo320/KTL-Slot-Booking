<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Slot;
use App\Models\BookingType;
use Carbon\Carbon;

class CustomerBookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $this->filteredBookings($request)->paginate(20);
        return view('customer.bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $date = $request->input('date') ?? now()->format('Y-m-d');
        $depotId = $request->input('depot_id') ?? auth()->user()->depots->pluck('id')->first();

        return view('customer.bookings.create', [
            'booking' => new Booking(),
            'slots'   => $this->getVisibleSlots($depotId, $date),
            'types'   => BookingType::orderBy('name')->get(),
            'selectedDepotId' => $depotId,
            'selectedDate' => $date,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'expected_cases' => 'nullable|integer|min:0',
            'expected_pallets' => 'nullable|integer|min:0',
            'container_size' => 'nullable|integer|min:0',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'vehicle_registration' => 'nullable|string|max:50',
            'container_number' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'carrier_company' => 'nullable|string|max:100',
            'load_type' => 'nullable|string|max:50',
            'hazmat' => 'nullable|boolean',
            'temperature_requirements' => 'nullable|string|max:50',
            'estimated_arrival' => 'nullable|date',
            'special_instructions' => 'nullable|string',
        ]);

        $slot = Slot::findOrFail($data['slot_id']);
        if ($slot->locked_at && $slot->locked_at->isPast()) {
            return back()->withErrors(['slot_id' => 'That slot is no longer available (cut-off time passed).']);
        }

        $data['user_id'] = auth()->id();
        $data['customer_id'] = auth()->user()->customer_id;

        Booking::create($data);

        return redirect()->route('customer.bookings.index')->with('success', 'Booking created.');
    }

    public function edit(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->arrived_at) {
            return redirect()->route('customer.bookings.index')->with('error', 'Cannot edit once arrived.');
        }

        $date = $request->input('date') ?? $booking->slot->start_at->format('Y-m-d');
        $depotId = $request->input('depot_id') ?? $booking->slot->depot_id;

        $slots = $this->getVisibleSlots($depotId, $date);

        if ($booking->slot && !$slots->contains('id', $booking->slot_id)) {
            $slots->push($booking->slot);
        }

        return view('customer.bookings.edit', [
            'booking' => $booking,
            'slots' => $slots->sortBy('start_at'),
            'types' => BookingType::orderBy('name')->get(),
            'selectedDepotId' => $depotId,
            'selectedDate' => $date,
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->arrived_at) {
            return redirect()->route('customer.bookings.index')->with('error', 'Booking already arrived.');
        }

        $data = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'expected_cases' => 'nullable|integer|min:0',
            'expected_pallets' => 'nullable|integer|min:0',
            'container_size' => 'nullable|integer|min:0',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'vehicle_registration' => 'nullable|string|max:50',
            'container_number' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'carrier_company' => 'nullable|string|max:100',
            'load_type' => 'nullable|string|max:50',
            'hazmat' => 'nullable|boolean',
            'temperature_requirements' => 'nullable|string|max:50',
            'estimated_arrival' => 'nullable|date',
            'special_instructions' => 'nullable|string',
        ]);

        $slot = Slot::findOrFail($data['slot_id']);
        if ($slot->locked_at && $slot->locked_at->isPast()) {
            return back()->withErrors(['slot_id' => 'That slot is locked and cannot be changed.']);
        }

        $booking->update($data);

        return redirect()->route('customer.bookings.index')->with('success', 'Booking updated.');
    }

    public function export(Request $request)
    {
        $bookings = $this->filteredBookings($request)->get();

        $csvHeaders = [
            'Depot',
            'Start Time',
            'End Time',
            'Booking Type',
            'Reference',
            'Expected Cases',
            'Expected Pallets',
            'Arrival',
            'Departure',
            'Status',
        ];

        $rows = $bookings->map(function ($b) {
            return [
                $b->slot->depot->name,
                $b->slot->start_at,
                $b->slot->end_at,
                optional($b->bookingType)->name,
                $b->reference,
                $b->expected_cases,
                $b->expected_pallets,
                optional($b->arrived_at)?->format('Y-m-d H:i'),
                optional($b->departed_at)?->format('Y-m-d H:i'),
                $b->status,
            ];
        });

        $filename = 'bookings_export_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($rows, $csvHeaders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $csvHeaders);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

private function filteredBookings(Request $request)
{
    $user = auth()->user();
    $customerId = $user->customer_id;
    $accessibleDepotIds = $user->depots->pluck('id')->toArray();

    $query = Booking::with(['slot.depot', 'bookingType'])
        ->where('customer_id', $customerId)
        ->whereHas('slot', function ($q) use ($accessibleDepotIds, $request) {
            $q->whereIn('depot_id', $accessibleDepotIds);

            if ($request->filled('week')) {
                // ✅ Corrected week logic using ISO weeks
                $yearStart = now()->startOfYear()->startOfWeek();
                $start = $yearStart->copy()->addWeeks($request->week - 1);
                $end = $start->copy()->endOfWeek();
                $q->whereBetween('start_at', [$start, $end]);
            } elseif ($request->filled('from_date')) {
                $from = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                $to = $request->filled('to_date')
                    ? \Carbon\Carbon::parse($request->to_date)->endOfDay()
                    : $from->copy()->endOfDay();
                $q->whereBetween('start_at', [$from, $to]);
            }

            if ($request->filled('depot_id')) {
                $q->where('depot_id', $request->depot_id);
            }
        });

    if ($request->filled('status')) {
        $query->where(function ($q) use ($request) {
            if ($request->status === 'not_arrived') {
                $q->whereNull('arrived_at');
            } elseif ($request->status === 'on_site') {
                $q->whereNotNull('arrived_at')->whereNull('departed_at');
            } elseif ($request->status === 'completed') {
                $q->whereNotNull('arrived_at')->whereNotNull('departed_at');
            }
        });
    }

    return $query->orderByDesc('slot_id');
}

protected function getVisibleSlots($depotId, $date)
{
    $user       = auth()->user();
    $customerId = $user->customer_id;

    return Slot::where('depot_id', $depotId)
        ->whereDate('start_at', $date)
        ->where('start_at', '>', now())

        // **NEW**: only slots with no bookings at all
        ->doesntHave('bookings')

        ->where(function ($query) use ($customerId, $depotId) {
            $query->where(function ($q) use ($customerId) {
                // Restricted slots (no released_at, but allowed_customers)
                $q->whereNull('released_at')
                  ->whereHas('allowed_customers', fn($q2) => 
                      $q2->where('customers.id', $customerId)
                  );
            })
            ->orWhere(function ($q) use ($depotId) {
                // Public slots (released_at in the past)
                $q->whereNotNull('released_at')
                  ->where('released_at', '<=', now());
            });
        })
        ->where(function ($q) {
            // Not locked
            $q->whereNull('locked_at')
              ->orWhere('locked_at', '>', now());
        })
        ->with('allowed_customers','depot')
        ->orderBy('start_at')
        ->get();
}
}
