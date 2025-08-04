<?php 


namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Slot;
use App\Models\BookingType;

class BookingController extends Controller
{
public function index()
{
    $bookings = Booking::with(['slot.depot', 'bookingType'])
        ->where('customer_id', auth()->user()->customer_id)
        ->latest()
        ->paginate(20);

    return view('customer.bookings.index', compact('bookings'));
}





public function create()
{
    return view('customer.bookings.create', [
        'booking' => new Booking(),
        'slots'   => $this->getVisibleSlots(),
        'types'   => BookingType::orderBy('name')->get(),
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
    ]);

    $data['user_id'] = auth()->id();
    $data['customer_id'] = auth()->user()->customer_id;

    Booking::create($data);

    return redirect()->route('customer.bookings.index')->with('success', 'Booking created.');
}

public function edit(Booking $booking)
{
    $this->authorize('update', $booking);
    if ($booking->arrived_at) {
        return redirect()->route('customer.bookings.index')->with('error', 'Cannot edit once arrived.');
    }

    return view('customer.bookings.edit', [
        'booking' => $booking,
        'slots'   => $this->getVisibleSlots(),
        'types'   => BookingType::orderBy('name')->get(),
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
    ]);

    $booking->update($data);

    return redirect()->route('customer.bookings.index')->with('success', 'Booking updated.');
}

Protected function getVisibleSlots()
{
    $customerId = auth()->user()->customer_id;

    return Slot::where('depot_id', auth()->user()->depot_id)
        ->where('start_at', '>', now())
        ->whereNotNull('released_at')
        ->where('released_at', '<=', now())
        ->where(function ($query) {
            $query->whereNull('locked_at')->orWhere('locked_at', '>', now());
        })
        ->where(function ($query) use ($customerId) {
            $query->whereDoesntHave('allowed_customers')
                ->orWhereHas('allowed_customers', function ($q) use ($customerId) {
                    $q->where('customers.id', $customerId);
                });
        })
        ->orderBy('start_at')
        ->get();
}


}