<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <h2 class="font-semibold text-xl">Edit Booking</h2>
  </x-slot>

  <div class="py-6 max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <form action="{{ route('customer.bookings.update', $booking) }}" method="POST">
      @csrf
      @method('PUT')
      @include('customer.bookings._form_readonly')
      <div class="mt-4">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Update Booking
        </button>
        <a href="{{ route('customer.bookings.index') }}"
           class="ml-2 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
          Cancel
        </a>
      </div>
    </form>
  </div>
</x-app-layout>