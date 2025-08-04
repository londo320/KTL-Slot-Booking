<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingType;

class BookingTypeController extends Controller
{
    public function index()
    {
        $types = BookingType::all();
        return view('admin.booking_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.booking_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        BookingType::create($data);

        return redirect()->route('admin.booking-types.index')->with('success', 'Booking type created.');
    }

    public function edit(BookingType $bookingType)
    {
        return view('admin.booking_types.edit', compact('bookingType'));
    }

    public function update(Request $request, BookingType $bookingType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $bookingType->update($data);

        return redirect()->route('admin.booking-types.index')->with('success', 'Booking type updated.');
    }

    public function destroy(BookingType $bookingType)
    {
        $bookingType->delete();

        return redirect()->route('admin.booking-types.index')->with('success', 'Booking type deleted.');
    }
}
