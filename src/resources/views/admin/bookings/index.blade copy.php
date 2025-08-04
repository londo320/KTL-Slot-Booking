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

<form method="GET" class="mb-4 flex flex-wrap gap-4 items-end">
  <div>
    <label class="block text-sm font-medium">Depot</label>
    <select name="depot_id" class="border rounded px-2 py-1 text-sm">
      <option value="">All</option>
      @foreach($depots as $depot)
              <option value="{{ $depot->id }}" {{ request('depot_id') == $depot->id ? 'selected' : '' }}>
            {{ $depot->name }}
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
      <option value="not_arrived" @selected(request('arrival') == 'not_arrived')>Not Arrived</option>
      <option value="arrived" @selected(request('arrival') == 'arrived')>Arrived</option>
     <option value="onsite" @selected(request('arrival') == 'onsite')>On Site</option>
    </select>
  </div>

  <div>
    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Filter</button>
  </div>
</form>


    <table class="min-w-full bg-white shadow rounded overflow-hidden text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Depot</th>
          <th class="px-4 py-2 text-left">Start → End</th>
          <th class="px-4 py-2 text-left">Customer / Ref</th>
          <th class="px-4 py-2 text-left">Type</th>
          <th class="px-4 py-2 text-left">Cases</th>
          <th class="px-4 py-2 text-left">Pallets</th>
          <th class="px-4 py-2 text-left">Arrival</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>

        @foreach($bookings as $booking)
          <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-2">{{ $booking->slot->depot->name }}</td>
            <td class="px-4 py-2">
              @php
                $isLate = now()->greaterThan($booking->slot->start_at) && !$booking->arrived_at;
              @endphp

              @if($isLate)
                <div id="late-timer-{{ $booking->id }}" class="text-red-600 text-xs font-semibold">
                  Late by: calculating...
                </div>
                <script>
                  document.addEventListener("DOMContentLoaded", function () {
                    const start = new Date("{{ $booking->slot->start_at->format('Y-m-d H:i:s') }}");
                    const target = document.getElementById("late-timer-{{ $booking->id }}");

                    function formatLateTime(ms) {
                      const totalMins = Math.floor(ms / 60000);
                      const days = Math.floor(totalMins / 1440);
                      const hours = Math.floor((totalMins % 1440) / 60);
                      const mins = totalMins % 60;

                      return `${days} Day${days !== 1 ? 's' : ''} ${hours} Hour${hours !== 1 ? 's' : ''} ${mins} Min${mins !== 1 ? 's' : ''} late`;
                    }

                    function updateLateTime() {
                      const now = new Date();
                      const diffMs = now - start;
                      if (diffMs > 0) {
                        target.textContent = formatLateTime(diffMs);
                      }
                    }

                    updateLateTime();
                    setInterval(updateLateTime, 60000);
                  });
                </script>
              @endif

              {{ $booking->slot->start_at->format('d-M H:i') }} →
              {{ $booking->slot->end_at->format('d-M H:i') }}
            </td>
            <td class="px-4 py-2">
              <div>{{ $booking->customer->name ?? '-' }}</div>
              <div class="text-xs text-gray-500">{{ $booking->reference ?? '-' }}</div>
            </td>
            <td class="px-4 py-2">{{ optional($booking->bookingType)->name ?? '-' }}</td>
            <td class="px-4 py-2">
              {{ $booking->actual_cases ?? '-' }} / {{ $booking->expected_cases ?? '-' }}
              @php
                $caseDiff = $booking->case_variance ?? 0;
                $caseColor = $caseDiff < 0 ? 'text-red-600' : ($caseDiff > 0 ? 'text-orange-500' : 'text-green-600');
              @endphp
              <div class="text-xs {{ $caseColor }}">Δ {{ $caseDiff }}</div>
            </td>
            <td class="px-4 py-2">
              {{ $booking->actual_pallets ?? '-' }} / {{ $booking->expected_pallets ?? '-' }}
              @php
                $palletDiff = $booking->pallet_variance ?? 0;
                $palletColor = $palletDiff < 0 ? 'text-red-600' : ($palletDiff > 0 ? 'text-orange-500' : 'text-green-600');
              @endphp
              <div class="text-xs {{ $palletColor }}">Δ {{ $palletDiff }}</div>
            </td>
            <td class="px-4 py-2">
              @if($booking->arrived_at)
                <div>✅ Arrived: {{ $booking->arrived_at->format('d-M H:i') }}</div>
              @endif
              @if($booking->departed_at)
                <div>🕒 Departed: {{ $booking->departed_at->format('d-M H:i') }}</div>

