<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">Create Slot</h2>
  </x-slot>

  <div class="max-w-xl mx-auto py-6">
    <form method="POST" action="{{ route('admin.slots.store') }}">
      @csrf

      <div class="mb-4">
        <label class="block text-sm">Depot</label>
        <select name="depot_id" class="w-full border rounded p-2">
          @foreach($depots as $depot)
            <option value="{{ $depot->id }}">{{ $depot->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm">Booking Type</label>
        <select name="booking_type_id" class="w-full border rounded p-2">
          @foreach($types as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm">Start At</label>
        <input type="datetime-local" name="start_at" class="w-full border rounded p-2" required>
      </div>

      <div class="mb-4">
        <label class="block text-sm">End At</label>
        <input type="datetime-local" name="end_at" class="w-full border rounded p-2" required>
      </div>

      <div class="mb-4">
        <label class="inline-flex items-center">
          <input type="checkbox" name="is_blocked" class="mr-2">
          Block this slot
        </label>
      </div>

      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create Slot
      </button>
    </form>
  </div>
</x-app-layout>
