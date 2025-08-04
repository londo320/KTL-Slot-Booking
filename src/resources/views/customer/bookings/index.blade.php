{{-- resources/views/customer/bookings/index.blade.php --}}
<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">My Bookings</h2>
      <a href="{{ route('customer.bookings.create') }}"
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

    @if($bookings->isEmpty())
      <p class="text-gray-500">No bookings yet.</p>
    @else
      <table class="min-w-full bg-white shadow rounded overflow-hidden text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Booking Ref</th>
            <th class="px-4 py-2 text-left">Depot</th>
            <th class="px-4 py-2 text-left">Start → End</th>
            <th class="px-4 py-2 text-left">Type</th>
            <th class="px-4 py-2 text-left">Vehicle/Container</th>
            <th class="px-4 py-2 text-left">Expected / Actual</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bookings as $booking)
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2">
                <span class="font-mono text-sm font-semibold text-blue-600">
                  {{ $booking->booking_reference ?? 'N/A' }}
                </span>
                @if($booking->reference)
                  <br><span class="text-xs text-gray-500">{{ $booking->reference }}</span>
                @endif
              </td>
              <td class="px-4 py-2">{{ $booking->slot->depot->name }}</td>
              <td class="px-4 py-2">
                {{ \Carbon\Carbon::parse($booking->slot->start_at)->format('d-M H:i') }} →
                {{ \Carbon\Carbon::parse($booking->slot->end_at)->format('d-M H:i') }}
              </td>
              <td class="px-4 py-2">{{ optional($booking->bookingType)->name ?? '-' }}</td>
              <td class="px-4 py-2">
                @if($booking->vehicle_registration)
                  🚛 {{ $booking->vehicle_registration }}<br>
                @endif
                @if($booking->container_number)
                  📦 {{ $booking->container_number }}<br>
                @endif
                @if($booking->driver_name)
                  👤 {{ $booking->driver_name }}
                @endif
              </td>
              <td class="px-4 py-2">
                <div class="grid grid-cols-2 gap-2 text-xs">
                  <div>
                    <strong>Expected:</strong><br>
                    {{ $booking->expected_cases ?? 0 }} cases<br>
                    {{ $booking->expected_pallets ?? 0 }} pallets
                  </div>
                  <div>
                    <strong>Actual:</strong><br>
                    <span class="{{ $booking->actual_cases ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                      {{ $booking->actual_cases ?? '-' }} cases
                    </span><br>
                    <span class="{{ $booking->actual_pallets ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                      {{ $booking->actual_pallets ?? '-' }} pallets
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-4 py-2">
                @if($booking->arrived_at)
                  ✅ Arrived: {{ $booking->arrived_at->format('H:i') }}<br>
                @endif
                @if($booking->departed_at)
                  🕒 Departed: {{ $booking->departed_at->format('H:i') }}<br>
                  ⏱ {{ $booking->arrived_at?->diffInMinutes($booking->departed_at) }} mins<br>
                @endif
                @if($booking->status)
                  <span class="inline-block px-2 py-1 mt-1 rounded text-white text-xs font-semibold
                    {{ $booking->status === 'early' ? 'bg-blue-500' : ($booking->status === 'on time' ? 'bg-green-500' : 'bg-red-600') }}">
                    {{ ucfirst($booking->status) }}
                  </span>
                @endif
              </td>
              <td class="px-4 py-2">
                @if(!$booking->arrived_at)
                  <a href="{{ route('customer.bookings.edit', $booking) }}"
                     class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                @else
                  <span class="text-xs text-gray-500">Locked</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        {{ $bookings->links() }}
      </div>
    @endif
  </div>
</x-app-layout>
