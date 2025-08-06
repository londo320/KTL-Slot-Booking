<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Slot;
use App\Models\BookingType;
use App\Models\Depot;
use App\Models\Customer;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Services\PDFService;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin']);
    }

    /**
     * Get depot IDs that the current user can access based on their role
     */
    private function getAllowedDepotIds()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            // Super admin can access all depots
            return Depot::pluck('id')->toArray();
        } else {
            // depot-admin, site-admin, and customer roles are limited to assigned depots
            return $user->depots()->pluck('depots.id')->toArray();
        }
    }

    /**
     * Get depots that the current user can access based on their role
     */
    private function getAllowedDepots()
    {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            // Super admin can access all depots
            return Depot::orderBy('name')->get();
        } else {
            // depot-admin, site-admin, and customer roles are limited to assigned depots
            return $user->depots()->orderBy('name')->get();
        }
    }

    /**
     * Check if user has depot access, throw 403 if not
     */
    private function ensureDepotAccess()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('admin')) {
            $isDepotUser = DB::table('depot_user')->where('user_id', $user->id)->exists();
            if (!$isDepotUser) {
                abort(403, 'Unauthorized access — you are not linked to any depot.');
            }
        }
    }

    public function index(Request $request)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Base query restricted to user's depots
        $query = Booking::with(['slot.depot', 'bookingType', 'customer'])
            ->whereHas('slot', fn($q) => $q->whereIn('depot_id', $allowedDepotIds))
            ->latest();

        // Depot filter
        if ($depotId = $request->input('depot_id')) {
            $query->whereHas('slot', fn($q) => $q->where('depot_id', $depotId));
        }

        // Customer filter
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Booking type filter
        if ($bookingTypeId = $request->input('booking_type_id')) {
            $query->where('booking_type_id', $bookingTypeId);
        }

        // Date filters
        if ($from = $request->input('from')) {
            if (! $to = $request->input('to')) {
                $query->whereHas('slot', function($q) use($from) {
                    $q->whereDate('start_at', $from);
                });
            } else {
                $query->whereHas('slot', function($q) use($from, $to) {
                    $q->whereBetween('start_at', [$from, $to]);
                });
            }
        }

        // Arrival filter
        if ($arr = $request->arrival) {
            if ($arr === 'not_arrived') {
                $query->whereNull('arrived_at');
            } elseif ($arr === 'arrived') {
                $query->whereNotNull('arrived_at');
            } elseif ($arr === 'onsite') {
                $query->whereNotNull('arrived_at')->whereNull('departed_at');
            }
        }

        // Pagination for display
        $bookings = $query->paginate(30)
            ->appends($request->only(['depot_id','customer_id','booking_type_id','from','to','arrival']));

        // Load needed data for filters in view
        $depots = $this->getAllowedDepots();
        $customers = Customer::orderBy('name')->get();
        $types = BookingType::orderBy('name')->get();

        // Build summary by depot and customer using filtered bookings
        $filteredBookings = (clone $query)->get();

        $summaryByDepotCustomer = [];
       foreach ($filteredBookings as $b) {
    $dn = $b->slot->depot->name;
    $cn = $b->customer->name;
    $data =& $summaryByDepotCustomer[$dn][$cn];
    if (!isset($data)) {
        $data = [
            'arrived'=>0,'late'=>0,'outstanding'=>0,
            'expected_cases'=>0,'actual_cases'=>0,'case_variance'=>0,
            'expected_pallets'=>0,'actual_pallets'=>0,'pallet_variance'=>0,
            'late_duration_minutes' => 0, // total late minutes for depot/customer group
        ];
    }
    $data['expected_cases']   += $b->expected_cases ?? 0;
    $data['actual_cases']     += $b->actual_cases   ?? 0;
    $data['expected_pallets'] += $b->expected_pallets ?? 0;
    $data['actual_pallets']   += $b->actual_pallets   ?? 0;

 $now = Carbon::now();
$slotStart = Carbon::parse($b->slot->start_at);

if ($b->arrived_at) {
    $data['arrived']++;

    $arrivedAt = Carbon::parse($b->arrived_at);

    // Check if arrived late
    if ($arrivedAt->gt($slotStart)) {
        $data['late']++;
        // Calculate late duration (minutes) from slot start to arrival
        $lateMinutes = $arrivedAt->diffInMinutes($slotStart);
        $data['late_duration_minutes'] += $lateMinutes;
    }
} else {
    // Not arrived yet
    if ($now->gt($slotStart)) {
        // Late (not arrived but past slot start)
        $data['late']++;

        // Calculate late duration from slot start to now
        $lateMinutes = $now->diffInMinutes($slotStart);
        $data['late_duration_minutes'] += $lateMinutes;
    }
    $data['outstanding']++;
}
}

        // Compute variances and depot totals
        foreach ($summaryByDepotCustomer as $dn => $custs) {
            $totals = [
                'arrived'=>0,'late'=>0,'outstanding'=>0,
                'expected_cases'=>0,'actual_cases'=>0,'case_variance'=>0,
                'expected_pallets'=>0,'actual_pallets'=>0,'pallet_variance'=>0
            ];
            foreach ($custs as $cn => $sum) {
                $summaryByDepotCustomer[$dn][$cn]['case_variance'] =
                    $sum['actual_cases'] - $sum['expected_cases'];
                $summaryByDepotCustomer[$dn][$cn]['pallet_variance'] =
                    $sum['actual_pallets'] - $sum['expected_pallets'];
                foreach (['arrived','late','outstanding','expected_cases','actual_cases','expected_pallets','actual_pallets'] as $key) {
                    $totals[$key] += $sum[$key];
                }
            }
            $totals['case_variance']   = $totals['actual_cases']   - $totals['expected_cases'];
            $totals['pallet_variance'] = $totals['actual_pallets'] - $totals['expected_pallets'];
            $summaryByDepotCustomer[$dn]['_totals'] = $totals;
        }

        return view('admin.bookings.index', compact(
            'bookings','depots','customers','types','summaryByDepotCustomer'
        ));
    }

