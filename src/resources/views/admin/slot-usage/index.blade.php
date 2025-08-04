  {{-- Admin Nav --}}
  @include('layouts.admin-nav')
<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Slot Usage Viewer</h2>
  </x-slot>

  <div class="max-w-5xl mx-auto py-6">
    <form method="GET" action="{{ route('admin.slot-usage.index') }}" class="flex items-end gap-4 mb-6">
      <div>
        <label class="block text-sm mb-1">Depot</label>
        <select name="depot_id" class="border rounded p-2">
          @foreach($depots as $depot)
            <option value="{{ $depot->id }}" @selected($depot->id == $selectedDepot)>
              {{ $depot->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm mb-1">Date</label>
        <input type="date" name="date" value="{{ $date }}" class="border rounded p-2">
      </div>

      <div>
        <button class="mt-5 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Filter
        </button>
      </div>
    </form>

    <table class="w-full table-auto text-sm border">
      <thead class="bg-gray-100">
        <tr>
          <th class="border p-2">Start</th>
          <th class="border p-2">End</th>
          <th class="border p-2">Type(s)</th>
          <th class="border p-2">Booked</th>
          <th class="border p-2">Capacity</th>
          <th class="border p-2">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($slots as $slot)
          @php
            $count = $slot->bookings->count();
            $capacity = $slot->capacity;
            $status = $count >= $capacity ? 'Full' : ($count > 0 ? 'Partial' : 'Free');
          @endphp
          <tr>
            <td class="border p-2">{{ \Carbon\Carbon::parse($slot->start_at)->format('d-M H:i') }}</td>
            <td class="border p-2">{{ \Carbon\Carbon::parse($slot->end_at)->format('d-M H:i') }}</td>
            <td class="border p-2">
              @foreach($slot->bookings as $booking)
                <span class="inline-block bg-gray-200 rounded px-2 text-xs">{{ $booking->bookingType->name }}</span>
              @endforeach
            </td>
            <td class="border p-2">{{ $count }}</td>
            <td class="border p-2">{{ $capacity }}</td>
            <td class="border p-2">
              @if($status === 'Full')
                <span class="text-red-600 font-semibold">Full</span>
              @elseif($status === 'Partial')
                <span class="text-orange-600 font-semibold">Partial</span>
              @else
                <span class="text-green-600 font-semibold">Free</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="border p-4 text-center text-gray-500">No slots found for this depot and date.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-app-layout>
