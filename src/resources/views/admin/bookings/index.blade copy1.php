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
        <input type="date" name="from" value="{{  request('from', now()->toDateString()) }}" class="border rounded px-2 py-1 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium">To</label>
        <input type="date" name="to" value="{{ request('to', now()->toDateString()) }}" class="border rounded px-2 py-1 text-sm">
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
      <div>
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Filter</button>
      </div>
    </form>

    {{-- Bookings Table --}}
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
  @foreach($bookings->groupBy(fn($b) => $b->slot->depot->name) as $depotName => $group)
    <tr><td colspan="8" class="bg-gray-200 font-semibold px-4 py-2">Depot: {{ $depotName }}</td></tr>
    @foreach($group->sortBy(fn($b) => $b->slot->start_at) as $booking)
      <tr class="border-t hover:bg-gray-50">
        {{-- Depot cell --}}
        <td class="px-4 py-2 align-top">{{ $booking->slot->depot->name }}</td>

        {{-- Start → End with live Late timer --}}
        <td class="px-4 py-2 align-top">
          @php $late = now()->greaterThan($booking->slot->start_at) && !$booking->arrived_at; @endphp
          @if($late)
            <div id="late-{{ $booking->id }}" class="text-red-600 text-xs font-semibold">Late by: calculating…</div>
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const start = new Date("{{ $booking->slot->start_at->format('Y-m-d H:i:s') }}");
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
          @endif
          {{ $booking->slot->start_at->format('d-M H:i') }} → {{ $booking->slot->end_at->format('d-M H:i') }}
        </td>

        {{-- Customer / Ref --}}
        <td class="px-4 py-2 align-top">
          <div>{{ $booking->customer->name ?? '-' }}</div>
          <div class="text-xs text-gray-500">{{ $booking->reference ?? '-' }}</div>
        </td>

        {{-- Type --}}
        <td class="px-4 py-2 align-top">{{ optional($booking->bookingType)->name ?? '-' }}</td>

        {{-- Cases --}}
        <td class="px-4 py-2 align-top">
          {{ $booking->actual_cases ?? '-' }} / {{ $booking->expected_cases ?? '-' }}
          @php
            $cd = $booking->case_variance ?? 0;
            $cc = $cd < 0 ? 'text-red-600' : ($cd > 0 ? 'text-orange-500' : 'text-green-600');
          @endphp
          <div class="text-xs {{ $cc }}">Δ {{ $cd }}</div>
        </td>

        {{-- Pallets --}}
        <td class="px-4 py-2 align-top">
          {{ $booking->actual_pallets ?? '-' }} / {{ $booking->expected_pallets ?? '-' }}
          @php
            $pd = $booking->pallet_variance ?? 0;
            $pc = $pd < 0 ? 'text-red-600' : ($pd > 0 ? 'text-orange-500' : 'text-green-600');
          @endphp
          <div class="text-xs {{ $pc }}">Δ {{ $pd }}</div>
        </td>

        {{-- Arrival / Departure / Duration Badge --}}
        <td class="px-4 py-2 align-top space-y-1">
          @if(!$booking->arrived_at)
            <form action="{{ route('admin.bookings.arrival', $booking) }}" method="POST" class="inline-block">
              @csrf @method('PATCH')
              <button class="text-blue-600 text-xs underline">Mark Arrived</button>
            </form>
          @else
            <div>✅ Arrived: {{ $booking->arrived_at->format('d-M H:i') }}</div>
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
              <h4 class="font-semibold mb-2">🧾 {{ $name==='__totals' ? 'Totals' : $name }}</h4>
              <div class="space-y-1 text-sm">
                <div>✅ Arrived: {{ $sum['arrived'] }}</div>
                <div>⏰ Late: {{ $sum['late'] }}</div>
                <div>🚚 Outstanding: {{ $sum['outstanding'] }}</div>
                @if($name==='__totals')
                  <div>🗓️ Slots Used: {{ $sum['arrived'] + $sum['late'] + $sum['outstanding'] }} of {{ $bookings->count() }}</div>
                @endif
                <div>📦 Exp: {{ number_format($sum['expected_cases']) }} / Act: {{ number_format($sum['actual_cases']) }}</div>
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
</x-app-layout>
@endsection