@php
  $arrived = \Carbon\Carbon::parse($booking->arrived_at);
  $departed = \Carbon\Carbon::parse($booking->departed_at);
  $slotStart = \Carbon\Carbon::parse($booking->slot->start_at);
  $slotEnd = \Carbon\Carbon::parse($booking->slot->end_at);

  $durationMins = $arrived->diffInMinutes($departed);
  $slotDurationMins = $slotStart->diffInMinutes($slotEnd);

  $days = floor($durationMins / 1440);
  $hours = floor(($durationMins % 1440) / 60);
  $minutes = $durationMins % 60;

  $isOverTime = $durationMins > $slotDurationMins;
  $badgeColor = $isOverTime ? 'bg-red-600' : 'bg-green-600';
  $badgeLabel = $isOverTime ? 'Tip: Over Time' : 'Tip: On Time';
@endphp

<div class="text-xs text-gray-700 mt-1">
  ⏱ Duration: {{ $days }}d {{ $hours }}h {{ $minutes }}m
  <span class="ml-2 inline-block px-2 py-0.5 rounded text-white text-xs font-semibold {{ $badgeColor }}">
    {{ $badgeLabel }}
  </span>
</div>
              @endif

              @if($booking->status)
                <span class="inline-block px-2 py-1 mt-1 rounded text-white text-xs font-semibold
                  {{ $booking->status === 'early' ? 'bg-blue-500' : ($booking->status === 'on time' ? 'bg-green-500' : 'bg-red-600') }}">
                  {{ ucfirst($booking->status) }}
                </span>
              @endif

              @unless($booking->arrived_at)
                <form action="{{ route('admin.bookings.arrival', $booking) }}" method="POST" class="inline-block">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="text-blue-600 text-xs underline">Mark Arrived</button>
                </form>
              @elseif(!$booking->departed_at)
                <form action="{{ route('admin.bookings.departure', $booking) }}" method="POST" class="inline-block ml-2">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="text-green-600 text-xs underline">Mark Departed</button>
                </form>
              @endif
            </td>
            <td class="px-4 py-2 space-x-2">
              <a href="{{ route('admin.bookings.edit', $booking) }}"
                 class="inline-block px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
              <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this booking?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">Delete</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-4">
      {{ $bookings->links() }}
    </div>
  </div>

{{-- Depot Summary --}}
<div class="mt-10 bg-white shadow rounded p-4 mx-auto max-w-7xl">
  <h3 class="text-lg font-semibold mb-4 text-gray-800 text-center">Depot Summary</h3>

  <div class="space-y-8">
    @foreach ($summaryByDepotCustomer as $depotName => $customers)
      <div class="border border-gray-200 bg-gray-50 rounded p-4 shadow-sm">
        {{-- Depot Header --}}
        <div class="text-center">
        <h4 class="text-md font-bold text-white bg-blue-600 px-3 py-1 rounded mb-4 inline-block">
          {{ $depotName }}
        </h4>
                </div>         
        {{-- Customer Cards --}}
        <div class="flex flex-wrap justify-center gap-6 mx-auto max-w-5xl">
          @foreach ($customers as $customerName => $summary)
            <div class="bg-white border rounded shadow p-4">
              <h5 class="text-sm font-semibold text-gray-700 mb-3">🧾 Customer: {{ $customerName }}</h5>

              <div class="flex gap-x-10 items-start text-sm">
                {{-- Left column --}}
                <div class="space-y-1 w-1/2">
                  <div>✅ <strong>Arrived:</strong> {{ $summary['arrived'] }}</div>
                  <div>⏰ <strong>Late:</strong> {{ $summary['late'] }}</div>
                  <div>🚚 <strong>Outstanding:</strong> {{ $summary['outstanding'] }}</div>
                  @if ($customerName === '_totals')
                  <div>🗓️ <strong>Slots Used:</strong>
                    {{ $summary['slots_used'] }} of {{ $summary['slots_total'] }}
                    ({{ $summary['slots_total'] > 0 ? round(($summary['slots_used'] / $summary['slots_total']) * 100) : 0 }}%)
                  </div>
                @endif
                                </div>

                {{-- Right column --}}
                <div class="space-y-1 w-1/2">
                  <div class="flex justify-between gap-2">
                    <span>📦 <strong>Expected Units:</strong></span>
                    <span>{{ number_format($summary['expected_cases']) }}</span>
                  </div>
                  <div class="flex justify-between gap-2">
                    <span>✅ <strong>Actual Units:</strong></span>
                    <span>{{ number_format($summary['actual_cases']) }}</span>
                  </div>
                  <div class="flex justify-between gap-2">
                    <span>🔺 <strong>Δ:</strong></span>
                    <span class="{{ $summary['case_variance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                      {{ number_format($summary['case_variance']) }}
                    </span>
                  </div>
                  <div class="flex justify-between gap-2 pt-2">
                    <span>📦 <strong>Expected Pallets:</strong></span>
                    <span>{{ number_format($summary['expected_pallets']) }}</span>
                  </div>
                  <div class="flex justify-between gap-2">
                    <span>✅ <strong>Actual Pallets:</strong></span>
                    <span>{{ number_format($summary['actual_pallets']) }}</span>
                  </div>
                  <div class="flex justify-between gap-2">
                    <span>🔺 <strong>Δ Pallets:</strong></span>
                    <span class="{{ $summary['pallet_variance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                      {{ number_format($summary['pallet_variance']) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</div>




