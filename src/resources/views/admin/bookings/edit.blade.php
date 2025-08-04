{{-- resources/views/admin/bookings/edit.blade.php --}}
<x-app-layout>
  @include('layouts.admin-nav')
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Edit Booking #{{ $booking->id }}</h2>
  </x-slot>

  <div class="py-6 max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
      @csrf
      @method('PATCH')
      @include('admin.bookings._form')
      <div class="mt-6 flex space-x-3">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
          Update Booking
        </button>
        <a href="{{ route('admin.bookings.index') }}"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded">
           Cancel
        </a>
      </div>
    </form>
    <hr class="my-6"/>
    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST"
          onsubmit="return confirm('Delete this booking?');">
      @csrf
      @method('DELETE')
      <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
        Delete Booking
      </button>
    </form>
  </div>
</x-app-layout>
