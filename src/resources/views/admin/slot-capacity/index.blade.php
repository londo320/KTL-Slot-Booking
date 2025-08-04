<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Edit Slot Capacities</h2>
  </x-slot>

  <div class="max-w-6xl mx-auto py-6">
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.slot-capacity.update') }}">
      @csrf

      @foreach($depots as $depot)
        <h3 class="text-lg font-semibold mt-6">{{ $depot->name }}</h3>

        <table class="w-full text-sm mb-4 border">
          <thead>
            <tr class="bg-gray-100">
              <th class="p-2 border">Start Time</th>
              <th class="p-2 border">End Time</th>
              <th class="p-2 border">Current Capacity</th>
            </tr>
          </thead>
          <tbody>
            @foreach($depot->slots as $slot)
              <tr>
                <td class="p-2 border">{{ $slot->start_at->format('Y-m-d H:i') }}</td>
                <td class="p-2 border">{{ $slot->end_at->format('H:i') }}</td>
                <td class="p-2 border">
                  <input type="number"
                         name="capacities[{{ $slot->id }}]"
                         value="{{ old("capacities.{$slot->id}", $slot->capacity) }}"
                         class="w-16 border rounded p-1"
                         min="1" max="10">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endforeach

      <div class="mt-4">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</x-app-layout>