public function create()
{
    $this->ensureDepotAccess();
    $allowedDepotIds = $this->getAllowedDepotIds();
    $depots = $this->getAllowedDepots();

    $slots = Slot::with(['depot', 'allowed_customers'])
        ->whereIn('depot_id', $allowedDepotIds)
        ->whereDate('start_at', '>=', now()->toDateString())
        ->get()
        ->filter(fn($slot) => $slot->bookings()->count() < $slot->capacity);

    $types     = BookingType::orderBy('name')->get();
    $customers = Customer::orderBy('name')->get();

    $booking = new Booking(); // 👈 avoids undefined variable errors

    return view('admin.bookings.create', compact('slots', 'types', 'depots', 'customers', 'booking'));
}

public function show(Booking $booking)
{
    $this->ensureDepotAccess();
    $allowedDepotIds = $this->getAllowedDepotIds();
    
    // Check if user has access to this booking's depot
    if (!in_array($booking->slot->depot_id, $allowedDepotIds)) {
        abort(403, 'You do not have access to this booking.');
    }

    return view('admin.bookings.show', [
        'booking' => $booking->load(['slot.depot', 'bookingType', 'customer', 'user']),
    ]);
}
public function store(Request $request)
{
    $data = $request->validate([
        'slot_id'           => 'required|exists:slots,id',
        'booking_type_id'   => 'required|exists:booking_types,id',
        'expected_cases'    => 'nullable|integer|min:0',
        'actual_cases'      => 'nullable|integer|min:0',
        'expected_pallets'  => 'nullable|integer|min:0',
        'actual_pallets'    => 'nullable|integer|min:0',
        'container_size'    => 'required|integer|min:0',
        'reference'         => 'nullable|string',
        'notes'             => 'nullable|string',
        'customer_id'       => 'nullable|exists:customers,id',
        'vehicle_registration' => 'nullable|string|max:50',
        'container_number' => 'nullable|string|max:50',
        'driver_name' => 'nullable|string|max:100',
        'driver_phone' => 'nullable|string|max:20',
        'carrier_company' => 'nullable|string|max:100',
        'gate_number' => 'nullable|string|max:10',
        'bay_number' => 'nullable|string|max:10',
        'manifest_number' => 'nullable|string|max:50',
        'load_type' => 'nullable|string|max:50',
        'hazmat' => 'nullable|boolean',
        'temperature_requirements' => 'nullable|string|max:50',
        'estimated_arrival' => 'nullable|date',
        'special_instructions' => 'nullable|string',
    ]);

    $data['user_id'] = auth()->id();

    $booking = Booking::create($data);

    $this->recalculateSlot($booking);

    return redirect()->route('admin.bookings.index')->with('success','Booking created.');
    }

