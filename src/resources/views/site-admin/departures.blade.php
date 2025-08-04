<x-site-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🕒 Departure Management
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6 bg-orange-50 border-l-4 border-orange-400 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-orange-700">
                        <strong>Departure Management</strong> - Shows all vehicles currently on site that need to be marked as departed.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Booking Reference
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer & Vehicle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location & Gate
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Arrival Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration On Site
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($departures as $booking)
                            <tr class="hover:bg-gray-50 bg-blue-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-mono text-sm font-semibold text-blue-600">
                                        {{ $booking->booking_reference }}
                                    </div>
                                    @if($booking->reference)
                                        <div class="text-xs text-gray-500">{{ $booking->reference }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $booking->customer->name ?? 'N/A' }}
                                    </div>
                                    @if($booking->vehicle_registration)
                                        <div class="text-xs text-gray-600">🚛 {{ $booking->vehicle_registration }}</div>
                                    @endif
                                    @if($booking->container_number)
                                        <div class="text-xs text-gray-600">📦 {{ $booking->container_number }}</div>
                                    @endif
                                    @if($booking->driver_name)
                                        <div class="text-xs text-gray-600">👤 {{ $booking->driver_name }}</div>
                                    @endif
                                    @if($booking->driver_phone)
                                        <div class="text-xs text-blue-600">📞 {{ $booking->driver_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        📍 {{ $booking->slot->depot->name }}
                                    </div>
                                    @if($booking->gate_number || $booking->bay_number)
                                        <div class="text-sm text-green-600">
                                            @if($booking->gate_number)🚪 Gate {{ $booking->gate_number }} @endif
                                            @if($booking->bay_number)🏗️ Bay {{ $booking->bay_number }}@endif
                                        </div>
                                    @endif
                                    @if($booking->special_instructions)
                                        <div class="text-xs text-orange-600 mt-1">
                                            ⚠️ {{ Str::limit($booking->special_instructions, 30) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ✅ {{ $booking->arrived_at->format('d-M H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Slot: {{ $booking->slot->start_at->format('d-M H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-600">
                                        {{ $booking->arrived_at->diffForHumans() }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $booking->arrived_at->diffInMinutes(now()) }} minutes
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" action="{{ route('site.bookings.departure', $booking) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                            Mark Departed
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No vehicles currently on site.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($departures->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $departures->links() }}
                </div>
            @endif
        </div>

        @if($departures->count() > 0)
            <div class="mt-6 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📊 On-Site Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded">
                        <div class="text-2xl font-bold text-blue-600">{{ $departures->count() }}</div>
                        <div class="text-sm text-gray-600">Vehicles On Site</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $departures->avg(function($booking) { return $booking->arrived_at->diffInMinutes(now()); }) ? round($departures->avg(function($booking) { return $booking->arrived_at->diffInMinutes(now()); })) : 0 }}
                        </div>
                        <div class="text-sm text-gray-600">Avg. Minutes On Site</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded">
                        <div class="text-2xl font-bold text-orange-600">
                            {{ $departures->where('arrived_at', '<', now()->subHours(2))->count() }}
                        </div>
                        <div class="text-sm text-gray-600">On Site > 2 Hours</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-site-admin-layout>