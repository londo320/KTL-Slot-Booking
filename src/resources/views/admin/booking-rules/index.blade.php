<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Depot Booking Rules</h2>
  </x-slot>

  <div class="max-w-6xl mx-auto py-6">
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.booking-rules.store') }}">
      @csrf

      <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="p-2 border">Depot</th>
            @foreach($types as $type)
              <th class="p-2 border">{{ $type->name }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($depots as $depot)
            <tr>
              <td class="p-2 border font-semibold">{{ $depot->name }}</td>
              @foreach($types as $type)
                @php
                  $current = $depot->bookingTypes->firstWhere('id', $type->id);
                @endphp
                <td class="p-2 border">
                  <input type="number"
                         name="rules[{{ $depot->id }}-{{ $type->id }}][duration]"
                         value="{{ old("rules.{$depot->id}-{$type->id}.duration", $current?->pivot->duration_minutes) }}"
                         class="w-20 border rounded p-1"
                         min="1">
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-6">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Save Rules
        </button>
      </div>
    </form>
  </div>
</x-app-layout>