{{-- Depot Totals Summary Only --}}
{{-- Depot Totals Summary --}}
<div class="mt-16 bg-white shadow rounded p-6 mx-auto max-w-5xl">
  <h3 class="text-lg font-semibold mb-8 text-gray-800 text-center">Depot Totals Summary</h3>

  <div class="space-y-10">
    @foreach ($summaryByDepotCustomer as $depotName => $customers)
      @if (isset($customers['_totals']))
        @php $totals = $customers['_totals']; @endphp

        <div class="border border-gray-200 bg-gray-50 rounded p-5 shadow-sm">
          {{-- Depot Header --}}
          <div class="text-center mb-4">
            <h4 class="text-md font-bold text-white bg-blue-600 px-4 py-1 rounded inline-block">
              {{ $depotName }}
            </h4>
          </div>

          {{-- Totals Card --}}
          <div class="mx-auto max-w-3xl bg-white border border-blue-300 rounded shadow p-6">
            <h5 class="text-md font-bold text-blue-800 mb-5 text-center">📊 Totals for {{ $depotName }}</h5>

            <div class="grid md:grid-cols-2 gap-8 text-sm">
              {{-- Left Column --}}
              <div class="space-y-2">
                <div>✅ <strong>Arrived:</strong> {{ $totals['arrived'] }}</div>
                <div>⏰ <strong>Late:</strong> {{ $totals['late'] }}</div>
                <div>🚚 <strong>Outstanding:</strong> {{ $totals['outstanding'] }}</div>
                <div>🗓️ <strong>Slots Used:</strong>
                  {{ $totals['slots_used'] }} of {{ $totals['slots_total'] }}
                  ({{ $totals['slots_total'] > 0 ? round(($totals['slots_used'] / $totals['slots_total']) * 100) : 0 }}%)
                </div>
              </div>

              {{-- Right Column --}}
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span>📦 <strong>Expected Units:</strong></span>
                  <span>{{ number_format($totals['expected_cases']) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>✅ <strong>Actual Units:</strong></span>
                  <span>{{ number_format($totals['actual_cases']) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>🔺 <strong>Δ:</strong></span>
                  <span class="{{ $totals['case_variance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($totals['case_variance']) }}
                  </span>
                </div>
                <div class="flex justify-between pt-2">
                  <span>📦 <strong>Expected Pallets:</strong></span>
                  <span>{{ number_format($totals['expected_pallets']) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>✅ <strong>Actual Pallets:</strong></span>
                  <span>{{ number_format($totals['actual_pallets']) }}</span>
                </div>
                <div class="flex justify-between">
                  <span>🔺 <strong>Δ Pallets:</strong></span>
                  <span class="{{ $totals['pallet_variance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($totals['pallet_variance']) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
    @endforeach
  </div>
</div>



</x-app-layout>
