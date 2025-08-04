<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <h2 class="text-xl font-semibold">✏️ Edit Booking Type</h2>
  </x-slot>

  <div class="py-6 max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded shadow">
      <form method="POST" action="{{ route('admin.booking-types.update', $bookingType) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Name</label>
          <input type="text" name="name" class="w-full border rounded p-2"
                 value="{{ old('name', $bookingType->name) }}">
          @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex justify-end">
          <a href="{{ route('admin.booking-types.index') }}"
             class="text-sm text-gray-600 hover:underline mr-4">Cancel</a>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
