<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <h2 class="text-xl font-semibold">📊 Admin Dashboard</h2>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto space-y-8">
    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-4 mb-4">
      <div>
        <label for="date" class="text-sm font-medium">Select Date:</label>
        <input type="date" name="date" id="date" class="border rounded px-2 py-1"
               value="{{ $date->format('Y-m-d') }}">
      </div>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Apply Filter
      </button>
      <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:underline">Reset</a>
    </form>

    @foreach($depots as $depot)
      <div class="bg-white shadow rounded p-6">
        <div class="flex justify-between items-center mb-2">
          <div>
            <h3 class="text-lg font-bold">{{ $depot->name }}</h3>
            <p class="text-sm text-gray-500">{{ $depot->location ?? '—' }}</p>
          </div>
          <div class="text-sm text-gray-500">
            {{ $depot->summary['date']->format('D, d M Y') }}
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-2 gap-4 text-sm">
          {{-- Summary --}}
          <div class="space-y-1">
            <p><strong>Total Slots:</strong> {{ $depot->summary['total'] }}</p>
            <p><strong>Used:</strong> {{ $depot->summary['used'] }}</p>
            <p><strong>Available:</strong> {{ $depot->summary['available'] }}</p>
          </div>

          {{-- Status --}}
          <div class="space-y-1">
            <p><strong>Arrived:</strong> {{ $depot->summary['arrived'] }}</p>
            <p><strong>In Progress:</strong> {{ $depot->summary['in_progress'] }}</p>
            <p><strong>Finished:</strong> {{ $depot->summary['finished'] }}</p>
            <p><strong>Late:</strong> <span class="text-red-600">{{ $depot->summary['late'] }}</span></p>
          </div>
        </div>

        {{-- Booking Types --}}
        <div class="mt-4">
          <p class="font-semibold mb-1">📦 Bookings by Type</p>
          <ul class="text-sm space-y-1">
            @forelse($bookingTypes as $type)
             @php
            $data = $depot->summary['types'][$type->id] ?? ['used' => 0, 'capacity' => 0];
            @endphp
        <li>{{ $type->name }} — {{ $data['used'] ?? 0 }} / {{ $data['capacity'] ?? 0 }}</li>
            @empty
              <li>No booking types found.</li>
            @endforelse

            {{-- Unassigned --}}
@php
  $unassigned = $depot->summary['types'][null] ?? [];
  $used = $unassigned['used'] ?? 0;
  $capacity = $unassigned['capacity'] ?? 0;
@endphp

@if($used > 0 || $capacity > 0)
  <li class="text-red-600">Unassigned — {{ $used }} / {{ $capacity }}</li>
@endif
          </ul>
        </div>
      </div>
    @endforeach
  </div>
</x-app-layout>
