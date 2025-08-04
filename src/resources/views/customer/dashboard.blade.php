<x-app-layout>
    <div class="py-6 max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}</h1>

        <div class="bg-white rounded shadow p-4">
            <h2 class="text-lg font-semibold mb-2">Your Company</h2>
            <p>{{ $customer->name }}</p>
        </div>

        <div class="mt-6">
            <a href="{{ route('customer.bookings.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
               📦 Book a Slot
            </a>
        </div>
    </div>
</x-app-layout>
