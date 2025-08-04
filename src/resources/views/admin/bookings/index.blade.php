@extends('layouts.admin')

@section('content')
<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Bookings</h2>
      <a href="{{ route('admin.bookings.create') }}"
         class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
        + New Booking
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-sm font-medium">Depot</label>
        <select name="depot_id" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          @foreach($depots as $depot)
            <option value="{{ $depot->id }}" @selected(request('depot_id') == $depot->id)>{{ $depot->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Customer</label>
        <select name="customer_id" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Type</label>
        <select name="booking_type_id" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          @foreach($types as $type)
            <option value="{{ $type->id }}" @selected(request('booking_type_id') == $type->id)>{{ $type->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-2 py-1 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-2 py-1 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium">Arrival</label>
        <select name="arrival" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <option value="not_arrived" @selected(request('arrival')=='not_arrived')>Not Arrived</option>
          <option value="arrived" @selected(request('arrival')=='arrived')>Arrived</option>
          <option value="onsite" @selected(request('arrival')=='onsite')>On Site</option>
        </select>
      </div>
      <div class="flex space-x-2">
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Filter</button>
        <a href="{{ route('admin.bookings.index') }}" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">Clear</a>
      </div>
    </form>

    {{-- Bookings Table --}}
    <table class="min-w-full bg-white shadow rounded overflow-hidden text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Booking Reference</th>
          <th class="px-4 py-2 text-left">Start → End</th>
          <th class="px-4 py-2 text-left">Customer / Collection</th>
          <th class="px-4 py-2 text-left">Type</th>
          <th class="px-4 py-2 text-left">Cases</th>
          <th class="px-4 py-2 text-left">Pallets</th>
          <th class="px-4 py-2 text-left">Arrival</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
  @foreach($bookings->groupBy(fn($b) => $b->slot->depot->name) as $depotName => $group)
    <tr><td colspan="8" class="bg-gray-200 font-semibold px-4 py-2">Depot: {{ $depotName }}</td></tr>
    @foreach($group->sortBy(fn($b) => $b->slot->start_at) as $booking)
      <tr class="border-t hover:bg-gray-50">
        {{-- Booking Reference --}}
        <td class="px-4 py-2 align-top">
          <div class="font-mono text-sm font-semibold text-blue-600">{{ $booking->booking_reference }}</div>
        </td>

        {{-- Start → End with live Late timer --}}
              <td class="px-4 py-2 align-top">
              @php
                $slotStart = $booking->slot->start_at;
                $now = now();
                $arrivedAt = $booking->arrived_at;

                // Determine if booking is late
                $isLateNotArrived = $now->greaterThan($slotStart) && !$arrivedAt;
                $isLateArrived = $arrivedAt && $arrivedAt->greaterThan($slotStart);
              @endphp

              @if($isLateNotArrived)
                <div id="late-{{ $booking->id }}" class="text-red-600 text-xs font-semibold">Late by: calculating…</div>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    const start = new Date("{{ $slotStart->format('Y-m-d H:i:s') }}");
                    const el = document.getElementById('late-{{ $booking->id }}');
                    function update() {
                      const now = new Date();
                      let diff = Math.floor((now - start) / 60000);
                      const d = Math.floor(diff / 1440); diff %= 1440;
                      const h = Math.floor(diff / 60); const m = diff % 60;
                      el.textContent = `Late by: ${d}d ${h}h ${m}m`;
                    }
                    update(); setInterval(update, 60000);
                  });
                </script>
              @elseif($isLateArrived)
                @php
                  $lateMinutes = $arrivedAt->diffInMinutes($slotStart);
                  $d = intdiv($lateMinutes, 1440);
                  $h = intdiv($lateMinutes % 1440, 60);
                  $m = $lateMinutes % 60;
                @endphp
                <div class="text-yellow-600 text-xs font-semibold">
                  Arrived Late by: {{ $d }}d {{ $h }}h {{ $m }}m
                </div>
              @endif

              {{ $slotStart->format('d-M H:i') }} → {{ $booking->slot->end_at->format('d-M H:i') }}
</td>

        {{-- Customer / Collection --}}
        <td class="px-4 py-2 align-top">
          <div class="text-sm font-medium text-gray-900">{{  $booking->customer->name ?? '-' }}</div>
          @if($booking->reference)
            <div class="text-xs text-gray-600">Collection: {{ $booking->reference }}</div>
          @endif
        </td>

        {{-- Type --}}
        <td class="px-4 py-2 align-top">{{ optional($booking->bookingType)->name ?? '-' }}</td>

        {{-- Cases --}}
        <td class="px-4 py-2 align-top">
          <div class="text-sm">
            {{ $booking->actual_cases ?? '-' }} / {{ $booking->expected_cases ?? '-' }}
          </div>
          @if($booking->actual_cases !== null && $booking->expected_cases !== null)
            @php
              $cd = $booking->case_variance ?? 0;
              if ($cd < 0) {
                $icon = '↓';
                $color = 'text-red-600';
                $text = 'Under by ' . abs($cd);
              } elseif ($cd > 0) {
                $icon = '↑';
                $color = 'text-blue-600';
                $text = 'Over by ' . $cd;
              } else {
                $icon = '=';
                $color = 'text-green-600';
                $text = 'Matched';
              }
            @endphp
            <div class="text-xs {{ $color }} font-medium">
              <span class="text-base">{{ $icon }}</span> {{ $text }}
            </div>
          @endif
        </td>

        {{-- Pallets --}}
        <td class="px-4 py-2 align-top">
          <div class="text-sm">
            {{ $booking->actual_pallets ?? '-' }} / {{ $booking->expected_pallets ?? '-' }}
          </div>
          @if($booking->actual_pallets !== null && $booking->expected_pallets !== null)
            @php
              $pd = $booking->pallet_variance ?? 0;
              if ($pd < 0) {
                $icon = '↓';
                $color = 'text-red-600';
                $text = 'Under by ' . abs($pd);
              } elseif ($pd > 0) {
                $icon = '↑';
                $color = 'text-blue-600';
                $text = 'Over by ' . $pd;
              } else {
                $icon = '=';
                $color = 'text-green-600';
                $text = 'Matched';
              }
            @endphp
            <div class="text-xs {{ $color }} font-medium">
              <span class="text-base">{{ $icon }}</span> {{ $text }}
            </div>
          @endif
        </td>

        {{-- Arrival / Departure / Duration Badge --}}
        <td class="px-4 py-2 align-top space-y-1">
          @if(!$booking->arrived_at)
            <button onclick="openArrivalModal({{ $booking->id }}, '{{ $booking->booking_reference }}', '{{ addslashes($booking->customer->name ?? 'N/A') }}', '{{ $booking->slot->depot->name }}', '{{ $booking->slot->start_at->format('d-M-Y H:i') }}', '{{ $booking->vehicle_registration ?? '' }}', '{{ $booking->container_number ?? '' }}', '{{ $booking->driver_name ?? '' }}', '{{ $booking->driver_phone ?? '' }}', '{{ $booking->gate_number ?? '' }}', '{{ $booking->bay_number ?? '' }}', '{{ $booking->actual_cases ?? '' }}', '{{ $booking->actual_pallets ?? '' }}', '{{ $booking->expected_cases ?? 0 }}', '{{ $booking->expected_pallets ?? 0 }}', '{{ addslashes($booking->special_instructions ?? '') }}')" 
                    class="inline-block bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 cursor-pointer">
              🚛 Process Arrival
            </button>
          @else
            <div>✅ Arrived: {{ $booking->arrived_at->format('d-M H:i') }}</div>
            @if($booking->vehicle_registration)
              <div class="text-xs text-gray-600">🚛 {{ $booking->vehicle_registration }}</div>
            @endif
            @if($booking->container_number)
              <div class="text-xs text-gray-600">📦 {{ $booking->container_number }}</div>
            @endif
          @endif

          @if($booking->arrived_at && !$booking->departed_at)
            <form action="{{ route('admin.bookings.departure', $booking) }}" method="POST" class="inline-block">
              @csrf @method('PATCH')
              <button class="text-green-600 text-xs underline">Mark Departed</button>
            </form>
          @elseif($booking->departed_at)
            <div>🕒 Departed: {{ $booking->departed_at->format('d-M H:i') }}</div>
            @php
              $arr = \Carbon\Carbon::parse($booking->arrived_at);
              $dep = \Carbon\Carbon::parse($booking->departed_at);
              $dur = $arr->diffInMinutes($dep);
              $slotDur = $booking->slot->start_at->diffInMinutes($booking->slot->end_at);
              $badge = $dur > $slotDur
                ? ['Over Time', 'bg-red-600']
                : ['On Time', 'bg-green-600'];
              $d = floor($dur / 1440);
              $h = floor(($dur % 1440) / 60);
              $m = $dur % 60;
            @endphp
            <div class="text-xs text-gray-700 mt-1">
              ⏱ Duration: {{ "$d d $h h $m m" }}
              <span class="ml-2 inline-block px-2 py-0.5 rounded text-white text-xs font-semibold {{ $badge[1] }}">
                Tip: {{ $badge[0] }}
              </span>
            </div>
          @endif
        </td>

        {{-- Actions --}}
        <td class="px-4 py-2 align-top space-y-1">
          <a href="{{ route('admin.bookings.edit', $booking) }}"
             class="inline-block px-2 py-1 bg-yellow-500 text-white rounded-full hover:bg-yellow-600 text-xs">
            Edit
          </a>
          <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Delete?');">
            @csrf @method('DELETE')
            <button class="inline-block px-2 py-1 bg-red-600 text-white rounded-full hover:bg-red-700 text-xs">
              Delete
            </button>
          </form>
        </td>
      </tr>
    @endforeach
  @endforeach
</tbody>
    </table>

    <div class="mt-4">{{ $bookings->links() }}</div>

    {{-- Depot Summary --}}
    <div class="mt-10">
      @foreach($summaryByDepotCustomer as $dep => $custs)
        <h3 class="text-lg font-semibold mb-4 text-center bg-blue-600 text-white px-4 py-1 rounded">{{ $dep }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          @foreach($custs as $name => $sum)
            <div class="bg-white border rounded shadow p-4">
              <h4 class="font-semibold mb-2">🧾 {{ $name==='_totals' ? 'Site Totals' : $name }}</h4>
              <div class="space-y-1 text-sm">
                <div>✅ Arrived: {{ $sum['arrived'] }}</div>
                <div>⏰ Late: {{ $sum['late'] }}</div>
                <div>🚚 Outstanding: {{ $sum['outstanding'] }}</div>
                @if($name==='__totals')
                  <div>🗓️ Slots Used: {{ $sum['arrived'] + $sum['late'] + $sum['outstanding'] }} of {{ $bookings->count() }}</div>
                @endif
                <div>📦 Exp Units: {{ number_format($sum['expected_cases']) }} / Act: {{ number_format($sum['actual_cases']) }}</div>
                <div>🔺 Δ: {{ number_format($sum['case_variance']) }}</div>
                <div>📦 Pal Exp: {{ number_format($sum['expected_pallets']) }} / Act: {{ number_format($sum['actual_pallets']) }}</div>
                <div>🔺 Δ Pal: {{ number_format($sum['pallet_variance']) }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>

  <!-- Arrival Modal -->
  <div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">🚛 Vehicle Arrival Processing</h3>
          <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Booking Summary -->
        <div id="bookingSummary" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <!-- Will be populated by JavaScript -->
        </div>

        <!-- Arrival Form -->
        <form id="arrivalForm" method="POST" class="mt-6">
          @csrf
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Required Vehicle Registration -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Vehicle Registration <span class="text-red-500">*</span>
              </label>
              <input type="text" id="vehicleRegistration" name="vehicle_registration" required
                     placeholder="e.g., AB12 CDE"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
            </div>

            <!-- Container Number -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
              <input type="text" id="containerNumber" name="container_number"
                     placeholder="e.g., CONT123456 or TR123456"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p class="text-xs text-gray-500 mt-1">Can be updated if different from booking</p>
            </div>

            <!-- Driver Name -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
              <input type="text" id="driverName" name="driver_name"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Driver Phone -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Driver Phone</label>
              <input type="tel" id="driverPhone" name="driver_phone"
                     placeholder="e.g., +44 7XXX XXXXXX"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Gate Number -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Gate Number</label>
              <input type="text" id="gateNumber" name="gate_number"
                     placeholder="e.g., Gate 1"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Bay Number -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Bay Number</label>
              <input type="text" id="bayNumber" name="bay_number"
                     placeholder="e.g., Bay A1"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            <!-- Actual Cases -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Actual Cases</label>
              <input type="number" id="actualCases" name="actual_cases" min="0"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p id="expectedCases" class="text-xs text-gray-500 mt-1">Expected: 0</p>
            </div>

            <!-- Actual Pallets -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Actual Pallets</label>
              <input type="number" id="actualPallets" name="actual_pallets" min="0"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p id="expectedPallets" class="text-xs text-gray-500 mt-1">Expected: 0</p>
            </div>

          </div>

          <!-- Special Instructions -->
          <div id="specialInstructions" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 hidden">
            <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
            <p id="specialInstructionsText" class="text-yellow-700"></p>
          </div>

          <!-- Arrival Time Display -->
          <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
            <h4 class="font-medium text-green-800 mb-2">📅 Arrival Time:</h4>
            <p class="text-green-700 font-semibold" id="arrivalTime">Will be recorded as: [Current Time]</p>
          </div>

          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-4">
            <button type="button" onclick="closeArrivalModal()" 
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
              🚛 Mark Vehicle Arrived
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let currentBookingId = null;

    function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, driverName, driverPhone, gateNum, bayNum, actualCases, actualPallets, expectedCases, expectedPallets, specialInstructions) {
      currentBookingId = bookingId;
      
      // Update booking summary
      document.getElementById('bookingSummary').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>
            <strong>Booking:</strong> ${bookingRef}<br>
            <strong>Customer:</strong> ${customer}
          </div>
          <div>
            <strong>Depot:</strong> ${depot}<br>
            <strong>Scheduled:</strong> ${scheduledTime}
          </div>
          <div>
            <strong>Expected:</strong> ${expectedCases} cases, ${expectedPallets} pallets
          </div>
        </div>
      `;

      // Update form action
      document.getElementById('arrivalForm').action = `/admin/bookings/${bookingId}/arrival`;

      // Populate form fields
      document.getElementById('vehicleRegistration').value = vehicleReg;
      document.getElementById('containerNumber').value = containerNum;
      document.getElementById('driverName').value = driverName;
      document.getElementById('driverPhone').value = driverPhone;
      document.getElementById('gateNumber').value = gateNum;
      document.getElementById('bayNumber').value = bayNum;
      document.getElementById('actualCases').value = actualCases;
      document.getElementById('actualPallets').value = actualPallets;

      // Update expected quantities display
      document.getElementById('expectedCases').textContent = `Expected: ${expectedCases}`;
      document.getElementById('expectedPallets').textContent = `Expected: ${expectedPallets}`;

      // Show special instructions if any
      if (specialInstructions && specialInstructions.trim() !== '') {
        document.getElementById('specialInstructionsText').textContent = specialInstructions;
        document.getElementById('specialInstructions').classList.remove('hidden');
      } else {
        document.getElementById('specialInstructions').classList.add('hidden');
      }

      // Update arrival time display
      const now = new Date();
      const timeString = now.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;

      // Show modal
      document.getElementById('arrivalModal').classList.remove('hidden');
      
      // Focus on vehicle registration field
      setTimeout(() => {
        document.getElementById('vehicleRegistration').focus();
      }, 100);
    }

    function closeArrivalModal() {
      document.getElementById('arrivalModal').classList.add('hidden');
      currentBookingId = null;
    }

    // Close modal when clicking outside
    document.getElementById('arrivalModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeArrivalModal();
      }
    });

    // Update time display every second
    setInterval(() => {
      if (!document.getElementById('arrivalModal').classList.contains('hidden')) {
        const now = new Date();
        const timeString = now.toLocaleString('en-GB', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        });
        document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;
      }
    }, 1000);
  </script>
</x-app-layout>
@endsection
