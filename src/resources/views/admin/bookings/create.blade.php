{{-- resources/views/admin/bookings/create.blade.php --}}
<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Create Booking Paul</h2>
      <a href="{{ route('admin.bookings.index') }}"
         class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
        ← Back to List
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <form action="{{ route('admin.bookings.store') }}" method="POST">
      @csrf

      {{-- Include all the input fields --}}
      @include('admin.bookings._form')

      {{-- Form actions --}}
      <div class="mt-6 flex space-x-3">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Save Booking
        </button>
        <a href="{{ route('admin.bookings.index') }}"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
          Cancel
        </a>
      </div>
    </form>
  </div>
</x-app-layout>