//    public function edit(Booking $booking)
//    {
//        $slots     = Slot::with('depot')->orderBy('start_at')->get();
//        $types     = BookingType::orderBy('name')->get();
//        $depots    = auth()->user()->depots()->orderBy('name')->get();
//        $customers = Customer::orderBy('name')->get();

//        return view('admin.bookings.edit', compact('booking','slots','types','depots','customers'));
//    }

public function edit(Booking $booking)
{
    $this->ensureDepotAccess();
    $allowedDepotIds = $this->getAllowedDepotIds();
    $depots = $this->getAllowedDepots();
    $types = BookingType::orderBy('name')->get();
    $customers = Customer::orderBy('name')->get();

    $availableSlots = Slot::with(['depot', 'allowed_customers'])
        ->whereIn('depot_id', $allowedDepotIds)
        ->whereDate('start_at', '>=', now()->toDateString())
        ->get()
        ->filter(function ($slot) use ($booking) {
            return $slot->bookings()->where('id', '!=', $booking->id)->count() < $slot->capacity;
        });

    // Ensure current slot is visible even if full or past
    if ($booking->slot && !$availableSlots->contains('id', $booking->slot->id)) {
        $booking->slot->load(['depot', 'allowed_customers']);
        $availableSlots->push($booking->slot);
    }

    $slots = $availableSlots->sortBy('start_at');

    return view('admin.bookings.edit', compact('booking','slots','types','depots','customers'));
}

  public function update(Request $request, Booking $booking)
{
    $data = $request->validate([
        'slot_id'           => 'nullable|exists:slots,id',
        'booking_type_id'   => 'required|exists:booking_types,id',
        'expected_cases'    => 'nullable|integer|min:0',
        'actual_cases'      => 'nullable|integer|min:0',
        'expected_pallets'  => 'nullable|integer|min:0',
        'actual_pallets'    => 'nullable|integer|min:0',
        'container_size'    => 'required|integer|min:0',
        'reference'         => 'nullable|string',
        'notes'             => 'nullable|string',
        'customer_id'       => 'nullable|exists:customers,id',
        'vehicle_registration' => 'nullable|string|max:50',
        'container_number' => 'nullable|string|max:50',
        'driver_name' => 'nullable|string|max:100',
        'driver_phone' => 'nullable|string|max:20',
        'carrier_company' => 'nullable|string|max:100',
        'gate_number' => 'nullable|string|max:10',
        'bay_number' => 'nullable|string|max:10',
        'manifest_number' => 'nullable|string|max:50',
        'load_type' => 'nullable|string|max:50',
        'hazmat' => 'nullable|boolean',
        'temperature_requirements' => 'nullable|string|max:50',
        'estimated_arrival' => 'nullable|date',
        'special_instructions' => 'nullable|string',
    ]);

    if (isset($data['slot_id']) && $data['slot_id'] != $booking->slot_id) {
        $this->ensureSlotIsAvailable($data['slot_id'], $booking->id);
        $booking->slot_id = $data['slot_id'];
    }

    $booking->update($data);

    $this->recalculateSlot($booking);

    return redirect()->route('admin.bookings.index')->with('success', 'Booking updated.');
}

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success','Booking deleted.');
    }

    public function markArrived(Request $request, Booking $booking)
    {
        // If this is a POST request with vehicle details, validate and update
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'vehicle_registration' => 'required|string|max:50',
                'container_number' => 'nullable|string|max:50',
                'driver_name' => 'nullable|string|max:100',
                'driver_phone' => 'nullable|string|max:20',
                'gate_number' => 'nullable|string|max:10',
                'bay_number' => 'nullable|string|max:10',
                'actual_cases' => 'nullable|integer|min:0',
                'actual_pallets' => 'nullable|integer|min:0',
            ]);

            $booking->update($validated);
            $booking->arrived_at = now();
            $booking->save();

            return redirect()->back()->with('success', 'Vehicle arrived and details updated.');
        }

        // If GET request, show the arrival form based on user role
        $user = auth()->user();
        if ($user->hasRole('site-admin')) {
            return view('site-admin.arrival-form', compact('booking'));
        } elseif ($user->hasRole('depot-admin')) {
            return view('depot-admin.bookings.arrival-form', compact('booking'));
        } else {
            return view('admin.bookings.arrival-form', compact('booking'));
        }
    }

    public function markDeparted(Booking $booking)
    {
        $booking->departed_at = now();
        $booking->save();

        return redirect()->back()->with('success', 'Booking marked as departed.');
    }

    protected function recalculateSlot(Booking $booking): void
    {
        $slot = $booking->slot;
        $length = 60;
        if ($booking->bookingType->name === 'Handball') {
            $s = $booking->container_size;
            $length = $s < 3000 ? 180 : ($s <= 6000 ? 240 : 300);
        }
        $slot->update(['end_at' => $slot->start_at->copy()->addMinutes($length)]);
    }

    protected function ensureSlotIsAvailable(int $slotId, int $ignoreBookingId = null): void
    {
        $slot = Slot::findOrFail($slotId);
        $end = $slot->start_at->copy()->addMinutes($slot->capacity ?? 60);
        $blocks = Slot::where('depot_id', $slot->depot_id)
            ->whereBetween('start_at', [$slot->start_at, $end->subSecond()])->get();

        foreach ($blocks as $b) {
            $count = $b->bookings()->when($ignoreBookingId, fn($q) => $q->where('id', '!=', $ignoreBookingId))->count();
            if ($count >= $b->capacity) abort(422, 'Time blocks full.');
        }
    }

    public function emailPDF(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // Check if user has access to this booking's depot
        if (!in_array($booking->slot->depot_id, $allowedDepotIds)) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500'
        ]);

        try {
            $booking->load(['slot.depot', 'bookingType', 'customer', 'user']);
            
            // Generate PDF with mPDF for better emoji support
            $pdfService = new PDFService();
            $mpdf = $pdfService->generateBookingPDF('admin.bookings.pdf', compact('booking'));
            $pdfContent = $mpdf->Output('', 'S');
            
            // Send email
            Mail::send('admin.bookings.email', [
                'booking' => $booking,
                'message' => $request->input('message', '')
            ], function ($mail) use ($request, $booking, $pdfContent) {
                $mail->to($request->input('email'))
                     ->subject("Booking Details #" . $booking->id)
                     ->attachData($pdfContent, "booking-{$booking->id}.pdf", [
                         'mime' => 'application/pdf',
                     ]);
            });

            return response()->json(['success' => true, 'message' => 'PDF sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send PDF: ' . $e->getMessage()]);
        }
    }

    public function downloadPDF(Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // Check if user has access to this booking's depot
        if (!in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['slot.depot', 'bookingType', 'customer', 'user']);
        
        $pdfService = new PDFService();
        $mpdf = $pdfService->generateBookingPDF('admin.bookings.pdf', compact('booking'));
        
        $filename = "booking-{$booking->id}-{$booking->customer->name}-" . $booking->slot->start_at->format('Y-m-d') . ".pdf";
        
        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function getLateDurationMinutesAttribute()
{
    $slotStart = Carbon::parse($this->slot->start_at);
    $now = Carbon::now();

    if ($this->arrived_at) {
        $arrivedAt = Carbon::parse($this->arrived_at);
        if ($arrivedAt->gt($slotStart)) {
            return $arrivedAt->diffInMinutes($slotStart);
        }
        return 0;
    } else {
        // Not arrived yet
        if ($now->gt($slotStart)) {
            return $now->diffInMinutes($slotStart);
        }
        return 0;
    }
}
}
